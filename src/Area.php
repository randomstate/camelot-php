<?php


namespace RandomState\Camelot;


class Area
{
    protected $xTopLeft;
    protected $yTopLeft;
    protected $xBottomRight;
    protected $yBottomRight;

    public function __construct($xTopLeft, $yTopLeft, $xBottomRight, $yBottomRight)
    {
        $this->xTopLeft = $xTopLeft;
        $this->yTopLeft = $yTopLeft;
        $this->xBottomRight = $xBottomRight;
        $this->yBottomRight = $yBottomRight;
    }

    public function xTopLeft()
    {
        return $this->xTopLeft;
    }

    public function yTopLeft()
    {
        return $this->yTopLeft;
    }

    public function xBottomRight()
    {
        return $this->xBottomRight;
    }

    public function yBottomRight()
    {
        return $this->yBottomRight;
    }

    public function coords()
    {
        return implode(',', [
            $this->xTopLeft,
            $this->yTopLeft,
            $this->xBottomRight,
            $this->yBottomRight,
        ]);
    }
}