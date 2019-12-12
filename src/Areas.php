<?php


namespace RandomState\Camelot;


class Areas
{
    /**
     * @var array
     */
    protected $areas = [];

    public static function from($xTopLeft, $yTopLeft, $xBottomRight, $yBottomRight)
    {
        $areas = new static;
        $areas->push(new Area($xTopLeft, $yTopLeft, $xBottomRight, $yBottomRight));

        return $areas;
    }

    public function add($xTopLeft, $yTopLeft, $xBottomRight, $yBottomRight)
    {
        $this->areas[] = new Area($xTopLeft, $yTopLeft, $xBottomRight, $yBottomRight);

        return $this;
    }

    public function push(Area $area)
    {
        $this->areas[] = $area;

        return $this;
    }

    public function toDelimitedString($join)
    {
        $coords = array_map(function(Area $area) {
            return $area->coords();
        }, $this->areas);

        return $join . implode($join, $coords);
    }
}