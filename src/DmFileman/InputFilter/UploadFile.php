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
        // File Input
        $fileInput = $this->getFileInput();

        $fileInput->setName('file');
        $fileInput->setRequired(true);

        $fileInput->getValidatorChain()->attachByName('filesize', array('max' => 20480000));

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

        $this->add($fileInput);

        $this->add(
            array(
                'name'       => 'security',
                'required'   => true,
                /* csrf is autoadded
                'validators' => array(
                    array(
                        'name'    => 'Csrf',
                        'options' => array(
                        ),
                    ),
                ),*/
            )
        );
    }
}
