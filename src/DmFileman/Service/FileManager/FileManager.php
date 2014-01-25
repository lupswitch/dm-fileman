<?php

namespace DmFileman\Service\FileManager;

use Zend\Cache\Storage\StorageInterface;
use SplFileInfo;

class FileManager
{
    const CACHE_KEY_DIR       = 'dir';
    const CACHE_KEY_FILES     = 'files';
    const CACHE_KEY_LAST_PATH = 'last-path';

    const PATH_ORIG  = 'orig';
    const PATH_THUMB = 'thumb';

    const CONFIG_NAMESPACE = 'namespace';
    const CONFIG_BASE_DIR  = 'upload_dir';
    const CONFIG_BASE_PATH = 'upload_path';

    /** @var string */
    protected $namespace;

    /** @var string */
    protected $baseDir;

    /** @var string */
    protected $basePath;

    /** @var string */
    protected $currentPath;

    /** @var StorageInterface */
    protected $cacheStorage;

    /** @var Factory */
    protected $factory;

    /**
     * @param StorageInterface $cacheStorage
     * @param Factory          $factory
     * @param array            $config
     */
    public function __construct(StorageInterface $cacheStorage, Factory $factory, $config)
    {
        $this->cacheStorage = $cacheStorage;

        $this->factory = $factory;

        $this->namespace = $config[static::CONFIG_NAMESPACE];
        $this->baseDir   = $config[static::CONFIG_BASE_DIR];
        $this->basePath  = $config[static::CONFIG_BASE_PATH];
    }

    /**
     * @param string $currentPath
     */
    public function setCurrentPath($currentPath)
    {
        $this->currentPath = $currentPath;
    }

    /**
     * @param string $key
     *
     * @return string
     */
    protected function getKey($key)
    {
        return $this->namespace . '/' . $key;
    }

    /**
     * @param string $key
     *
     * @return mixed
     */
    public function getItem($key)
    {
        $key = $this->getKey($key);

        return $this->cacheStorage->getItem($key);
    }

    /**
     * @param string $key
     * @param bool   $newValue
     *
     * @return bool
     */
    public function setItem($key, $newValue)
    {
        $key = $this->getKey($key);

        return $this->cacheStorage->setItem($key, $newValue);
    }

    /**
     * @param string $dir
     *
     * @return string
     */
    public function getOrigDir($dir = '')
    {
        return './' . $this->baseDir . '/' . static::PATH_ORIG . $dir;
    }

    /**
     * @param string $dir
     *
     * @return string
     */
    public function getThumbDir($dir = '')
    {
        return './' . $this->baseDir . '/' . static::PATH_THUMB . $dir;
    }

    /**
     * @param string $dir
     *
     * @return string
     */
    protected function getOrigPath($dir = '')
    {
        return $this->basePath . '/' . static::PATH_ORIG . $dir;
    }

    /**
     * @param string $dir
     *
     * @return string
     */
    protected function getThumbPath($dir = '')
    {
        return $this->basePath . '/' . static::PATH_THUMB . $dir;
    }

    /**
     * @param string|null $currentDir
     *
     * @return FileInfo[];
     */
    public function getList($currentDir = null)
    {
        $currentDir = is_null($currentDir) ? $this->currentPath : $currentDir;

        $this->setItem(static::CACHE_KEY_LAST_PATH, $currentDir);

        $origPath = $this->getOrigDir($currentDir);

        /** @var FileInfo[] $list */
        $list = array();

        if (!empty($currentDir) && $currentDir != '/') {
            $upOneDir = rtrim(dirname($currentDir), '/') . '/';

            $list[] = $this->getOneUpFileInfo($upOneDir, '..');
        }

        $iterator = $this->factory->getFilesystemIterator($origPath);

        /** @var $splFileInfo SplFileInfo */
        foreach ($iterator as $splFileInfo) {
            $fileInfo = $this->factory->getFileInfo($currentDir, $this->getOrigPath(), $this->getThumbPath());

            $fileInfo->setFromSplFileInfo($splFileInfo);

            $list[] = $fileInfo;
        }

        return $list;
    }

    /**
     * @param string $dir
     * @param string $displayName
     *
     * @return FileInfo
     */
    protected function getOneUpFileInfo($dir, $displayName = '')
    {
        $oneMoreUpDir = rtrim(dirname($dir), '/') . '/';

        $fileInfo = $this->factory->getFileInfo($oneMoreUpDir, $this->getOrigPath(), $this->getThumbPath());

        $splFileInfo = $this->factory->getSplFileInfo($this->getOrigDir($dir));

        $fileInfo->setFromSplFileInfo($splFileInfo);

        if ($displayName) {
            $fileInfo->setDisplayName($displayName);
        }

        // Tweak for first level one-up
        if ($dir=='/') {
            $fileInfo->disableWithFilename();
        }

        return $fileInfo;
    }

    /**
     * @return bool;
     */
    public function refresh()
    {
        return true;
    }

    /**
     * @param string      $directoryName
     * @param string|null $currentDir
     *
     * @return bool;
     */
    public function create($directoryName, $currentDir = null)
    {
        $currentDir = is_null($currentDir) ? $this->currentPath : $currentDir;

        $newOrigDir = $this->getOrigDir($currentDir) . $directoryName;

        if (!file_exists($newOrigDir)) {
            mkdir($newOrigDir, 0777, true);
        } else {
            return false;
        }

        $newThumbDir = $this->getThumbDir($currentDir) . $directoryName;
        if (!file_exists($newThumbDir)) {
            mkdir($newThumbDir, 0777, true);
        }

        return true;
    }

    /**
     * @return bool;
     */
    public function update()
    {
        return true;
    }

    /**
     * @param string $filename
     *
     * @return bool;
     */
    public function delete($filename)
    {
        $origPath  = $this->getOrigDir($this->currentPath) . $filename;
        $thumbPath = $this->getThumbDir($this->currentPath) . $filename;

        if (is_dir($origPath)) {
            $this->deleteTree($origPath);
        } elseif (is_file($origPath)) {
            unlink($origPath);
        }

        if (is_dir($thumbPath)) {
            $this->deleteTree($thumbPath);
        } elseif (is_file($thumbPath)) {
            unlink($thumbPath);
        }

        return true;
    }

    /**
     * @see http://de2.php.net/manual/en/function.rmdir.php
     *
     * @param string $dir
     *
     * @return bool
     */
    public function deleteTree($dir)
    {
        $files = array_diff(scandir($dir), array('.','..'));

        foreach ($files as $file) {
            if (is_dir("$dir/$file")) {
                $this->deleteTree("$dir/$file");
            } else {
                unlink("$dir/$file");
            }
        }

        return rmdir($dir);
    }

    /**
     * @return bool;
     */
    public function upload()
    {
        return true;
    }
}
