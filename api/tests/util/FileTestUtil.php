<?php

namespace App\Tests\util;

use App\Model\LocationEntry;
use App\Util\Error;
use App\Util\FileValidations;
use Exception;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class FileTestUtil
{
    /**
     * @param $sheetArr
     * @return Spreadsheet
     * @throws Exception
     */
    public static function createXlsxTestFile($sheetArr): Spreadsheet
    {
        try {
            $sheet = new Spreadsheet();
            $sheet->createSheet();

            foreach ($sheetArr as $tab => $content) {
                $sheet->setActiveSheetIndex($tab);
                foreach ($content as $row => $columns) {
                    foreach ($columns as $column => $value) {
                        $sheet->getActiveSheet()->setCellValueByColumnAndRow($column + 1, $row + 1, $value);
                    }
                }
            }

            return $sheet;
        } catch (\PhpOffice\PhpSpreadsheet\Exception $e) {
            dump($e->getMessage());
            throw Error::message(Error::SPREADSHEET_ERROR, [['@CODE@', $e->getCode()]]);
        }
    }

    /**
     * @return array
     */
    public static function providerSheetData(): array
    {
        return [
            'should-return-error-file-not-found' => [
                'actual' => [],
                'expected' => Error::FILE_NOT_FOUND
            ],
            'should-return-error-no-header' => [
                'actual' => [
                    'file' => 'mock',
                    'sheet' => [
                        0 => []
                    ]
                ],
                'expected' => Error::INVALID_FORMAT
            ],
            'should-return-error-no-content' => [
                'actual' => [
                    'file' => 'mock',
                    'sheet' => [
                        0 => [
                            0 => FileValidations::headers,
                            1 => []
                        ]
                    ]
                ],
                'expected' => Error::EMPTY_FILE
            ],
            'should-return-success' => [
                'actual' => [
                    'file' => 'mock',
                    'sheet' => [
                        0 => [
                            0 => FileValidations::headers,
                            1 => [
                                'Dell R210-IIIntel G530', '4GBDDR3', '2x500GBSATA2', 'AmsterdamAMS-01', '€35.99'
                            ]
                        ]
                    ]
                ],
                'expected' => [
                    'items' => [
                        [
                            'Dell R210-IIIntel G530' => [
                                new LocationEntry(
                                    "Amsterdam#AMS-01",
                                    [
                                        [
                                            "ramSize" => "4GB",
                                            "ramType" => "DDR3",
                                            "driveQuantity" => "2",
                                            "driveSize" => "500GB",
                                            "driveType" => "SATA2",
                                            "price" => '€35.99',
                                        ]
                                    ]
                                )
                            ]
                        ]
                    ]
                ]
            ],
            'should-return-success-with-tabs' => [
                'actual' => [
                    'file' => 'mock',
                    'sheet' => [
                        0 => [
                            0 => FileValidations::headers,
                            1 => [
                                'Dell R210-IIIntel G530', '4GBDDR3', '2x500GBSATA2', 'AmsterdamAMS-01', '€35.99'
                            ],
                            2 => [
                                'Dell R210-IIIntel G530', '4GBDDR3', '2x500GBSATA2', 'AmsterdamAMS-01', '€35.99'
                            ]
                        ],
                        1 => [
                            0 => FileValidations::headers,
                            1 => []
                        ]
                    ]
                ],
                'expected' => [
                    'items' => [
                        [
                            'Dell R210-IIIntel G530' => [
                                new LocationEntry(
                                    "Amsterdam#AMS-01",
                                    [
                                        [
                                            "ramSize" => "4GB",
                                            "ramType" => "DDR3",
                                            "driveQuantity" => "2",
                                            "driveSize" => "500GB",
                                            "driveType" => "SATA2",
                                            "price" => '€35.99',
                                        ]
                                    ]
                                )
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }
}