<?php

namespace App\Provider\Product;

use App\Util\Error;
use Exception;

class ProductProvider implements ProductProviderInterface
{
    /**
     * @param array|null $products
     * @param array|null $filterOptions
     * @return array
     * @throws Exception
     */
    public function filterProducts(
        ?array $products,
        ?array $filterOptions
    ): array
    {
        if (empty($products) || sizeof($products) == 0) {
            throw Error::message(Error::EMPTY_PRODUCT_LIST);
        }

        ksort($filterOptions, SORT_ASC);
        foreach ($filterOptions as $filterData) {
            $filteringColumn = key($filterData);
            $filteringValue = array_values($filterData)[0];

            if (empty($products)) break;

            if ($filteringColumn == 'location') {
                $products = self::locationFilter($products['items'], $filteringValue);
                continue;
            }

            $products = self::productInfoFilter($products['items'], $filteringColumn, $filteringValue);
        }

        if (empty($products)) {
            $products['items'] = [];
            return $products;
        }

        $items = $products['items'];
        $products = [];
        foreach ($items as $item) {
            $products['items'][] = $item;
        }

        return $products;
    }

    /**
     * @param array $items
     * @param String $filteringValue
     * @return array
     */
    protected static function locationFilter(array $items, string $filteringValue): array
    {
        $filteredArray = [];
        foreach ($items as $key => $models) {
            foreach ($models as $modelKey => $model) {
                $filtered = array_filter($model, function ($productLocation) use ($filteringValue) {
                    return str_contains($productLocation->location, $filteringValue);
                });

                if (!empty($filtered)) {
                    $filteredArray[$key][$modelKey] = $filtered;
                }
            }
        }

        $items = [];
        foreach ($filteredArray as $key => $location) {
            $modelKey = key($location);
            $info = array_values($location)[0];
            $info = array_values($info);
            $items['items'][$key][$modelKey] = $info;
        }
        return $items;
    }

    /**
     * @param array $items
     * @param String $filteringColumn
     * @param String|array $filteringValue
     * @return array
     */
    protected static function productInfoFilter(array $items, string $filteringColumn, $filteringValue): array
    {
        $filteredArray = [];
        foreach ($items as $key => $models) {
            foreach ($models as $modelKey => $model) {
                foreach ($model as $itemKey => $item) {
                    $filtered = array_filter(
                        is_array($item) ? $item['products'] : $item->products,
                        function ($productInfo) use ($filteringColumn, $filteringValue) {
                            $info = (array)$productInfo;
                            return is_array($filteringValue) ?
                                in_array($info[$filteringColumn], $filteringValue) :
                                str_contains($info[$filteringColumn], $filteringValue);
                        });

                    if (!empty($filtered)) {
                        $location = is_array($item) ? $item['location'] : $item->location;
                        $data = [
                            'location' => $location,
                            'products' => $filtered
                        ];
                        $filteredArray[$key][$modelKey][] = $data;
                    }
                }
            }
        }

        if (empty($filteredArray)) return $filteredArray;

        $items = [];
        foreach ($filteredArray as $key => $location) {
            $modelKey = key($location);
            $modelInfos = array_values($location)[0];

            foreach ($modelInfos as $infoKey => $info) {
                $modelInfos[$infoKey]['location'] = $info['location'];
                $modelInfos[$infoKey]['products'] = array_values($info['products']);
            }

            $items['items'][$key][$modelKey] = $modelInfos;
        }
        return $items;
    }
}