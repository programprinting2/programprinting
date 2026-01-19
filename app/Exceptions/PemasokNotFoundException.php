<?php

namespace App\Exceptions;

use Exception;

class PemasokNotFoundException extends Exception
{
    public function __construct(string $identifier = 'Pemasok')
    {
        parent::__construct("{$identifier} tidak ditemukan");
    }
}










