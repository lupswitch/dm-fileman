<?php

namespace DmFilemanTest\Form;

use DmFileman\Form\CreateDirectoryForm as Form;

class CreateDirectoryFormTest extends \PHPUnit_Framework_TestCase
{
    /** @var Form */
    protected $sut;

    public function setUp()
    {
        $this->sut = new Form();
    }

    /**
     * @covers DmFileman\Form\CreateDirectoryForm
     */
    public function testBuild()
    {
        $this->sut->build();

        $this->assertInstanceOf('Zend\Form\Element\Text', $this->sut->get(Form::DIRECTORY));
        $this->assertInstanceOf('Zend\Form\Element\Csrf', $this->sut->get(Form::SECURITY));
    }
}
