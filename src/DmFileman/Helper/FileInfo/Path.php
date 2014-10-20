<?php

namespace DmFileman\Helper\FileInfo;

use SplFileInfo;

class Path
{
    /** @var array */
    protected $imageExtensions = ['png', 'jpg', 'jpeg', 'gif'];

    /** @var string */
    protected $fileTypeBasePath = '/img/filemanager/';

    protected $thumbsByType = [
        'compressed.png'  => ['zip', 'rar', '7z', 'gz', 'cab'],
        'css.png'         => ['css'],
        'developer.png'   => [
            'c', 'd', 'e', 'f', 'go',
            'h', 'hpp', 'java', 'lisp', 'm',
            'php', 'php4', 'php5', 'phtml', 'pl',
            'pm', 'prg', 'py', 'r', 'rb',
            'vb', 'bat', 'coffee', 'erb', 'js',
            'lua', 'pl', 'sh',
        ],
        'excel.png'       => ['xls'],
        'fileicon_bg.png' => ['exe'],
        'fireworks.png'   => [],
        'flash.png'       => ['swf', 'flv', 'fla'],
        'folder.png'      => [],
        'html.png'        => ['htm', 'html'],
        'illustrator.png' => ['ai'],
        'image.png'       => ['jpg', 'tif', 'bmp', 'gif', 'png', 'jpeg', 'ico', 'svg'],
        'keynote.png'     => [],
        'movie.png'       => ['wmv', 'avi', 'mpg', 'mov', 'mp4', 'm4b', 'divx', 'm4v'],
        'music.png'       => [
            'mp3', 'wav', 'wma', 'rmvb', 'aif',
            'm4a', 'mid', 'ogg', 'ram', 'mp2',
            'flac', 'tta', 'la', 'ape', 'rka',
            'au', 'aiff', 'aif', 'aifc', 'bwf',
            'vqf', 'ra', 'rm', 'vox', 'aup',
            'asx', 'm3u', 'pls', 'xpl', 'zpl',
            'als', 'aup', 'band', 'cel', 'cpr',
            'cwp', 'drm', 'mmr', 'npr', 'omfi',
            'ses', 'sfl', 'sng', 'stf', 'snd',
            'syn', 'flp'
        ],
        'numbers.png'     => ['mml', 'odf', 'sxm'],
        'pages.png'       => [],
        'pdf.png'         => ['pdf'],
        'photoshop.png'   => ['psd'],
        'powerpoint.png'  => ['ppt', 'pps'],
        'text.png'        => ['txt', 'log'],
        'word.png'        => ['doc', 'rtf'],
    ];

    /**
     * @param bool             $withFilename
     * @param bool             $withFilenameDisabled
     * @param SplFileInfo|null $splFileInfo
     * @param string           $relativePath
     *
     * @return string
     */
    public function getRelativePath(
        $withFilename = false,
        $withFilenameDisabled = false,
        SplFileInfo $splFileInfo = null,
        $relativePath = ''
    ) {
        if ($splFileInfo && $withFilename && !$withFilenameDisabled) {
            $relativePath .= $splFileInfo->getFilename();

            if ($splFileInfo->isDir()) {
                $relativePath .= '/';
            }
        }

        return $relativePath;
    }

    /**
     * @param SplFileInfo $splFileInfo
     * @param string      $pathname
     * @param string      $displayName
     * @param string      $thumbBasePath
     * @param string      $relativePath
     *
     * @return string
     */
    public function getThumbnailPath(
        SplFileInfo $splFileInfo = null,
        $pathname = '',
        $displayName = '',
        $thumbBasePath = '',
        $relativePath = ''
    ) {
        if (empty($splFileInfo)) {
            return '';
        }

        if ($splFileInfo->isFile() && in_array($this->getExtension($splFileInfo), $this->imageExtensions)) {
            $thumbnailPath = $this->getImageThumbnailPath(
                $splFileInfo,
                $pathname,
                $displayName,
                $thumbBasePath,
                $relativePath
            );

            if ($thumbnailPath) {
                return $thumbnailPath;
            }
        }

        return $this->getTypeThumbnailPath($splFileInfo);
    }

    /**
     * @param SplFileInfo $splFileInfo
     * @param string      $prepend
     *
     * @return string
     */
    public function getExtension(SplFileInfo $splFileInfo = null, $prepend = '')
    {
        if (!empty($splFileInfo) && $splFileInfo->getExtension()) {
            return $prepend . $splFileInfo->getExtension();
        }

        return '';
    }

    /**
     * @param SplFileInfo $splFileInfo
     * @param string      $pathname
     * @param string      $displayName
     * @param string      $thumbBasePath
     * @param string      $relativePath
     *
     * @return string
     */
    public function getImageThumbnailPath(
        SplFileInfo $splFileInfo = null,
        $pathname = '',
        $displayName = '',
        $thumbBasePath = '',
        $relativePath = ''
    ) {
        if (!empty($splFileInfo)) {
            $thumbPathname = str_replace('orig', 'thumb', $pathname);

            if (file_exists($thumbPathname)) {
                return $thumbBasePath . $relativePath . $displayName;
            }
        }

        return '';
    }

    /**
     * @param SplFileInfo $splFileInfo
     *
     * @return string
     */
    public function getTypeThumbnailPath(SplFileInfo $splFileInfo = null)
    {
        if (!empty($splFileInfo)) {
            if ($splFileInfo->isDir()) {
                return $this->fileTypeBasePath . 'folder.png';
            }

            $extension = $this->getExtension($splFileInfo);

            foreach ($this->thumbsByType as $thumbnail => $extensions) {
                if (in_array($extension, $extensions)) {
                    return $this->fileTypeBasePath . $thumbnail;
                }
            }
        }

        return $this->fileTypeBasePath . 'fileicon_bg.png';
    }
}
