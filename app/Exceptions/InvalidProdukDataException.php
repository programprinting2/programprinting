<?php

namespace App\Exceptions;

use Exception;

class InvalidProdukDataException extends Exception
{
    public function __construct(string $message = 'Data produk tidak valid')
    {
        parent::__construct($message);
    }
}
