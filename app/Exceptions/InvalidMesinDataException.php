<?php

namespace App\Exceptions;

use Exception;

class InvalidMesinDataException extends Exception
{
    public function __construct(string $message = 'Data mesin tidak valid')
    {
        parent::__construct($message);
    }
}
