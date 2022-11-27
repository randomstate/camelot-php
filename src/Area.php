<?php


namespace RandomState\Camelot;


class Area
{
    protected int $xTopLeft;
    protected int $yTopLeft;
    protected int $xBottomRight;
    protected int $yBottomRight;

    public function __construct(int $xTopLeft, int $yTopLeft, int $xBottomRight, int $yBottomRight)
    {
        $this->xTopLeft = $xTopLeft;
        $this->yTopLeft = $yTopLeft;
        $this->xBottomRight = $xBottomRight;
        $this->yBottomRight = $yBottomRight;
    }

    public function xTopLeft(): int
    {
        return $this->xTopLeft;
    }

    public function yTopLeft(): int
    {
        return $this->yTopLeft;
    }

    public function xBottomRight(): int
    {
        return $this->xBottomRight;
    }

    public function yBottomRight(): int
    {
        return $this->yBottomRight;
    }

    public function coords(): string
    {
        return implode(',', [
            $this->xTopLeft,
            $this->yTopLeft,
            $this->xBottomRight,
            $this->yBottomRight,
        ]);
    }
}