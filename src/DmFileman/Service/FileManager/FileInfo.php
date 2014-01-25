<?php

namespace DmFileman\Service\FileManager;

use SplFileInfo;
use DmFileman\Helper\FileInfo\Formatter;
use DmFileman\Helper\FileInfo\Path as PathHelper;

/**
 * Class FileInfo
 *
 * @package DmFileman\Entity
 *
 * @method string getBasename
 * @method string getPathname
 * @method string getFilename
 * @method string getType
 * @method bool   isDir
 * @method bool   isFile
 */
class FileInfo
{
    /** @var string */
    protected $relativePath;

    /** @var string */
    protected $origBasePath;

    /** @var string */
    protected $thumbBasePath;

    /** @var SplFileInfo */
    protected $splFileInfo;

    /** @var string */
    protected $displayName;

    /** @var bool */
    protected $withFilenameDisabled = false;

    /** @var Formatter */
    protected $formatter;

    /** @var PathHelper */
    protected $pathHelper;

    /**
     * @param string     $relativePath
     * @param string     $origBasePath
     * @param string     $thumbBasePath
     * @param Formatter  $formatter
     * @param PathHelper $pathHelper
     */
    public function __construct($relativePath, $origBasePath, $thumbBasePath, $formatter = null, $pathHelper = null)
    {
        $this->relativePath = $relativePath;

        $this->origBasePath = $origBasePath;

        $this->thumbBasePath = $thumbBasePath;

        $this->formatter = $formatter;

        $this->pathHelper = $pathHelper;
    }

    /**
     * @param Formatter $formatter
     */
    public function setFormatter(Formatter $formatter)
    {
        $this->formatter = $formatter;
    }

    /**
     * @FIXME: Make sure helper is always injected
     *
     * @return Formatter
     */
    protected function getFormatter()
    {
        if (null === $this->formatter) {
            $this->formatter = new Formatter;
        }

        return $this->formatter;
    }

    /**
     * @param PathHelper $pathHelper
     */
    public function setPath(PathHelper $pathHelper)
    {
        $this->pathHelper = $pathHelper;
    }

    /**
     * @FIXME: Make sure helper is always injected
     *
     * @return PathHelper
     */
    protected function getPathHelper()
    {
        if (null === $this->pathHelper) {
            $this->pathHelper = new PathHelper;
        }

        return $this->pathHelper;
    }

    /**
     * @param SplFileInfo $splFileInfo
     */
    public function setFromSplFileInfo(SplFileInfo $splFileInfo)
    {
        $this->splFileInfo = $splFileInfo;
    }

    /**
     * @return string
     */
    public function getOrigBasePath()
    {
        return $this->origBasePath;
    }

    /**
     * @return string
     */
    public function getThumbBasePath()
    {
        return $this->thumbBasePath;
    }

    /**
     * @return string
     */
    public function disableWithFilename()
    {
        $this->withFilenameDisabled = true;
    }

    /**
     * @param bool $rawSize
     *
     * @return string|int
     */
    public function getSize($rawSize = false)
    {
        return $this->getFormatter()->formatSize($this->splFileInfo, $rawSize);
    }

    /**
     * @return string
     */
    public function getPermissions()
    {
        return $this->getFormatter()->formatPermissions($this->splFileInfo);
    }

    /**
     * @return string
     */
    public function getOwner()
    {
        return $this->getFormatter()->formatOwner($this->splFileInfo);
    }

    /**
     * @return string
     */
    public function getGroup()
    {
        return $this->getFormatter()->formatGroup($this->splFileInfo);
    }

    /**
     * @return string
     */
    public function getAccessedAt()
    {
        if ($this->splFileInfo) {
            return $this->splFileInfo->getATime();
        }

        return '';
    }

    /**
     * @return string
     */
    public function getCreatedAt()
    {
        if ($this->splFileInfo) {
            return $this->splFileInfo->getCTime();
        }

        return '';
    }

    /**
     * @return string
     */
    public function getModifiedAt()
    {
        if ($this->splFileInfo) {
            return $this->splFileInfo->getMTime();
        }

        return '';
    }

    /**
     * @param string $prepend
     *
     * @return string
     */
    public function getExtension($prepend = '')
    {
        return $this->pathHelper->getExtension($this->splFileInfo, $prepend);
    }

    /**
     * @return string
     */
    public function getDisplayName()
    {
        if (null !== $this->displayName) {
            return $this->displayName;
        }

        if ($this->splFileInfo && $this->splFileInfo->getFilename()) {
            return $this->splFileInfo->getFilename();
        }

        return '';
    }

    /**
     * @param string $filename
     *
     * @return $this
     */
    public function setDisplayName($filename)
    {
        $this->displayName = $filename;

        return $this;
    }

    /**
     * @param bool $withFilename
     *
     * @return string
     */
    public function getRelativePath($withFilename = false)
    {
        return $this->getPathHelper()
            ->getRelativePath($withFilename, $this->withFilenameDisabled, $this->splFileInfo, $this->relativePath);
    }

    /**
     * @return string
     */
    public function getOrigPath()
    {
        $origPath = '';

        if ($this->splFileInfo) {
            $origPath = $this->origBasePath . $this->getRelativePath(true);
        }

        return $origPath;
    }

    /**
     * @return string
     */
    public function getThumbnailPath()
    {
        return $this->getPathHelper()
            ->getThumbnailPath(
                $this->splFileInfo,
                $this->getPathname(),
                $this->displayName,
                $this->thumbBasePath,
                $this->relativePath
            );
    }

    /**
     * @return string
     */
    protected function getImageThumbnailPath()
    {
        return $this->getPathHelper()
            ->getImageThumbnailPath(
                $this->splFileInfo,
                $this->getPathname(),
                $this->displayName,
                $this->thumbBasePath,
                $this->relativePath
            );
    }

    /**
     * @return string
     */
    public function getTypeThumbnailPath()
    {
        return $this->getPathHelper()->getTypeThumbnailPath($this->splFileInfo);
    }

    /**
     * @param string $method
     * @param array  $args
     *
     * @return mixed
     * @throws \BadMethodCallException
     */
    public function __call($method, $args)
    {
        if ($this->splFileInfo) {
            if (method_exists($this->splFileInfo, $method)) {
                return call_user_func_array(array($this->splFileInfo, $method), $args);
            }
        }

        throw new \BadMethodCallException('Method `' . $method . '` does not exists in class `' . __CLASS__ . '`');
    }
}
