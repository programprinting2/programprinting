<?php

namespace App\Exceptions;

use Exception;

class SpkNotFoundException extends Exception
{
    public function __construct(string $identifier = 'SPK')
    {
        parent::__construct("{$identifier} tidak ditemukan");
    }
}








