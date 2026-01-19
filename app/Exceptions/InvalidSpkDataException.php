<?php

namespace App\Exceptions;

use Exception;

class InvalidSpkDataException extends Exception
{
    public function __construct(string $message = 'Data SPK tidak valid')
    {
        parent::__construct($message);
    }
}










