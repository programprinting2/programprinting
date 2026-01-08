<?php

namespace App\Exceptions;

use Exception;

class BahanBakuNotFoundException extends Exception
{
    public function __construct(string $message = 'Bahan baku tidak ditemukan')
    {
        parent::__construct($message);
    }
}
