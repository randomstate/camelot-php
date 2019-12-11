<?php


namespace RandomState\Camelot\Tests\Helpers;

use League\Csv\Reader;

trait CsvAssertions
{
    public function csvFromString($data)
    {
        return Reader::createFromString($data);
    }
}