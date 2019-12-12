<?php


namespace RandomState\Camelot;


use RandomState\Camelot\Exceptions\BackgroundLinesNotSupportedException;
use RandomState\Camelot\Exceptions\PdfEncryptedException;
use Symfony\Component\Process\Process;
use Spatie\TemporaryDirectory\TemporaryDirectory;

class Camelot
{
    const MODE_LATTICE = 'lattice';
    const MODE_STREAM = 'stream';

    /**
     * lattice | stream
     *
     * @var string
     */
    protected $mode;

    /**
     * csv | json | excel | html | sqlite
     *
     * @var string
     */
    protected $format = 'csv';

    /**
     * @var string
     */
    protected $path;

    /**
     * @var string
     */
    protected $pages;

    /**
     * @var string
     */
    protected $password;

    /**
     * @var string
     */
    protected $processBackgroundLines;

    /**
     * @var string
     */
    protected $plot;

    /**
     * @var Areas
     */
    protected $areas;

    /**
     * @var Areas
     */
    protected $regions;

    public function __construct($path, $mode = null)
    {
        $this->path = $path;
        $this->mode = $mode ?? static::MODE_LATTICE;
    }

    public static function lattice($path)
    {
        return new self($path);
    }

    public static function stream($path)
    {
        return new self($path, static::MODE_STREAM);
    }

    public function pages(string $pages)
    {
        $this->pages = $pages;

        return $this;
    }

    public function csv()
    {
        $this->format = 'csv';
    }

    public function save($path)
    {
        // run the process
        $this->runCommand($path);
        return $this->getFilesContents($path);
    }

    public function extract()
    {
        $dir = (new TemporaryDirectory())->create();
        $path = $dir->path('extract.txt');

        $this->runCommand($path);

        $output = $this->getFilesContents($path);

        $dir->delete();

        return $output;
    }

    protected function getFilesContents($filePath)
    {
        $pathInfo = pathinfo($filePath);
        $filename = $pathInfo['filename'];
        $directory = $pathInfo['dirname'];

        $files = scandir($directory);
        $files = array_values(array_filter($files, function($file) use($filename) {
            return preg_match("/{$filename}-.*-table-.*\..*/", $file);
        }));

        $output = [];

        foreach($files as $file) {
            $output[] = $content = file_get_contents($directory . DIRECTORY_SEPARATOR . $file);
        }

        return $output;
    }

    protected function runCommand($outputPath = null)
    {
        $output = $outputPath ? " --output $outputPath" : "";
        $mode = " {$this->mode}";
        $pages = $this->pages ? " --pages {$this->pages}" : "";
        $password = $this->password ? " --password {$this->password}": "";

        // Advanced options
        $background = $this->processBackgroundLines ? " --process_background ": "";
        $plot = $this->plot ? " -plot {$this->plot}" : "";

        // Table areas/regions
        $areas = $this->areas ? $this->areas->toDelimitedString(" -T ") : "";
        $regions = $this->regions ? $this->regions->toDelimitedString(" -R ") : "";

        $cmd = "camelot --format csv {$output}{$pages}{$password}{$mode}{$background}{$plot}{$areas}{$regions} " . $this->path;

        $process = Process::fromShellCommandline($cmd);
        $process->run();

        if(!$process->isSuccessful()) {
            $this->throwError($this->path, $process->getErrorOutput(), $cmd);
        }
    }

    public function json()
    {
        $this->format = 'json';

        return $this;
    }

    public function html()
    {
        $this->format = 'html';

        return $this;
    }

    public function sqlite()
    {
        $this->format = 'sqlite';

        return $this;
    }

    public function excel()
    {
        $this->format = 'excel';

        return $this;
    }

    public function getFormat()
    {
        return $this->format;
    }

    public function getMode()
    {
        return $this->mode;
    }

    /**
     * @return string
     */
    public function getPages()
    {
        return $this->pages;
    }

    public function password($password)
    {
        $this->password = $password;

        return $this;
    }

    protected function throwError($path, string $getErrorOutput, string $cmd)
    {
        if(strpos($getErrorOutput, 'file has not been decrypted') > -1) {
            throw new PdfEncryptedException($path);
        }

        throw new \Exception("Unexpected Camelot error.\r\nCommand: $cmd\r\nOutput:\r\n-----------\r\n$getErrorOutput");
    }

    public function processBackgroundLines()
    {
        if($this->mode !== static::MODE_LATTICE) {
            throw new BackgroundLinesNotSupportedException($this->mode);
        }

        $this->processBackgroundLines = true;

        return $this;
    }

    public function plot($kind = 'text')
    {
        $this->plot = $kind;

        $this->runCommand();

        return $this;
    }

    public function inAreas(Areas $areas)
    {
        $this->areas = $areas;

        return $this;
    }

    public function inRegions(Areas $regions)
    {
        $this->regions = $regions;

        return $this;
    }

}