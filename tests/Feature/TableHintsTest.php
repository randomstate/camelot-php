<?php


namespace RandomState\Camelot\Tests\Feature;


use RandomState\Camelot\Areas;
use RandomState\Camelot\Camelot;
use RandomState\Camelot\Tests\TestCase;

class TableHintsTest extends TestCase
{
    /**
     * @test
     */
    public function can_set_table_area()
    {
        $area = Areas::from(316,499,566,337);

        $tables = Camelot::stream($this->file('table_areas.pdf'))
            ->inAreas($area)
            ->extract();

        $this->assertCount(1, $tables);
        $csv = $this->csvFromString($tables[0]);
        $csv->setHeaderOffset(1);
        $this->assertEquals('Payroll Period', $csv->getHeader()[0]);
    }
    
    /**
     * @test
     */
    public function can_set_more_than_one_table_area()
    {
        $area = Areas::from(89, 694,529,446)
            ->add(89, 383, 529, 143)
        ;

        $tables = Camelot::stream($this->file('twotables_2.pdf'))
            ->inAreas($area)
            ->extract();

        $this->assertCount(2, $tables);
        $csv = $this->csvFromString($tables[0]);
        $csv->setHeaderOffset(1);

        $this->assertEquals('State', $csv->getHeader()[0]);
        $this->assertEquals('n', $csv->getHeader()[1]);

        $csv = $this->csvFromString($tables[1]);
        $csv->setHeaderOffset(1);
        $this->assertEquals('State', $csv->getHeader()[0]);
        $this->assertEquals('n', $csv->getHeader()[1]);
    }
    
    /**
     * @test
     */
    public function can_suggest_table_region()
    {
        $area = Areas::from(170,370,560,270);

        $tables = Camelot::lattice($this->file('table_region.pdf'))
            ->inRegions($area)
            ->extract();

        $this->assertCount(1, $tables);
        $csv = $this->csvFromString($tables[0]);
        $csv->setHeaderOffset(0);

        $this->assertEquals('Età dell’Assicurato 
all’epoca del decesso', $csv->getHeader()[0]);
    }
}