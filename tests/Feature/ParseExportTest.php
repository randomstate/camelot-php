<?php


use RandomState\Camelot\Camelot;
use RandomState\Camelot\Tests\TestCase;

class ParseExportTest extends TestCase
{
    /**
     * @test
     */
    public function can_set_lattice_mode()
    {
        $tables = Camelot::lattice($this->file('foo_lattice.pdf'))
            ->extract()[0];

        $csv = $this->csvFromString($tables);
        $csv->setHeaderOffset(0);

        $this->assertCount(7, $csv->getHeader());
    }

    /**
     * @test
     */
    public function can_set_stream_mode()
    {
        $tables = Camelot::stream($this->file('health_stream.pdf'))
            ->extract()[0];

        $csv = $this->csvFromString($tables);
        $csv->setHeaderOffset(0);

        $this->assertCount(8, $header = $csv->getHeader());
        $this->assertEquals('States-A', $header[0]);
    }

    /**
     * @test
     */
    public function can_set_output_filename_base()
    {
        Camelot::stream($this->file('health_stream.pdf'))
            ->save(__DIR__ . '/output-custom.txt');

        $this->assertFileExists(__DIR__ . '/output-custom-page-1-table-1.txt');
        unlink(__DIR__ . '/output-custom-page-1-table-1.txt');
    }

    /**
     * @test
     */
    public function can_set_output_format()
    {
        $camelot = Camelot::lattice($this->file('foo_lattice.pdf'));
        $this->assertEquals('csv', $camelot->getFormat());

        $camelot->json();
        $this->assertEquals('json', $camelot->getFormat());

        $camelot->csv();
        $this->assertEquals('csv', $camelot->getFormat());

        $camelot->html();
        $this->assertEquals('html', $camelot->getFormat());

        $camelot->sqlite();
        $this->assertEquals('sqlite', $camelot->getFormat());

        $camelot->excel();
        $this->assertEquals('excel', $camelot->getFormat());
    }

    /**
     * @test
     */
    public function defaults_to_lattice_algorithm()
    {
        $camelot = new Camelot('foo.pdf');
        $this->assertEquals('lattice', $camelot->getMode());
    }
}