<?php


namespace RandomState\Camelot;


class Areas
{
    protected array $areas = [];

    public static function from($xTopLeft, $yTopLeft, $xBottomRight, $yBottomRight): static
    {
        $areas = new static;
        $areas->push(new Area($xTopLeft, $yTopLeft, $xBottomRight, $yBottomRight));

        return $areas;
    }

    public function add($xTopLeft, $yTopLeft, $xBottomRight, $yBottomRight): self
    {
        $this->areas[] = new Area($xTopLeft, $yTopLeft, $xBottomRight, $yBottomRight);

        return $this;
    }

    public function push(Area $area): self
    {
        $this->areas[] = $area;

        return $this;
    }

    public function toDelimitedString(string $join): string
    {
        $coords = array_map(function(Area $area) {
            return $area->coords();
        }, $this->areas);

        return $join . implode($join, $coords);
    }
}