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
}
