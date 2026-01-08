<?php

namespace App\Exceptions;

use Exception;

class PembelianNotFoundException extends Exception
{
    public function __construct(string $identifier = 'Pembelian')
    {
        parent::__construct("{$identifier} tidak ditemukan");
    }
}








