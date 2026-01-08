<?php

namespace App\Exceptions;

use Exception;

class InvalidPemasokDataException extends Exception
{
    public function __construct(string $message = 'Data pemasok tidak valid')
    {
        parent::__construct($message);
    }
}








