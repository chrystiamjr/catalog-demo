<?php

namespace App\Util;

use App\Model\LocationEntry;
use Exception;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class FileValidations
{
    const headers = ['Model', 'RAM', 'HDD', 'Location', 'Price'];
    const validExtensions = array('xlsx', 'xls');

    /**
     * @param string|null $path
     * @return string
     * @throws Exception
     */
    public static function getFileFromUpload(?string $path = null): string
    {
        $dir = $path ?? __DIR__ . '/../../uploads';
        if (!is_dir($dir)) mkdir($dir);

        $exclude = array('..', '.');
        $filesInDir = array_diff(scandir($dir, 1), $exclude);
        $filteredFiles = array_filter($filesInDir, function ($file) {
            return strpos($file, '.xls') > 0;
        });

        if (sizeof($filteredFiles) == 0) throw Error::message(Error::EMPTY_FOLDER);

        return $filteredFiles[0];
    }

    /**
     * @param Spreadsheet $sheet
     * @return array
     * @throws Exception
     */
    public static function cleanSheetContent(Spreadsheet $sheet): array
    {
        $result = [];
        try {
            for ($tab = 0; $tab < $sheet->getSheetCount(); $tab++) {
                $content = $sheet->getSheet($tab)->toArray();

                if ($tab == 0) {
                    if (self::mismatchHeaders($content)) {
                        throw Error::message(Error::INVALID_FORMAT);
                    }

                    if (sizeof($content) == 1 || self::isRowEmpty($content)) {
                        throw Error::message(Error::EMPTY_FILE);
                    }
                }

                if (sizeof($content) == 1 || self::mismatchHeaders($content) || self::isRowEmpty($content)) {
                    continue;
                }

                // Removing headers
                unset($content[0]);
                $content = array_values($content);

                // Sorting by model name
                array_multisort(array_column($content, 0), SORT_ASC, $content);

                // Filtering for not empty data
                $cleanContent = array_filter($content, function ($row) {
                    return !self::isRowEmpty($row, null);
                });

                foreach ($cleanContent as $data) {
                    [$model, $ram, $drive, $location, $price] = $data;
                    ['location' => $loc, 'code' => $code] = self::formatLocation($location);

                    $product = array_merge(
                        self::formatRam($ram),
                        self::formatDrive($drive),
                        ['price' => $price]
                    );

                    $locationEntry = new LocationEntry();
                    $locationEntry->location = "$loc#$code";
                    $locationEntry->products[] = $product;

                    if (empty($result[$model])) {
                        $result[$model][] = $locationEntry;
                        continue;
                    }

                    $keysForProduct = self::searchThroughArray($result, $model, [
                        'location' => $locationEntry->location,
                        'products.ramSize' => $product['ramSize'],
                        'products.ramType' => $product['ramType'],
                        'products.driveQuantity' => $product['driveQuantity'],
                        'products.driveSize' => $product['driveSize'],
                        'products.driveType' => $product['driveType'],
                    ]);

                    if (!empty($keysForProduct) && !empty($keysForProduct[0]) && is_array($keysForProduct[0])) {
                        $key = array_keys($keysForProduct)[0];
                        $value = array_values($keysForProduct[0])[0];

                        $entry = $result[$model][$key];
                        $productInfo = $entry->products[$value];

                        if ($product['price'] >= $productInfo['price']) {
                            $entry->products[$value] = $product;
                            $result[$model][$key] = $entry;
                        }

                        continue;
                    }

                    $keyForLocation = self::searchThroughArray($result, $model, [
                        'location' => $locationEntry->location,
                    ]);

                    if (!empty($keyForLocation)) {
                        $key = array_keys($keyForLocation)[0];
                        $entry = $result[$model][$key];
                        $entry->products[] = $product;

                        $result[$model][$key] = $entry;
                        continue;
                    }

                    $result[$model][] = $locationEntry;
                }
            }

            $index = 0;
            foreach ($result as $key => $item) {
                $result['items'][$index] = [$key => $item];
                unset($result[$key]);
                $index++;
            }

            return $result;
        } catch (\PhpOffice\PhpSpreadsheet\Exception $e) {
            throw Error::message(Error::SPREADSHEET_ERROR, [
                '@CODE@' => $e->getCode()
            ]);
        }
    }

    /**
     * @param array $content
     * @return bool
     */
    protected static function mismatchHeaders(array $content): bool
    {
        foreach (self::headers as $key => $header) {
            if ($content[0][$key] != $header) return true;
        }

        return false;
    }

    /**
     * @param array $sheetData
     * @param int|null $index
     * @return bool
     */
    protected static function isRowEmpty(array $sheetData, ?int $index = 1): bool
    {
        $content = ($index == 1) ? $sheetData[1] : $sheetData;
        foreach (self::headers as $key => $_) {
            if (empty($content[$key])) return true;
        }

        return false;
    }

    /**
     * @param string $location
     * @return array
     */
    protected static function formatLocation(string $location): array
    {
        $splitIndex = strlen($location) - 6;
        return [
            'location' => mb_substr($location, 0, $splitIndex),
            'code' => mb_substr($location, $splitIndex)
        ];
    }

    /**
     * @param string $ram
     * @return array
     */
    protected static function formatRam(string $ram): array
    {
        [$size, $type] = explode('DDR', $ram);
        return [
            'ramSize' => $size,
            'ramType' => 'DDR' . $type
        ];
    }

    /**
     * @param string $drive
     * @return array
     */
    protected static function formatDrive(string $drive): array
    {
        [$quantity, $details] = explode('x', $drive);
        $size = $type = '';

        if (!empty($details)) {
            if (strpos($details, 'GB')) {
                [$size, $type] = explode('GB', $details);
                $size .= 'GB';
            }

            if (strpos($details, 'TB')) {
                [$size, $type] = explode('TB', $details);
                $size .= 'TB';
            }
        }

        return [
            'driveQuantity' => $quantity,
            'driveSize' => $size,
            'driveType' => $type,
        ];
    }

    /**
     * @param array $dataArray
     * @param string $modelToFilter
     * @param array $arrayFilter
     * @return array
     */
    protected static function searchThroughArray(array $dataArray, string $modelToFilter, array $arrayFilter): array
    {
        $propertyKeys = array();
        $arrayKeys = array();

        if (!isset($dataArray[$modelToFilter])) return array();
        if (empty($dataArray[$modelToFilter])) return array();

        $locationItems = json_decode(json_encode($dataArray[$modelToFilter]), true);
        $propertyFilter = array_filter($arrayFilter, function ($item) use ($arrayFilter) {
            return strpos(array_keys($arrayFilter, $item)[0], '.') == 0;
        });

        foreach ($propertyFilter as $column => $value) {
            $found = array_filter($locationItems, function ($item) use ($column, $value) {
                return $item[$column] == $value;
            });
            $propertyKeys = array_merge(array_keys($found), $propertyKeys);
        }

        $childFilters = array_filter($arrayFilter, function ($item) use ($arrayFilter) {
            return strpos(array_keys($arrayFilter, $item)[0], '.');
        });

        foreach ($locationItems as $key => $item) {
            $result = self::validateArrayIf($item, $childFilters);
            if (empty($result)) continue;

            $arrayKeys[$key] = $result;
        }

        if (empty($propertyKeys) && empty($arrayKeys)) return array();
        if (!empty($propertyKeys) && empty($arrayKeys)) return $propertyKeys;
        if (!empty($arrayKeys) && empty($propertyKeys)) return $arrayKeys;
        return array_filter($arrayKeys, function ($arrayKey) use ($propertyKeys) {
            $key = array_keys($arrayKey)[0];
            return in_array($key, $propertyKeys);
        });
    }

    /**
     * @param array $properties
     * @param array $validations
     * @return array
     */
    protected static function validateArrayIf(array $properties, array $validations): array
    {
        $keys = array();

        $columnsToFilter = array_map(function ($item) use ($validations) {
            $key = array_keys($validations, $item)[0];
            [$column] = explode('.', $key);
            return $column;
        }, $validations);

        $columnsToFilter = array_unique($columnsToFilter);
        foreach ($columnsToFilter as $columnFilter) {
            if (!isset($properties[$columnFilter])) continue;

            foreach ($properties[$columnFilter] as $key => $entry) {
                $result = true;

                foreach ($validations as $column => $value) {
                    [$_, $columnEntry] = explode('.', $column);

                    if (!isset($entry[$columnEntry])) continue;
                    if ($entry[$columnEntry] != $value) $result = false;
                }

                if ($result) $keys[] = $key;
            }
        }

        return $keys;
    }
}