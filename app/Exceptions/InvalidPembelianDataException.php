<?php

namespace App\Exceptions;

use Exception;

class InvalidPembelianDataException extends Exception
{
    public function __construct(string $message = 'Data pembelian tidak valid')
    {
        parent::__construct($message);
    }
}








