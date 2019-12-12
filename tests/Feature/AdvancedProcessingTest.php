<?php


namespace RandomState\Camelot\Tests\Feature;


use RandomState\Camelot\Camelot;
use RandomState\Camelot\Exceptions\BackgroundLinesNotSupportedException;
use RandomState\Camelot\Exceptions\ColumnSeparatorsNotSupportedException;
use RandomState\Camelot\Tests\TestCase;

class AdvancedProcessingTest extends TestCase
{
    /**
     * @test
     */
    public function can_process_background_lines() 
    {
        $tables = Camelot::lattice($this->file('background_lines_1.pdf'))
            ->processBackgroundLines()
            ->extract();

        $csv = $this->csvFromString($tables[1]);
        $csv->setHeaderOffset(0);

        $this->assertCount(8, $header = $csv->getHeader());
        $this->assertEquals('State', $header[0]);
    }

    /**
     * @test
     */
    public function cannot_use_background_line_processing_with_stream()
    {
        $this->expectException(BackgroundLinesNotSupportedException::class);

        Camelot::stream($this->file('background_lines_1.pdf'))
            ->processBackgroundLines()
            ->extract();
    }
    
    /**
     * @test
     */
    public function set_column_separators() 
    {
        $tables = Camelot::stream($this->file('column_separators.pdf'))
            ->setColumnSeparators([72,95,209,327,442,529,566,606,683])
            ->extract();

        $this->assertCount(1, $tables);
        $csv = $this->csvFromString($tables[0]);
        $csv->setHeaderOffset(3);
        $this->assertEquals('LICENSE', $csv->getHeader()[0]);
        $this->assertEquals('PREMISE', $csv->getHeader()[4]);
    }

    /**
     * @test
     */
    public function cannot_set_column_separators_for_lattice_mode()
    {
        $this->expectException(ColumnSeparatorsNotSupportedException::class);

        Camelot::lattice($this->file('column_separators.pdf'))
            ->setColumnSeparators([72,95,209,327,442,529,566,606,683])
            ->extract();
    }

    /**
     * @test
     */
    public function enable_split_text_along_separators()
    {
        $tables = Camelot::stream($this->file('column_separators.pdf'))
            ->setColumnSeparators([72,95,209,327,442,529,566,606,683], true)
            ->extract();

        $this->assertCount(1, $tables);
        $csv = $this->csvFromString($tables[0]);
        $csv->setHeaderOffset(4);
        $this->assertEquals('NUMBER', $csv->getHeader()[0]);
        $this->assertEquals('TYPE', $csv->getHeader()[1]);
    }
}