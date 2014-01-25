<?php

namespace DmFileman\Helper;

class Options
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
     * @return int
     */
    public function getPeriodInMinutes()
    {
        return isset($this->options['app']['cronjob']['period_in_minutes'])
            ? (int)$this->options['app']['cronjob']['period_in_minutes']
            : 30;
    }

    /**
     * @return int
     */
    public function getQueueSize()
    {
        return isset($this->options['app']['queue']['size'])
            ? (int)$this->options['app']['queue']['size']
            : 100;
    }

    /**
     * @return int
     */
    public function isRegistrationEnabled()
    {
        return isset($this->options['zfcuser']['enable_registration'])
            ? (int)$this->options['zfcuser']['enable_registration']
            : true;
    }

    /**
     * @return string
     */
    public function getTinyMceLang()
    {
        return isset($this->options['app']['tinyMceLang'])
            ? $this->options['app']['tinyMceLang']
            : '';
    }

    /**
     * @return string
     */
    public function getFilePathBase()
    {
        return isset($this->options['app']['filePathBase'])
            ? $this->options['app']['filePathBase']
            : '';
    }

    /**
     * @return string
     */
    public function getFileUrlBase()
    {
        return isset($this->options['app']['fileUrlBase'])
            ? $this->options['app']['fileUrlBase']
            : '';
    }

    /**
     * @return array
     */
    public function getLogApp()
    {
        return isset($this->options['log']['Log\App'])
            ? (array)$this->options['log']['Log\App']
            : array();
    }

    /**
     * @return array
     */
    public function getLogExc()
    {
        return isset($this->options['log']['Log\Exc'])
            ? (array)$this->options['log']['Log\Exc']
            : $this->getLogApp();
    }

    /**
     * @return array
     */
    public function getThrottle()
    {
        return isset($this->options['app']['throttle'])
            ? (array)$this->options['app']['throttle']
            : [];
    }

    /**
     * @return bool
     */
    public function hasSystemMailer()
    {
        if (isset($this->options['sxmail']['configs']['system'])) {
            return true;
        }

        return false;
    }

    /**
     * @return array
     */
    public function getCacheStorage()
    {
        return isset($this->options['cache-storage'])
            ? (array)$this->options['cache-storage']
            : [];
    }
}
