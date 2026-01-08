<?php

namespace App\Exceptions;

use Exception;

class RakNotFoundException extends Exception
{
    public function __construct(string $identifier = 'Rak')
    {
        parent::__construct("{$identifier} tidak ditemukan");
    }
}








