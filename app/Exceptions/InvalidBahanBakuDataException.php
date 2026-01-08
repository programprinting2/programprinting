<?php

namespace App\Exceptions;

use Exception;

class InvalidBahanBakuDataException extends Exception
{
    public function __construct(string $message = 'Data bahan baku tidak valid')
    {
        parent::__construct($message);
    }
}
