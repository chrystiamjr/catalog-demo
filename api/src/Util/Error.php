<?php

namespace App\Util;

use Exception;

class Error
{
    const EMPTY_PAYLOAD = [
        'code' => 100,
        'message' => "Empty payload provided."
    ];

    const INVALID_PAYLOAD = [
        'code' => 101,
        'message' => "Invalid payload provided. Please, use the following example: @PAYLOAD@"
    ];

    const EMPTY_FOLDER = [
        'code' => 102,
        'message' => 'The uploads folder is entirely empty.'
    ];

    const FILE_NOT_FOUND = [
        'code' => 103,
        'message' => 'The requested file was not found'
    ];

    const INVALID_FORMAT = [
        'code' => 104,
        'message' => 'The requested file is not in a valid format.'
    ];

    const EMPTY_FILE = [
        'code' => 105,
        'message' => 'The requested file has an empty body.'
    ];

    const FILE_ALREADY_UPLOADED = [
        'code' => 106,
        'message' => 'The provided file has already been uploaded, provide a new one.'
    ];

    const EMPTY_PRODUCT_LIST = [
        'code' => 107,
        'message' => 'No products available in the system.'
    ];

    const REDIS_DISCONNECTED = [
        'code' => 108,
        'message' => 'The redis memory cache is disconnected.\n' .
            'Please contact our support team to solve this issue.'
    ];

    const SPREADSHEET_ERROR = [
        'code' => 999,
        'message' => 'An unexpected error has occurred with our spreadsheet client.\n' .
            'Please contact our support team and provide the error code @CODE@.'
    ];

    /**
     * @param array $error
     * @param array|null $replacers
     * @return Exception
     */
    public static function message(array $error, ?array $replacers = []): Exception
    {
        ['code' => $code, 'message' => $msg] = $error;

        foreach ($replacers as $key => $replacement) {
            $msg = str_replace($key, $replacement, $msg);
        }

        return new Exception($msg, $code);
    }

    /**
     * @param Exception $exception
     * @return Exception
     */
    public static function rethrow(Exception $exception): Exception
    {
        return new Exception($exception->getMessage(), $exception->getCode());
    }

    /**
     * @param Exception $exception
     * @param array|null $trace
     * @return string
     */
    public static function jsonError(Exception $exception, ?array $trace = null): string
    {
        $error = [
            'code' => $exception->getCode(),
            'message' => $exception->getMessage(),
            'additionalInfo' => []
        ];

        if (!empty($trace)) {
            $error['additionalInfo'] = $trace;
            return json_encode($error);
        }

        foreach ($exception->getTrace() as $trace) {
            if (!strpos($trace['file'], 'vendor/') &&
                !strpos($trace['file'], 'index.php')
            ) {
                $error['additionalInfo'][] = [
                    'line' => $trace['line'],
                    'file' => str_replace('/var/www/api/', '', $trace['file']),
                    'trace' => $trace['class'] . $trace['type'] . $trace['function'],
                    'args' => $trace['args']
                ];
            }
        }

        return json_encode($error);
    }
}