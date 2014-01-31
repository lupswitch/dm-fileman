<?php

namespace DmFileman\Service\FileManager;

use SplFileInfo;
use FilesystemIterator;
use DmFileman\Helper\FileInfo\Formatter;
use DmFileman\Helper\FileInfo\Path;

class Factory
{
    /**
     * @param string $currentDir
     * @param string $origPath
     * @param string $thumbPath
     *
     * @return FileInfo
     */
    public function getFileInfo($currentDir, $origPath, $thumbPath)
    {
        $fileInfo = new FileInfo($currentDir, $origPath, $thumbPath, new Formatter, new Path);

        return $fileInfo;
    }

    /**
     * @param string $fileName
     *
     * @return SplFileInfo
     */
    public function getSplFileInfo($fileName)
    {
        return new SplFileInfo($fileName);
    }

    /**
     * @param string $path
     *
     * @return SplFileInfo
     */
    public function getFilesystemIterator($path)
    {
        return new FilesystemIterator($path);
    }
}
