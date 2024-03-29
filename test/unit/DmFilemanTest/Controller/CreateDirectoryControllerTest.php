<?php

namespace DmFilemanTest\Controller;

use DmFileman\Controller\CreateDirectoryController;
use DmTest\Controller\PluginMockFactory;

class CreateDirectoryControllerTest extends \PHPUnit_Framework_TestCase
{
    /** @var CreateDirectoryController */
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

    /** @var PluginMockFactory */
    protected $mockFactory;

    public function setUp()
    {
        $this->fileManagerMock = $this->getMockBuilder('DmFileman\Service\FileManager\FileManager')
            ->setMethods(['setCurrentPath', 'create'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->createDirFormMock = $this->getMockBuilder('DmFileman\Form\CreateDirectoryForm')
            ->setMethods(['setData', 'getInputFilter', 'getData', 'isValid'])
            ->getMock();

        $this->thumbnailerMock = $this->getMockBuilder('DmFileman\Service\Thumbnailer\Thumbnailer')
            ->setMethods(['resize'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->userTextMock = $this->getMockBuilder('DmCommon\View\Helper\UserText')
            ->setMethods(['getMessage'])
            ->getMock();

        $this->sut = new CreateDirectoryController(
            $this->fileManagerMock,
            $this->createDirFormMock,
            $this->thumbnailerMock,
            $this->userTextMock
        );

        $this->mockFactory = new PluginMockFactory($this);
    }

    /**
     * @covers DmFileman\Controller\CreateDirectoryController
     */
    public function testCreateActionAddsErrorMessageToFlashMessengerByDefault()
    {
        $responseMock = $this->mockFactory->getResponseMock();

        $flashMessengerMock = $this->mockFactory->getFlashMessengerPluginMock(0, 1);
        $redirectMock       = $this->mockFactory->getRedirectPluginMock($responseMock);
        $pluginMock         = $this->mockFactory->getPluginMock($flashMessengerMock, $redirectMock);

        $this->sut->setPluginManager($pluginMock);

        $actualResult = $this->sut->createAction();

        $this->assertEquals($responseMock, $actualResult);
    }

    /**
     * @covers DmFileman\Controller\CreateDirectoryController
     */
    public function testCreateActionAddsErrorMessageToFlashMessengerWhenFormIsNotValid()
    {
        $responseMock = $this->mockFactory->getResponseMock();

        $flashMessengerMock = $this->mockFactory->getFlashMessengerPluginMock(0, 1);
        $redirectMock       = $this->mockFactory->getRedirectPluginMock($responseMock);
        $pluginMock         = $this->mockFactory->getPluginMock($flashMessengerMock, $redirectMock);

        $this->sut->setPluginManager($pluginMock);

        $requestMock = $this->mockFactory->getRequestMock([]);
        $this->sut->setRequest($requestMock);

        $inputFilterMock = $this->getMock('Zend\InputFilter\InputFilter', ['init']);
        $this->createDirFormMock
            ->expects($this->once())
            ->method('getInputFilter')
            ->will($this->returnValue($inputFilterMock));
        $this->createDirFormMock
            ->expects($this->once())
            ->method('isValid')
            ->will($this->returnValue(false));

        $actualResult = $this->sut->createAction();

        $this->assertEquals($responseMock, $actualResult);
    }

    /**
     * @covers DmFileman\Controller\CreateDirectoryController
     */
    public function testCreateActionAddsSuccessMessageToFlashMessengerWhenFormIsValid()
    {
        $responseMock = $this->mockFactory->getResponseMock();

        $flashMessengerMock = $this->mockFactory->getFlashMessengerPluginMock(1, 0);
        $redirectMock       = $this->mockFactory->getRedirectPluginMock($responseMock);
        $pluginMock         = $this->mockFactory->getPluginMock($flashMessengerMock, $redirectMock);

        $this->sut->setPluginManager($pluginMock);

        $requestMock = $this->mockFactory->getRequestMock([]);
        $this->sut->setRequest($requestMock);

        $inputFilterMock = $this->getMock('Zend\InputFilter\InputFilter', ['init']);
        $this->createDirFormMock
            ->expects($this->once())
            ->method('getInputFilter')
            ->will($this->returnValue($inputFilterMock));
        $this->createDirFormMock
            ->expects($this->once())
            ->method('isValid')
            ->will($this->returnValue(true));
        $this->createDirFormMock
            ->expects($this->once())
            ->method('getData')
            ->will($this->returnValue(['directoryName' => 'foo']));

        $this->fileManagerMock->expects($this->once())->method('create')->will($this->returnValue(true));

        $this->sut->setCurrentPath('');

        $actualResult = $this->sut->createAction();

        $this->assertEquals($responseMock, $actualResult);
    }
}
