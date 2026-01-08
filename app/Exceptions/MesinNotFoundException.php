<?php

namespace App\Exceptions;

use Exception;

class MesinNotFoundException extends Exception
{
    public function __construct(string $message = 'Mesin tidak ditemukan')
    {
        parent::__construct($message);
    }
}
