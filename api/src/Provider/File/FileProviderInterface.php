<?php

namespace App\Provider\File;

use PhpOffice\PhpSpreadsheet\Spreadsheet;

interface FileProviderInterface
{
    public function readFile(
        ?string      $fileName,
        ?Spreadsheet $mockedSheet = null
    ): array;

    public function uploadFile(
        array   $fileBytes,
        string  $fileName,
        ?string $path = null
    ): bool;
}