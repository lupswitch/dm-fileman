<?php

namespace DmFilemanTest\Form;

use DmFileman\Form\UploadFileForm as Form;

class UploadFileFormTest extends \PHPUnit_Framework_TestCase
{
    /** @var Form */
    protected $sut;

    public function setUp()
    {
        $this->sut = new Form();
    }

    /**
     * @covers DmFileman\Form\UploadFileForm
     */
    public function testBuild()
    {
        $this->sut->build();

        $this->assertInstanceOf('Zend\Form\Element\File', $this->sut->get(Form::FILE));
        $this->assertInstanceOf('Zend\Form\Element\Csrf', $this->sut->get(Form::SECURITY));
    }
}
