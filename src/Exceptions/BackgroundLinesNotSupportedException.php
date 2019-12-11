<?php


namespace RandomState\Camelot\Exceptions;


use RandomState\Camelot\Camelot;
use Throwable;

class BackgroundLinesNotSupportedException extends \Exception
{
    public function __construct($mode)
    {
        $valid = Camelot::MODE_LATTICE;
        parent::__construct("Processing mode '$mode' does not support processing background lines. It can be used with '$valid' mode.");
    }
}