<?php

namespace App\Provider\File;

use App\Util\Error;
use App\Util\FileValidations;
use DateTime;
use Exception;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class FileProvider implements FileProviderInterface
{
    /**
     * @param string|null $fileName
     * @param Spreadsheet|null $mockedSheet
     * @return array
     * @throws Exception
     */
    public function readFile(
        ?string      $fileName,
        ?Spreadsheet $mockedSheet = null
    ): array
    {
        try {
            if (empty($fileName)) {
                throw Error::message(Error::FILE_NOT_FOUND);
            }

            $reader = new Xlsx();
            $sheet = $mockedSheet ?? $reader->load($fileName);
            return FileValidations::cleanSheetContent($sheet);
        } catch (\PhpOffice\PhpSpreadsheet\Exception $e) {
            throw Error::message(Error::SPREADSHEET_ERROR, [
                '@CODE@' => $e->getCode()
            ]);
        } catch (Exception $e) {
            throw Error::rethrow($e);
        }
    }

    /**
     * @param array $fileBytes
     * @param string $fileName
     * @param string|null $path
     * @return bool
     * @throws Exception
     */
    public function uploadFile(
        array   $fileBytes,
        string  $fileName,
        ?string $path = null
    ): bool
    {
        $dir = $path ?? dirname(__DIR__) . '/../../uploads';
        if (!is_dir($dir)) mkdir($dir);

        if (empty($fileBytes) || empty($fileName)) {
            throw Error::message(Error::EMPTY_FILE);
        }

        $fileParts = explode('.', $fileName);
        $fileExtension = $fileParts[count($fileParts) - 1];

        $extensions = array_map('strtolower', FileValidations::validExtensions);
        if (!in_array(strtolower($fileExtension), $extensions)) {
            throw Error::message(Error::INVALID_FORMAT);
        }

        $epocTimestamp = (new DateTime())->getTimestamp();
        $generatedFile = "$dir/{$epocTimestamp}_sheet.$fileExtension";

        $bytesStr = pack('C*', ...$fileBytes);
        file_put_contents($generatedFile, $bytesStr);

        $file = FileValidations::getFileFromUpload($dir);
        return in_array($file, explode('/', $generatedFile));
    }

    /**
     * @param string $from
     * @param string $to
     * @return bool
     */
    protected function move_file(string $from, string $to): bool
    {
        return move_uploaded_file($from, $to);
    }
}

// HELPER TO CONTROLLER: https://www.youtube.com/watch?v=AzXaR-tt-dg

/*
 * ROADMAP:
 * -- Create flutter web docker
 * -- Create flutter frontend
 *
 * -- OPTIONAL: Create swagger
 * -- BE HAPPY
 */