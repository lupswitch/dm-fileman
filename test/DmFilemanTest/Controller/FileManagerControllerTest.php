<?php

namespace DmFilemanTest\Controller;

use DmFileman\Controller\FileManagerController;

class FileManagerControllerTest extends \PHPUnit_Framework_TestCase
{
    /** @var FileManagerController */
    protected $sut;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $fileManagerMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $createDirFormMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $uploadFileFormMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $deleteFileFormMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $thumbnailerMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $userTextMock;

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

        $this->thumbnailerMock = $this->getMockBuilder('DmFileman\Service\Thumbnailer\Thumbnailer')
            ->setMethods(['resize'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->userTextMock = $this->getMockBuilder('DmFileman\View\Helper\UserText')
            ->setMethods(['getMessage'])
            ->getMock();

        $this->sut = new FileManagerController(
            $this->fileManagerMock,
            $this->createDirFormMock,
            $this->uploadFileFormMock,
            $this->deleteFileFormMock,
            $this->thumbnailerMock,
            $this->userTextMock
        );
    }

    /**
     * @covers DmFileman\Controller\FileManagerController
     */
    public function testIndexAction()
    {
        $this->markTestIncomplete();
    }

    /**
     * @covers DmFileman\Controller\FileManagerController
     */
    public function testListAction()
    {
        $this->markTestIncomplete();
    }

    /**
     * @covers DmFileman\Controller\FileManagerController
     */
    public function testRefreshAction()
    {
        $this->markTestIncomplete();
    }

    /**
     * @covers DmFileman\Controller\FileManagerController
     */
    public function testCreateAction()
    {
        $this->markTestIncomplete();
    }

    /**
     * @covers DmFileman\Controller\FileManagerController
     */
    public function testDeleteAction()
    {
        $this->markTestIncomplete();
    }

    /**
     * @covers DmFileman\Controller\FileManagerController
     */
    public function testUploadAction()
    {
        $this->markTestIncomplete();
    }
}
