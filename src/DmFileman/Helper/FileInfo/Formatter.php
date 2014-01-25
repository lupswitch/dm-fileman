<?php

namespace DmFileman\Helper\FileInfo;

use SplFileInfo;

class Formatter
{
    /** @var array */
    protected $sizeUnits = array('B', 'KB', 'MB', 'GB');

    /** @var string */
    protected $decPoint = '.';

    /** @var string */
    protected $thousandsSep = ',';

    /**
     * @param null|string $decPoint
     * @param null|string $thousandsSep
     */
    public function __construct($decPoint = null, $thousandsSep = null)
    {
        if (!is_null($decPoint)) {
            $this->decPoint = (string)$decPoint;
        }

        if (!is_null($thousandsSep)) {
            $this->thousandsSep = (string)$thousandsSep;
        }
    }

    /**
     * @param SplFileInfo|null $splFileInfo
     * @param bool             $rawSize
     *
     * @return string|int
     */
    public function formatSize(SplFileInfo $splFileInfo = null, $rawSize = false)
    {
        $size = $splFileInfo ? $splFileInfo->getSize() : 0;

        if ($size > 0 && !$rawSize) {
            $unit = intval(log($size, 1024));

            if (array_key_exists($unit, $this->sizeUnits) === true) {
                $number = $size / pow(1024, $unit);

                $decimals = floor($number) == ceil($number) ? 0 : 2;

                $size = number_format($number, $decimals, $this->decPoint, $this->thousandsSep);

                return sprintf('%s %s', $size, $this->sizeUnits[$unit]);
            }
        }

        return $size;
    }

    /**
     * @param SplFileInfo|null $splFileInfo
     *
     * @return string
     */
    public function formatPermissions(SplFileInfo $splFileInfo = null)
    {
        if ($splFileInfo) {
            return substr(sprintf('%o', $splFileInfo->getPerms()), -4);
        }

        return '';
    }

    /**
     * @param SplFileInfo|null $splFileInfo
     *
     * @return string
     */
    public function formatOwner(SplFileInfo $splFileInfo = null)
    {
        if ($splFileInfo) {
            return posix_getpwuid($splFileInfo->getOwner())['name'];
        }

        return '';
    }

    /**
     * @param SplFileInfo|null $splFileInfo
     *
     * @return string
     */
    public function formatGroup(SplFileInfo $splFileInfo = null)
    {
        if ($splFileInfo) {
            return posix_getpwuid($splFileInfo->getGroup())['name'];
        }

        return '';
    }
}
