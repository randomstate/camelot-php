<?php


namespace RandomState\Camelot;


use RandomState\Camelot\Exceptions\BackgroundLinesNotSupportedException;
use RandomState\Camelot\Exceptions\ColumnSeparatorsNotSupportedException;
use RandomState\Camelot\Exceptions\PdfEncryptedException;
use Symfony\Component\Process\Process;
use Spatie\TemporaryDirectory\TemporaryDirectory;

class Camelot
{
    public const MODE_LATTICE = 'lattice';
    public const MODE_STREAM = 'stream';

    /**
     * lattice | stream
     *
     * @var string
     */
    protected string $mode;

    /**
     * csv | json | excel | html | sqlite
     *
     * @var string
     */
    protected string $format = 'csv';

    /**
     * @var string
     */
    protected string $path;

    /**
     * @var string
     */
    protected string $pages = '';

    /**
     * @var string
     */
    protected string $password = '';

    /**
     * @var string
     */
    protected string $processBackgroundLines = '';

    /**
     * @var string
     */
    protected string $plot = '';

    /**
     * @var Areas|null
     */
    protected ?Areas $areas = null;

    /**
     * @var Areas|null
     */
    protected ?Areas $regions = null;

    /**
     * @var array
     */
    protected array $columnSeparators = [];

    /**
     * @var bool
     */
    protected bool $splitAlongSeparators = false;

    /**
     * @var bool
     */
    protected bool $flagSize = false;

    /**
     * @var string
     */
    protected string $unwantedCharacters = '';

    /**
     * @var int|null
     */
    protected ?int $edgeTolerance = null;

    /**
     * @var int|null
     */
    protected ?int $rowTolerance = null;

    /**
     * @var int|null
     */
    protected ?int  $lineScale = null;

    /**
     * @var array
     */
    protected array $textShift = [];

    /**
     * @var array
     */
    protected array $copyTextDirections = [];

    public function __construct(string $path, ?string $mode = null)
    {
        $this->path = $path;
        $this->mode = $mode ?? static::MODE_LATTICE;
    }

    public static function lattice(string $path): static
    {
        return new self($path);
    }

    public static function stream(string $path): static
    {
        return new self($path, static::MODE_STREAM);
    }

    public function pages(string $pages): self
    {
        $this->pages = $pages;

        return $this;
    }

    public function csv(): self
    {
        $this->format = 'csv';

        return $this;
    }

    public function save(string $path): array
    {
        // run the process
        $this->runCommand($path);
        return $this->getFilesContents($path);
    }

    public function extract(): array
    {
        $dir = (new TemporaryDirectory())->create();
        $path = $dir->path('extract.txt');

        $this->runCommand($path);

        $output = $this->getFilesContents($path);

        $dir->delete();

        return $output;
    }

    protected function getFilesContents(string $filePath): array
    {
        $pathInfo = pathinfo($filePath);
        $filename = $pathInfo['filename'];
        $directory = $pathInfo['dirname'];

        $files = scandir($directory);
        $files = array_values(array_filter($files, function ($file) use ($filename) {
            return preg_match("/{$filename}-.*-table-.*\..*/", $file);
        }));

        $output = [];

        foreach ($files as $file) {
            $output[] = file_get_contents($directory . DIRECTORY_SEPARATOR . $file);
        }

        return $output;
    }

    protected function runCommand(?string $outputPath = null): void
    {
        $output = $outputPath ? " --output $outputPath" : "";
        $mode = " {$this->mode}";
        $pages = $this->pages ? " --pages {$this->pages}" : "";
        $password = $this->password ? " --password {$this->password}" : "";
        $format = $this->getFormat();

        // Advanced options
        $background = $this->processBackgroundLines ? " --process_background " : "";
        $plot = $this->plot ? " -plot {$this->plot}" : "";
        $split = ($this->splitAlongSeparators && $this->columnSeparators) ? " -split" : "";
        $flagSize = $this->flagSize ? " -flag" : "";
        $columnSeparators = count($this->columnSeparators) > 0 ? " -C " . implode(",",$this->columnSeparators) : "";
        $strip = $this->unwantedCharacters ? " -strip '{$this->unwantedCharacters}'" : "";
        $edgeTolerance = (null !== $this->edgeTolerance) ? " -e {$this->edgeTolerance}" : "";
        $rowTolerance = (null !== $this->rowTolerance) ? " -r {$this->rowTolerance}" : "";
        $lineScale = (null !== $this->lineScale) ? " -scale {$this->lineScale}" : "";
        $textShift = count($this->textShift) > 0 ? " -shift " . implode(" -shift ", $this->textShift) : "";
        $copyText = count($this->copyTextDirections) > 0 ? " -copy " . implode(" -copy ", $this->copyTextDirections) : "";

        // Table areas/regions
        $areas = $this->areas ? $this->areas->toDelimitedString(" -T ") : "";
        $regions = $this->regions ? $this->regions->toDelimitedString(" -R ") : "";

        $cmd = "camelot --format {$format} {$output}{$pages}{$password}{$flagSize}{$split}{$strip}{$mode}{$textShift}{$copyText}{$lineScale}{$edgeTolerance}{$rowTolerance}{$background}{$plot}{$areas}{$regions}{$columnSeparators} " . $this->path;

        $process = Process::fromShellCommandline($cmd);
        $process->run();

        if (!$process->isSuccessful()) {
            $this->throwError($this->path, $process->getErrorOutput(), $cmd);
        }
    }

    public function json(): self
    {
        $this->format = 'json';

        return $this;
    }

    public function html(): self
    {
        $this->format = 'html';

        return $this;
    }

    public function sqlite(): self
    {
        $this->format = 'sqlite';

        return $this;
    }

    public function excel(): self
    {
        $this->format = 'excel';

        return $this;
    }

    public function getFormat(): string
    {
        return $this->format;
    }

    public function getMode(): string
    {
        return $this->mode;
    }

    public function getPages(): string
    {
        return $this->pages;
    }

    public function password(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    protected function throwError(string $path, string $getErrorOutput, string $cmd): void
    {
        if (false !== stripos($getErrorOutput, 'file has not been decrypted')) {
            throw new PdfEncryptedException($path);
        }

        throw new \Exception("Unexpected Camelot error.\r\nCommand: $cmd\r\nOutput:\r\n-----------\r\n$getErrorOutput");
    }

    public function processBackgroundLines(): self
    {
        if ($this->mode !== static::MODE_LATTICE) {
            throw new BackgroundLinesNotSupportedException($this->mode);
        }

        $this->processBackgroundLines = true;

        return $this;
    }

    public function plot(string $kind = 'text'): self
    {
        $this->plot = $kind;

        $this->runCommand();

        return $this;
    }

    public function inAreas(Areas $areas): self
    {
        $this->areas = $areas;

        return $this;
    }

    public function inRegions(Areas $regions): self
    {
        $this->regions = $regions;

        return $this;
    }

    public function setColumnSeparators(array $xCoords, bool $split = false): self
    {
        if ($this->mode !== static::MODE_STREAM) {
            throw new ColumnSeparatorsNotSupportedException($this->mode);
        }

        $this->columnSeparators = $xCoords;
        $this->splitAlongSeparators = $split;

        return $this;
    }

    public function flagSize(bool $flag = true): self
    {
        $this->flagSize = $flag;

        return $this;
    }

    public function strip(string $unwantedCharacters): self
    {
        $this->unwantedCharacters = $unwantedCharacters;

        return $this;
    }

    public function setEdgeTolerance(int $edgeTolerance): self
    {
        $this->edgeTolerance = $edgeTolerance;

        return $this;
    }

    public function setRowTolerance(int $rowTolerance): self
    {
        $this->rowTolerance = $rowTolerance;

        return $this;
    }

    public function setLineScale(int $lineScale): self
    {
        $this->lineScale = $lineScale;

        return $this;
    }

    public function shiftText(...$directions): self
    {
        $this->textShift = $directions;

        return $this;
    }

    public function copyTextSpanningCells(...$directions): self
    {
        $this->copyTextDirections = $directions;

        return $this;
    }

}