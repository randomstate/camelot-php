<?php


namespace RandomState\Camelot;


use Symfony\Component\Process\Process;
use Spatie\TemporaryDirectory\TemporaryDirectory;

class Camelot
{
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

    public function __construct($path, $mode = 'lattice')
    {
        $this->path = $path;
        $this->mode = $mode;
    }

    public static function lattice($path)
    {
        return new self($path);
    }

    public static function stream($path)
    {
        return new self($path, 'stream');
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

    protected function runCommand($outputPath)
    {
        $mode = $this->mode;
        $cmd = "camelot --format csv --output $outputPath $mode " . $this->path;

        $process = Process::fromShellCommandline($cmd);

        $process->run();

//        var_dump($process->getExitCode(), $process->getErrorOutput());
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
}