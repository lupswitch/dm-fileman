<?php

namespace DmFileman\InputFilter;

use Zend\InputFilter\FileInput;
use Zend\InputFilter\InputFilter;

class UploadFile extends InputFilter
{
    /** @var string */
    protected $currentDir;

    /** @var FileInput */
    protected $fileInput;

    /** @var int */
    protected $maxSize = 0;

    /** @var array|string */
    protected $extensions = array();

    /**
     * @param $currentDir
     *
     * @return $this
     */
    public function setCurrentDir($currentDir)
    {
        $this->currentDir = $currentDir;

        return $this;
    }

    /**
     * @param int $maxSize
     */
    public function setMaxSize($maxSize)
    {
        $this->maxSize = $maxSize;
    }

    /**
     * @param array|string $extensions
     */
    public function setExtensions($extensions)
    {
        $this->extensions = $extensions;
    }

    /**
     * @param FileInput $fileInput
     */
    public function setFileInput(FileInput $fileInput)
    {
        $this->fileInput = $fileInput;
    }

    /**
     * @return FileInput
     */
    protected function getFileInput()
    {
        if (null == $this->fileInput) {
            $this->fileInput = new FileInput;
        }

        return $this->fileInput;
    }

    public function init()
    {
        $fileInput = $this->getFileInput();

        $fileInput->setName('file');
        $fileInput->setRequired(true);

        $this->addFileValidation($fileInput);
        $this->addFileFilter($fileInput);

        $this->add($fileInput);

        $this->add(
            array(
                'name'       => 'security',
                'required'   => true,
            )
        );
    }

    /**
     * @param FileInput $fileInput
     */
    private function addFileValidation(FileInput $fileInput)
    {
        if ($this->maxSize) {
            $fileInput->getValidatorChain()->attachByName('filesize', array('max' => $this->maxSize));
        }

        if (count($this->extensions)) {
            $fileInput->getValidatorChain()->attachByName('fileextension', array('extension' => $this->extensions));
        }
    }

    /**
     * @param FileInput $fileInput
     */
    private function addFileFilter(FileInput $fileInput)
    {
        if (null !== $this->currentDir) {
            $fileInput->getFilterChain()->attachByName(
                'filerenameupload',
                array(
                    'target'               => $this->currentDir,
                    'overwrite'            => true,
                    'randomize'            => false,
                    'use_upload_name'      => true,
                    'use_upload_extension' => true,
                )
            );
        }
    }
}
