<?php

namespace App\Exceptions;

use Exception;

class GudangNotFoundException extends Exception
{
    public function __construct(string $identifier = 'Gudang')
    {
        parent::__construct("{$identifier} tidak ditemukan");
    }
}










