<?php

namespace App\Exceptions;

use Exception;

class InvalidPelangganDataException extends Exception
{
    public function __construct(string $message = 'Data pelanggan tidak valid')
    {
        parent::__construct($message);
    }
}








