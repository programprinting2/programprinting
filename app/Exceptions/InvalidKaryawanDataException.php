<?php

namespace App\Exceptions;

use Exception;

class InvalidKaryawanDataException extends Exception
{
    public function __construct(string $message = 'Data karyawan tidak valid')
    {
        parent::__construct($message);
    }
}










