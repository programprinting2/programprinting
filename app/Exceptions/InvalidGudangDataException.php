<?php

namespace App\Exceptions;

use Exception;

class InvalidGudangDataException extends Exception
{
    public function __construct(string $message = 'Data gudang tidak valid')
    {
        parent::__construct($message);
    }
}










