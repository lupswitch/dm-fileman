<?php

namespace DmFilemanTest\Controller;

use DmFileman\Controller\ListController;
use DmTest\Controller\PluginMockFactory;

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

    /** @var PluginMockFactory */
    protected $mockFactory;

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

        $this->mockFactory = new PluginMockFactory($this);
    }

    /**
     * @covers DmFileman\Controller\ListController
     */
    public function testIndexActionRedirectsToListPage()
    {
        $responseMock = $this->mockFactory->getResponseMock();

        $redirectMock = $this->mockFactory->getRedirectPluginMock($responseMock);
        $pluginMock   = $this->mockFactory->getPluginMock($redirectMock);

        $this->sut->setPluginManager($pluginMock);

        $actualResult = $this->sut->indexAction();

        $this->assertEquals($responseMock, $actualResult);
    }

    /**
     * @covers DmFileman\Controller\ListController
     */
    public function testListActionReturnsViewModel()
    {
        $this->fileManagerMock->expects($this->once())->method('getList');

        $this->sut->setCurrentPath('');

        $actualResult = $this->sut->listAction();

        $this->assertInstanceOf('Zend\View\Model\ViewModel', $actualResult);
    }

    /**
     * @covers DmFileman\Controller\ListController
     */
    public function testRefreshActionRedirectsToListPage()
    {
        $responseMock = $this->mockFactory->getResponseMock();

        $redirectMock = $this->mockFactory->getRedirectPluginMock($responseMock);
        $pluginMock   = $this->mockFactory->getPluginMock($redirectMock);

        $this->sut->setPluginManager($pluginMock);

        $actualResult = $this->sut->refreshAction();

        $this->assertEquals($responseMock, $actualResult);
    }
}
