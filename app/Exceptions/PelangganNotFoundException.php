<?php

namespace App\Exceptions;

use Exception;

class PelangganNotFoundException extends Exception
{
    public function __construct(string $identifier = 'Pelanggan')
    {
        parent::__construct("{$identifier} tidak ditemukan");
    }
}








