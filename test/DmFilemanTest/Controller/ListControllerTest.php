<?php

namespace DmFilemanTest\Controller;

use DmFileman\Controller\ListController;

class ListControllerTest extends \PHPUnit_Framework_TestCase
{
    /** @var ListController */
    protected $sut;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $fileManagerMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $createDirFormMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $uploadFileFormMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $deleteFileFormMock;

    public function setUp()
    {
        $this->fileManagerMock = $this->getMockBuilder('DmFileman\Service\FileManager\FileManager')
            ->setMethods(['setCurrentPath', 'getOrigDir', 'getList'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->createDirFormMock = $this->getMockBuilder('DmFileman\Form\CreateDirectoryForm')
            ->setMethods(['build', 'getInputFilter'])
            ->getMock();

        $this->uploadFileFormMock = $this->getMockBuilder('DmFileman\Form\UploadFileForm')
            ->setMethods(['build', 'getMessages', 'getInputFilter'])
            ->getMock();

        $this->deleteFileFormMock = $this->getMockBuilder('DmFileman\Form\DeleteFileForm')
            ->setMethods(['build', 'getInputFilter'])
            ->getMock();

        $this->sut = new ListController(
            $this->fileManagerMock,
            $this->createDirFormMock,
            $this->uploadFileFormMock,
            $this->deleteFileFormMock
        );
    }

    /**
     * @covers DmFileman\Controller\ListController
     */
    public function testIndexAction()
    {
        $this->markTestIncomplete();
    }

    /**
     * @covers DmFileman\Controller\ListController
     */
    public function testListAction()
    {
        $this->markTestIncomplete();
    }

    /**
     * @covers DmFileman\Controller\ListController
     */
    public function testRefreshAction()
    {
        $this->markTestIncomplete();
    }
}
