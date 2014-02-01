<?php

namespace DmFilemanTest\Controller;

use DmFileman\Controller\DeleteFileController;
use DmTest\Controller\TestCaseTrait;

class DeleteFileControllerTest extends \PHPUnit_Framework_TestCase
{
    use TestCaseTrait;

    /** @var DeleteFileController */
    protected $sut;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $fileManagerMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $deleteFileFormMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $thumbnailerMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $userTextMock;

    /**
     * @covers DmFileman\Controller\DeleteFileController
     */
    public function setUp()
    {
        $this->fileManagerMock = $this->getMockBuilder('DmFileman\Service\FileManager\FileManager')
            ->setMethods(['setCurrentPath', 'delete'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->deleteFileFormMock = $this->getMockBuilder('DmFileman\Form\DeleteFileForm')
            ->setMethods(['build', 'getInputFilter', 'isValid', 'getData'])
            ->getMock();

        $this->thumbnailerMock = $this->getMockBuilder('DmFileman\Service\Thumbnailer\Thumbnailer')
            ->setMethods(['resize'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->userTextMock = $this->getMockBuilder('DmFileman\View\Helper\UserText')
            ->setMethods(['getMessage'])
            ->getMock();

        $this->sut = new DeleteFileController(
            $this->fileManagerMock,
            $this->deleteFileFormMock,
            $this->thumbnailerMock,
            $this->userTextMock
        );
    }

    /**
     * @covers DmFileman\Controller\DeleteFileController
     */
    public function testDeleteActionAddsErrorMessageToFlashMessengerByDefault()
    {
        $responseMock = $this->getResponseMock();

        $flashMessengerMock = $this->getFlashMessengerPluginMock(0, 1);
        $redirectMock       = $this->getRedirectPluginMock($responseMock);
        $pluginMock         = $this->getPluginMock($flashMessengerMock, $redirectMock);

        $this->sut->setPluginManager($pluginMock);

        $actualResult = $this->sut->deleteAction();

        $this->assertEquals($responseMock, $actualResult);
    }

    /**
     * @covers DmFileman\Controller\DeleteFileController
     */
    public function testDeleteActionAddsErrorMessageToFlashMessengerWhenFormIsNotValid()
    {
        $responseMock = $this->getResponseMock();

        $flashMessengerMock = $this->getFlashMessengerPluginMock(0, 1);
        $redirectMock       = $this->getRedirectPluginMock($responseMock);
        $pluginMock         = $this->getPluginMock($flashMessengerMock, $redirectMock);

        $this->sut->setPluginManager($pluginMock);

        $requestMock = $this->getRequestMock([]);
        $this->sut->setRequest($requestMock);

        $inputFilterMock = $this->getMock('Zend\InputFilter\InputFilter', ['init']);
        $this->deleteFileFormMock
            ->expects($this->once())
            ->method('getInputFilter')
            ->will($this->returnValue($inputFilterMock));
        $this->deleteFileFormMock
            ->expects($this->once())
            ->method('isValid')
            ->will($this->returnValue(false));

        $actualResult = $this->sut->deleteAction();

        $this->assertEquals($responseMock, $actualResult);
    }

    /**
     * @covers DmFileman\Controller\DeleteFileController
     */
    public function testDeleteActionAddsSuccessMessageToFlashMessengerWhenFormIsValid()
    {
        $responseMock = $this->getResponseMock();

        $flashMessengerMock = $this->getFlashMessengerPluginMock(1, 0);
        $redirectMock       = $this->getRedirectPluginMock($responseMock);
        $pluginMock         = $this->getPluginMock($flashMessengerMock, $redirectMock);

        $this->sut->setPluginManager($pluginMock);

        $requestMock = $this->getRequestMock([]);
        $this->sut->setRequest($requestMock);

        $inputFilterMock = $this->getMock('Zend\InputFilter\InputFilter', ['init']);
        $this->deleteFileFormMock
            ->expects($this->once())
            ->method('getInputFilter')
            ->will($this->returnValue($inputFilterMock));
        $this->deleteFileFormMock
            ->expects($this->once())
            ->method('isValid')
            ->will($this->returnValue(true));
        $this->deleteFileFormMock
            ->expects($this->once())
            ->method('getData')
            ->will($this->returnValue(['name' => 'foo']));

        $this->fileManagerMock->expects($this->once())->method('delete')->will($this->returnValue(true));

        $this->sut->setCurrentPath('');

        $actualResult = $this->sut->deleteAction();

        $this->assertEquals($responseMock, $actualResult);
    }
}
