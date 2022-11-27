<?php


namespace RandomState\Camelot\Exceptions;


abstract class NotSupportedException extends \Exception
{
    public function __construct(string $mode)
    {
        parent::__construct("Processing mode '$mode' does not support ". $this->featureName() . ". It can be used with ". $this->validModesString().".");
    }

    private function validModesString(): string
    {
        $validModes = $this->validModes();
        $modes = implode(' | ', $validModes);

        return count($validModes) > 1 ? $modes . ' modes' : $modes . ' mode';
    }
    abstract protected function validModes(): array;
    abstract protected function featureName(): string;
}