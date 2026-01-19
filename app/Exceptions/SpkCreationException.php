<?php

namespace App\Exceptions;

use Exception;

class SpkCreationException extends Exception
{
    protected array $errors;

    public function __construct(array $errors = [], string $message = 'Gagal membuat SPK')
    {
        $this->errors = $errors;
        parent::__construct($message);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}










