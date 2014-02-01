<?php

namespace DmFileman\Controller;

trait CurrentPathTrait
{
    /** @var string */
    private $currentPath;

    /**
     * @return string
     */
    private function getCurrentPath()
    {
        if (null === $this->currentPath) {
            $currentPath = $this->params('dir');

            $this->currentPath = $currentPath ? urldecode($currentPath) : '/';
        }

        return $this->currentPath;
    }

    /**
     * @param string $currentPath
     */
    public function setCurrentPath($currentPath)
    {
        $this->currentPath = $currentPath;
    }
}
 