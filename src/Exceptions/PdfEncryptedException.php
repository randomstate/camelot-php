<?php


namespace RandomState\Camelot\Exceptions;


use Throwable;

class PdfEncryptedException extends \Exception
{
    public function __construct(string $filePath)
    {
        parent::__construct("The PDF $filePath is encrypted and cannot be read without a password. Supply a password with \$camelot->password('my_password').");
    }
}