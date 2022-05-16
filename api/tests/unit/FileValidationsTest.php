<?php

namespace App\Tests\unit;

use App\Tests\util\CustomTestCase;
use App\Tests\util\FileTestUtil;
use App\Util\Error;
use App\Util\FileValidations;
use Exception;

class FileValidationsTest extends CustomTestCase
{

    /**
     * @test
     * @param array $actual
     * @param array $expected
     * @throws Exception
     * @dataProvider getFileProvider
     */
    public function testGetFileFromUpload(array $actual, array $expected): void
    {
        $dir = dirname(__DIR__) . '/uploadTest';

        if (!empty($expected['exception'])) {
            ['code' => $code, 'message' => $msg] = $expected['exception'];
            $this->expectExceptionCode($code);
            $this->expectExceptionMessage($msg);
        }

        // create folder and files
        if (array_key_exists('files', $actual)) {
            if (!is_dir($dir)) mkdir($dir);
            foreach ($actual['files'] as $key => $file) {
                file_put_contents("$dir/$file-$key.${actual['extension']}", '');
            }
        }

        $result = FileValidations::getFileFromUpload($dir);

        // delete folder and files
        foreach (array_diff(scandir($dir, 1), array('.', '..')) as $file) {
            unlink("$dir/$file");
        }
        rmdir($dir);

        $this->assertEquals($expected['file'], $result);
    }

    public function getFileProvider(): array
    {
        return [
            'should-return-error-empty-folder' => [
                'actual' => [],
                'expected' => [
                    'exception' => Error::EMPTY_FOLDER
                ]
            ],
            'should-return-error-empty-folder-with-file' => [
                'actual' => [
                    'files' => ['file'],
                    'extension' => 'txt'
                ],
                'expected' => [
                    'exception' => Error::EMPTY_FOLDER
                ]
            ],
            'should-return-success-one-file' => [
                'actual' => [
                    'files' => ['file'],
                    'extension' => 'xls'
                ],
                'expected' => [
                    'exception' => null,
                    'file' => 'file-0.xls'
                ]
            ],
            'should-return-success-multiple-files' => [
                'actual' => [
                    'files' => ['file', 'file'],
                    'extension' => 'xls'
                ],
                'expected' => [
                    'exception' => null,
                    'file' => 'file-1.xls'
                ]
            ]
        ];
    }

    /**
     * @test
     * @param array $actual
     * @param array $expected
     * @throws Exception
     * @dataProvider sheetProvider
     */
    public function testCleanSheetContent(array $actual, array $expected): void
    {
        if (array_key_exists('code', $expected)) {
            ['code' => $code, 'message' => $msg] = $expected;
            $this->expectExceptionCode($code);
            $this->expectExceptionMessage($msg);
        }

        if (empty($actual['file'] ?? null)) {
            throw Error::message(Error::FILE_NOT_FOUND);
        }

        $sheet = FileTestUtil::createXlsxTestFile($actual['sheet'] ?? []);
        $result = FileValidations::cleanSheetContent($sheet);
        $this->assertEquals($expected, $result);
    }

    /**
     * @return array
     */
    public function sheetProvider(): array
    {
        return FileTestUtil::providerSheetData();
    }
}
