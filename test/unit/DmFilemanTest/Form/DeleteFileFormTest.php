<?php

namespace DmFilemanTest\Form;

use DmFileman\Form\DeleteFileForm as Form;

class DeleteFileFormTest extends \PHPUnit_Framework_TestCase
{
    /** @var Form */
    protected $sut;

    public function setUp()
    {
        $this->sut = new Form();
    }

    /**
     * @covers DmFileman\Form\DeleteFileForm
     */
    public function testBuild()
    {
        $this->sut->build();

        $this->assertInstanceOf('Zend\Form\Element\Text', $this->sut->get(Form::NAME));
        $this->assertInstanceOf('Zend\Form\Element\Csrf', $this->sut->get(Form::SECURITY));
    }
}
