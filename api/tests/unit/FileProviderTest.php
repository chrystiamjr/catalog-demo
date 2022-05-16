<?php

namespace App\Tests\unit;

use App\Provider\File\FileProvider;
use App\Tests\util\CustomTestCase;
use App\Tests\util\FileTestUtil;
use App\Util\Error;
use Exception;

class FileProviderTest extends CustomTestCase
{
    /**
     * @test
     * @param array $actual
     * @param array $expected
     * @throws Exception
     * @dataProvider sheetProvider
     */
    public function testReadFile(array $actual, array $expected): void
    {
        if (array_key_exists('code', $expected)) {
            ['code' => $code, 'message' => $msg] = $expected;
            $this->expectExceptionCode($code);
            $this->expectExceptionMessage($msg);
        }

        $service = new FileProvider();
        $result = $service->readFile(
            $actual['file'] ?? null,
            FileTestUtil::createXlsxTestFile($actual['sheet'] ?? [])
        );
        $this->assertEquals($expected, $result);
    }

    /**
     * @return array
     */
    public function sheetProvider(): array
    {
        return FileTestUtil::providerSheetData();
    }

    /**
     * @test
     * @param array $actual
     * @param array|null $expected
     * @return void
     * @throws Exception
     * @dataProvider fileProvider
     */
    public function testUploadFile(array $actual, ?array $expected): void
    {
        if (!empty($expected) && array_key_exists('code', $expected)) {
            ['code' => $code, 'message' => $msg] = $expected;
            $this->expectExceptionCode($code);
            $this->expectExceptionMessage($msg);
        }

        $dir = dirname(__DIR__) . '/uploadTest';
        $service = new FileProvider();
        $return = $service->uploadFile(
            $actual['file'] ?? [],
            $actual['fileName'] ?? '',
            $dir
        );

        // delete folder and files
        foreach (array_diff(scandir($dir, 1), array('.', '..')) as $file) {
            unlink("$dir/$file");
        }
        rmdir($dir);

        $this->assertIsBool($return);
        $this->assertTrue($return);
    }

    /**
     * @return array
     */
    public function fileProvider(): array
    {
        return [
            'should-return-error-file-not-found' => [
                'actual' => [
                    'file' => null,
                    'fileName' => null
                ],
                'expected' => Error::EMPTY_FILE
            ],
            'should-return-error-invalid-format' => [
                'actual' => [
                    'file' => ['test'],
                    'fileName' => 'avatar.jpg',
                ],
                'expected' => Error::INVALID_FORMAT
            ],
            'should-return-success' => [
                'actual' => [
                    'file' => ['test'],
                    'fileName' => 'sheet.xlsx',
                ],
                'expected' => null
            ],
        ];
    }
}