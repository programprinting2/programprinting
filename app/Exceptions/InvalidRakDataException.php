<?php

namespace App\Exceptions;

use Exception;

class InvalidRakDataException extends Exception
{
    public function __construct(string $message = 'Data rak tidak valid')
    {
        parent::__construct($message);
    }
}










