<?php

namespace DmFileman\Service\Thumbnailer;

use Imagine\Image\ImageInterface;
use Imagine\Gd\Imagine as ImagineGd;
use Imagine\Gmagick\Imagine as ImagineGmagick;
use Imagine\Imagick\Imagine as ImagineImagick;

class Thumbnailer
{
    const CONFIG_WIDTH = 'width';

    const CONFIG_HEIGHT = 'height';

    /** @var ImagineGd|ImagineGmagick|ImagineImagick */
    protected $imagine;

    /** @var Factory */
    protected $factory;

    /** @var array */
    protected $thumbConfig = [];

    /**
     * @param ImageInterface $imagine
     * @param Factory        $factory
     * @param array          $thumbConfig
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($imagine, Factory $factory, array $thumbConfig = [])
    {
        if ($imagine instanceof ImagineGd || $imagine instanceof ImagineGmagick || $imagine instanceof ImagineImagick) {
            $this->imagine = $imagine;
        } else {
            $msg = 'Imagine must be an instance of ImagineGd, ImagineGmagick or ImagineImagick';
            throw new \InvalidArgumentException($msg);
        }

        $this->factory = $factory;

        $this->thumbConfig = $thumbConfig;
    }

    /**
     * @param array $thumbConfig
     */
    public function setThumbConfig(array $thumbConfig = [])
    {
        $this->thumbConfig = $thumbConfig;
    }

    /**
     * @param string $origName
     * @param string $origDir
     * @param string $thumbDir
     *
     * @return bool
     */
    public function resizeOrigImage($origName, $origDir, $thumbDir)
    {
        if (!file_exists($origName)) {
            return false;
        }

        // Getimagesize tends to throw errors not documented
        $origInfo = @getimagesize($origName);

        if (!$origInfo) {
            return false;
        }

        $thumbName = str_replace($origDir, $thumbDir, $origName);

        $this->resize($origName, $thumbName, $origInfo);

        return true;
    }

    /**
     * @param string $origName
     * @param string $thumbName
     * @param array  $origInfo
     */
    public function resize($origName, $thumbName, array $origInfo)
    {
        if ($this->shouldResize($origInfo)) {
            $resizeParameters = $this->getResizeParameters($origInfo);
            $cropParameters   = $this->getCropParameters($resizeParameters);

            $resizeSize = $this->factory->getBox($resizeParameters[0], $resizeParameters[1]);

            $cropStart = $this->factory->getPoint($cropParameters[0], $cropParameters[1]);
            $cropSize  = $this->factory->getBox(
                $this->thumbConfig[static::CONFIG_WIDTH],
                $this->thumbConfig[static::CONFIG_HEIGHT]
            );

            $this->imagine->open($origName)
                ->resize($resizeSize)
                ->crop($cropStart, $cropSize)
                ->save($thumbName);
        } else {
            copy($origName, $thumbName);
        }
    }

    /**
     * @param array $origInfo
     *
     * @return bool
     */
    protected function shouldResize(array $origInfo)
    {
        if ($origInfo[0] > $this->thumbConfig[static::CONFIG_WIDTH]) {
            return true;
        }

        return $origInfo[1] > $this->thumbConfig[static::CONFIG_HEIGHT];
    }

    /**
     * o:1024x768, t:128x128 => w:8, h:6 => divide by 6
     *
     * @param array $origInfo
     *
     * @return array
     */
    protected function getResizeParameters(array $origInfo)
    {
        $widthRatio  = $origInfo[0] / $this->thumbConfig[static::CONFIG_WIDTH];
        $heightRatio = $origInfo[1] / $this->thumbConfig[static::CONFIG_HEIGHT];

        $realRatio = min($widthRatio, $heightRatio);

        $resizeWidth  = ceil($origInfo[0] / $realRatio);
        $resizeHeight = ceil($origInfo[1] / $realRatio);

        return [$resizeWidth, $resizeHeight, $realRatio];
    }

    /**
     * @param array $resizeParameters
     *
     * @return array
     */
    protected function getCropParameters(array $resizeParameters)
    {
        $cropX = floor(($resizeParameters[0] - $this->thumbConfig[static::CONFIG_WIDTH]) / 2);
        $cropY = floor(($resizeParameters[1] - $this->thumbConfig[static::CONFIG_HEIGHT]) / 2);

        return [$cropX, $cropY];
    }
}
