<?php

namespace DmFileman\Service\Thumbnailer;

use Imagine\Image\Box;
use Imagine\Image\Point;

class Factory
{
    /**
     * @param int $width
     * @param int $height
     *
     * @return Box
     */
    public function getBox($width, $height)
    {
        return new Box($width, $height);
    }

    /**
     * @param int $xCoordinate
     * @param int $yCoordinate
     *
     * @return Point
     */
    public function getPoint($xCoordinate, $yCoordinate)
    {
        return new Point($xCoordinate, $yCoordinate);
    }
}
