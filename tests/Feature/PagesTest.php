<?php


namespace RandomState\Camelot\Tests\Feature;


use RandomState\Camelot\Camelot;
use RandomState\Camelot\Tests\TestCase;

class PagesTest extends TestCase
{
    /**
     * @test
     */
    public function can_set_pages_raw()
    {
        $camelot = Camelot::lattice('foo.pdf')->pages($pages = '1-2,4,5-end');

        $this->assertEquals($pages,$camelot->getPages());
    }

}