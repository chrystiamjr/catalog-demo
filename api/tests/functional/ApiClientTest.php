<?php

namespace App\Tests\functional;

use App\Tests\util\CustomWebTestCase;
use App\Util\Error;
use App\Util\RedisClient;
use Exception;

class ApiClientTest extends CustomWebTestCase
{
    /**
     * @test
     * @return void
     */
    public function readFileSuccessTest()
    {
        $client = $this->createClient();
        $client->request('GET', '/files/read');

        $this->assertStatusCode(200, $client);
        $this->assertTrue(str_contains($client->getResponse()->getContent(), 'items'));
    }

    /**
     * @test
     * @return void
     * @throws Exception
     */
    public function uploadFileSuccessTest()
    {
        $redis = RedisClient::getAdapter();
        $dir = dirname(__DIR__) . '/../uploads';

        $file = json_decode(file_get_contents(__DIR__ . '/../util/sample_binary.txt', true));

        $redisData = self::getRedisData();

        $client = $this->createClient();
        $client->request('POST', '/files/upload', [], [], [], json_encode([
            'file' => $file,
            'fileName' => 'test.xlsx'
        ]));

        $content = $client->getResponse()->getContent();
        $this->assertStatusCode(200, $client);
        $this->assertTrue(str_contains($content, 'items'));

        // remove testFile
        unlink("$dir/{$redis->get('actual_file')}");
        self::setRedisData($redisData);
    }

    /**
     * @return array
     * @throws Exception
     */
    protected static function getRedisData(): array
    {
        $redis = RedisClient::getAdapter();
        $lastFileName = $redis->get('original_fileName');
        $lastActualFile = $redis->get('actual_file');
        $lastProducts = $redis->get('products');

        return [
            'fileName' => $lastFileName,
            'actualFile' => $lastActualFile,
            'products' => $lastProducts
        ];
    }

    /**
     * @param array $data
     * @return void
     * @throws Exception
     */
    protected static function setRedisData(array $data): void
    {
        $redis = RedisClient::getAdapter();
        $redis->set('original_fileName', $data['fileName']);
        $redis->set('actual_file', $data['actualFile']);
        $redis->set('products', $data['products']);
    }

    /**
     * @test
     * @return void
     * @dataProvider uploadFileErrorsProvider
     * @throws Exception
     */
    public function uploadFileErrorsTest(array $actual, array $expected)
    {
        $redis = RedisClient::getAdapter();
        $lastFileName = '';
        if (!empty($actual['redis'])) {
            $lastFileName = $redis->get('original_fileName');
            $redis->set('original_fileName', $actual['redis']);
        }

        $client = $this->createClient();
        $client->request('POST', '/files/upload', [], [], [], json_encode(
            $actual['payload']
        ));

        $content = $client->getResponse()->getContent();
        $this->assertStatusCode($expected['status'], $client);
        $this->assertTrue(str_contains($content, '"code":' . $expected['error']['code']));
        $this->assertTrue(str_contains($content, $expected['error']['message']));

        if (!empty($actual['redis'])) {
            $redis->set('original_fileName', $lastFileName);
        }
    }

    /**
     * @return array
     */
    public function uploadFileErrorsProvider(): array
    {
        return [
            'return-empty-payload' => [
                'actual' => [
                    'payload' => [],
                ],
                'expected' => [
                    'status' => 400,
                    'error' => Error::EMPTY_PAYLOAD
                ]
            ],
            'return-invalid-payload-all' => [
                'actual' => [
                    'payload' => ['test'],
                ],
                'expected' => [
                    'status' => 400,
                    'error' => Error::INVALID_PAYLOAD
                ]
            ],
            'return-invalid-payload-with-file' => [
                'actual' => [
                    'payload' => ['file' => []],
                ],
                'expected' => [
                    'status' => 400,
                    'error' => Error::INVALID_PAYLOAD
                ]
            ],
            'return-invalid-payload-with-fileName' => [
                'actual' => [
                    'payload' => [
                        'file' => [],
                        'fileName' => 'test.xlsx'
                    ],
                ],
                'expected' => [
                    'status' => 400,
                    'error' => Error::INVALID_PAYLOAD
                ]
            ],
            'return-file-already-uploaded' => [
                'actual' => [
                    'redis' => 'test.xlsx',
                    'payload' => [
                        'file' => [0, 1, 2, 3],
                        'fileName' => 'test.xlsx'
                    ],
                ],
                'expected' => [
                    'status' => 400,
                    'error' => Error::FILE_ALREADY_UPLOADED
                ]
            ],
        ];
    }

    /**
     * @test
     * @return void
     * @throws Exception
     * @dataProvider filterProductsProvider
     */
    public function filterProductsTest(array $actual, array $expected)
    {
        $client = $this->createClient();
        $client->request('POST', '/products/filter', [], [], [], json_encode(
            $actual['payload']
        ));

        $content = $client->getResponse()->getContent();
        $this->assertStatusCode($expected['status'], $client);
        foreach ($expected['contains'] as $contain) {
            $this->assertTrue(str_contains($content, $contain));
        }

        foreach ($expected['notContains'] as $notContain) {
            $this->assertFalse(str_contains($content, $notContain));
        }
    }

    /**
     * @return array
     */
    public function filterProductsProvider(): array
    {
        return [
            'return-all-products' => [
                'actual' => [
                    'payload' => [],
                ],
                'expected' => [
                    'status' => 200,
                    'contains' => [
                        'Amsterdam',
                        'Hong Kong',
                        'Singapore'
                    ],
                    'notContains' => []
                ]
            ],
            'return-only-amsterdam' => [
                'actual' => [
                    'payload' => [
                        'filters' => [
                            '0' => ['location' => 'AMS-01']
                        ]
                    ],
                ],
                'expected' => [
                    'status' => 200,
                    'contains' => [
                        'Amsterdam'
                    ],
                    'notContains' => [
                        'Hong Kong',
                        'Singapore'
                    ]
                ]
            ],
            'return-only-sata' => [
                'actual' => [
                    'payload' => [
                        'filters' => [
                            '0' => ['driveType' => 'SATA']
                        ]
                    ],
                ],
                'expected' => [
                    'status' => 200,
                    'contains' => [
                        'SATA2'
                    ],
                    'notContains' => [
                        'SAS',
                        'SSD'
                    ]
                ]
            ],
            'return-size-1TB' => [
                'actual' => [
                    'payload' => [
                        'filters' => [
                            '0' => ['driveSize' => '1TB']
                        ]
                    ],
                ],
                'expected' => [
                    'status' => 200,
                    'contains' => [
                        '1TB'
                    ],
                    'notContains' => [
                        '240GB',
                        '500GB',
                        '2TB'
                    ]
                ]
            ],
            'return-ram-size-single' => [
                'actual' => [
                    'payload' => [
                        'filters' => [
                            '0' => ['ramSize' => ['16GB']]
                        ]
                    ],
                ],
                'expected' => [
                    'status' => 200,
                    'contains' => [
                        '16GB'
                    ],
                    'notContains' => [
                        '4GB',
                        '8GB',
                    ]
                ]
            ],
            'return-ram-size-multi' => [
                'actual' => [
                    'payload' => [
                        'filters' => [
                            '0' => ['ramSize' => ['8GB', '16GB']]
                        ]
                    ],
                ],
                'expected' => [
                    'status' => 200,
                    'contains' => [
                        '16GB',
                        '8GB'
                    ],
                    'notContains' => [
                        '4GB'
                    ]
                ]
            ]
        ];
    }
}