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

    /**
     * @test
     */
    public function flag_superscripts_and_subscripts()
    {
        $tables = Camelot::stream($this->file('superscript.pdf'))
            ->flagSize()
            ->extract();

        $this->assertCount(1, $tables);
        $csv = $this->csvFromString($tables[0]);

        $this->assertStringContainsString('<s>', $csv->fetchOne(18)[2]);
        $this->assertStringContainsString('</s>', $csv->fetchOne(18)[2]);
    }
    
    /**
     * @test
     */
    public function strip_characters_from_text() 
    {
        $tables = Camelot::stream($this->file('strip.pdf'))
            ->strip(" .\n") // space, period or new lines
            ->extract();

        $csv = $this->csvFromString($tables[0]);
        $this->assertEquals('Robbery', $csv->fetchOne(11)[0]);
    }

    /**
     * @test
     */
    public function can_adjust_edge_tolerance()
    {
        $tables = Camelot::stream($this->file('edge_tol.pdf'))
            ->strip("\n")
            ->setEdgeTolerance(500)
            ->extract();

        $this->assertStringContainsString('Total investment result per unit', $tables[0]);
    }

    /**
     * @test
     */
    public function can_adjust_row_tolerance()
    {
        $tables = Camelot::stream($this->file('row_tol.pdf'))
            ->strip("\n")
            ->setRowTolerance(10)
            ->extract();

        $this->assertCount(1, $tables);
        $csv = $this->csvFromString($tables[0]);
        $csv->setHeaderOffset(0);

        $this->assertEquals('Nombre Entidad', $csv->getHeader()[1]);
    }
    
    /**
     * @test
     */
    public function can_set_line_scale() 
    {
        $tables = Camelot::lattice($this->file('short_lines.pdf'))
            ->strip("-\n")
            ->setLineScale(40)
            ->extract();

        $this->assertCount(1, $tables);
        $csv = $this->csvFromString($tables[0]);
        $csv->setHeaderOffset(0);

        $this->assertEquals('Prevalence', $csv->getHeader()[3]);
        $this->assertEquals('C.I*', $csv->getHeader()[4]);
        $this->assertEquals('RelativePrecision', $csv->getHeader()[5]);
        $this->assertEquals('Sample sizeper State', $csv->getHeader()[6]);
    }

    /**
     * @test
     */
    public function can_set_text_shift_in_spanning_cells()
    {
        $tables = Camelot::lattice($this->file('short_lines.pdf'))
            ->strip("-\n")
            ->setLineScale(40)
            ->shiftText('r', 'b')
            ->extract();

        $this->assertCount(1, $tables);
        $csv = $this->csvFromString($tables[0]);

        $row = $csv->fetchOne(3);
        $this->assertEquals(2400, $row[1]);
    }
    
    /**
     * @test
     */
    public function can_copy_text_in_spanning_cells() 
    {
        $tables = Camelot::lattice($this->file('copy_text.pdf'))
            ->copyTextSpanningCells('v')
            ->extract();

        $this->assertCount(1, $tables);
        $csv = $this->csvFromString($tables[0]);

        $this->assertEquals(4, $csv->fetchOne(4)[0]);
        $this->assertEquals('West Bengal', $csv->fetchOne(4)[1]);

        $this->assertEquals(4, $csv->fetchOne(5)[0]);
        $this->assertEquals('West Bengal', $csv->fetchOne(5)[1]);
    }
}