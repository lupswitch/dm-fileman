<?php

namespace DmFileman\Helper;

use DmCommon\Helper\Options as DmCommonOptions;

class Options extends DmCommonOptions
{
    /** @var array */
    protected $options;

    /**
     * @param array $options
     */
    public function __construct(array $options)
    {
        $this->options = $options;
    }

    /**
     * @return string
     */
    public function getFileManagerOptions()
    {
        return isset($this->options['dm-fileman']['filemanager'])
            ? $this->options['dm-fileman']['filemanager']
            : '';
    }

    /**
     * @return string
     */
    public function getThumbsOptions()
    {
        return isset($this->options['dm-fileman']['thumbs'])
            ? $this->options['dm-fileman']['thumbs']
            : '';
    }

    /**
     * @return array
     */
    public function getExtensions()
    {
        return isset($this->options['dm-fileman']['file_upload']['extensions'])
            ? $this->options['dm-fileman']['file_upload']['extensions']
            : array();
    }

    /**
     * @return int
     */
    public function getMaxSize()
    {
        return isset($this->options['dm-fileman']['file_upload']['file_upload'])
            ? $this->options['dm-fileman']['file_upload']['file_upload']
            : 0;
    }
}
