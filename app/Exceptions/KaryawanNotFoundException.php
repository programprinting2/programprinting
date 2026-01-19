<?php

namespace App\Exceptions;

use Exception;

class KaryawanNotFoundException extends Exception
{
    public function __construct(string $identifier = 'Karyawan')
    {
        parent::__construct("{$identifier} tidak ditemukan");
    }
}










