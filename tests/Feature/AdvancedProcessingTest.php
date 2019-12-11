<?php


namespace RandomState\Camelot\Tests\Feature;


use RandomState\Camelot\Camelot;
use RandomState\Camelot\Exceptions\BackgroundLinesNotSupportedException;
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
    
}