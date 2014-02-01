<?php

namespace DmFilemanTest\Controller;

use DmFileman\Controller\UploadFileController;
use DmTest\Controller\TestCaseTrait;

class UploadFileControllerTest extends \PHPUnit_Framework_TestCase
{
    use TestCaseTrait;

    /** @var UploadFileController */
    protected $sut;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $fileManagerMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $uploadFileFormMock;

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

        $this->uploadFileFormMock = $this->getMockBuilder('DmFileman\Form\UploadFileForm')
            ->setMethods(['build', 'getMessages', 'getInputFilter', 'isValid', 'getData'])
            ->getMock();

        $this->thumbnailerMock = $this->getMockBuilder('DmFileman\Service\Thumbnailer\Thumbnailer')
            ->setMethods(['resize'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->userTextMock = $this->getMockBuilder('DmFileman\View\Helper\UserText')
            ->setMethods(['getMessage'])
            ->getMock();

        $this->sut = new UploadFileController(
            $this->fileManagerMock,
            $this->uploadFileFormMock,
            $this->thumbnailerMock,
            $this->userTextMock
        );
    }

    /**
     * @covers DmFileman\Controller\UploadFileController
     */
    public function testUploadActionAddsErrorMessageToFlashMessengerByDefault()
    {
        $responseMock = $this->getResponseMock();

        $flashMessengerMock = $this->getFlashMessengerPluginMock(0, 1);
        $redirectMock       = $this->getRedirectPluginMock($responseMock);
        $pluginMock         = $this->getPluginMock($flashMessengerMock, $redirectMock);

        $this->sut->setPluginManager($pluginMock);

        $this->uploadFileFormMock
            ->expects($this->once())
            ->method('getMessages')
            ->will($this->returnValue([]));

        $this->sut->setCurrentPath('');

        $actualResult = $this->sut->uploadAction();

        $this->assertEquals($responseMock, $actualResult);
    }

    /**
     * @covers DmFileman\Controller\UploadFileController
     */
    public function testUploadActionAddsErrorMessageToFlashMessengerWhenFormIsNotValid()
    {
        $responseMock = $this->getResponseMock();

        $flashMessengerMock = $this->getFlashMessengerPluginMock(0, 1);
        $redirectMock       = $this->getRedirectPluginMock($responseMock);
        $pluginMock         = $this->getPluginMock($flashMessengerMock, $redirectMock);

        $this->sut->setPluginManager($pluginMock);

        $requestMock = $this->getRequestMock(new \SplFixedArray(0), new \SplFixedArray(0));
        $this->sut->setRequest($requestMock);

        $inputFilterMock = $this->getMock('Zend\InputFilter\InputFilter', ['init', 'setCurrentDir']);
        $inputFilterMock->expects($this->any())->method('setCurrentDir')->will($this->returnSelf());

        $this->uploadFileFormMock
            ->expects($this->once())
            ->method('getInputFilter')
            ->will($this->returnValue($inputFilterMock));
        $this->uploadFileFormMock
            ->expects($this->once())
            ->method('isValid')
            ->will($this->returnValue(false));
        $this->uploadFileFormMock
            ->expects($this->once())
            ->method('getMessages')
            ->will($this->returnValue([]));

        $this->sut->setCurrentPath('');

        $actualResult = $this->sut->uploadAction();

        $this->assertEquals($responseMock, $actualResult);
    }

    /**
     * @covers DmFileman\Controller\UploadFileController
     */
    public function testUploadActionAddsUploadErrorsToFlashMessenger()
    {
        $responseMock = $this->getResponseMock();

        $flashMessengerMock = $this->getFlashMessengerPluginMock(0, 3);
        $redirectMock       = $this->getRedirectPluginMock($responseMock);
        $pluginMock         = $this->getPluginMock(
            $flashMessengerMock,
            $flashMessengerMock,
            $flashMessengerMock,
            $redirectMock
        );

        $this->sut->setPluginManager($pluginMock);

        $requestMock = $this->getRequestMock(new \SplFixedArray(0), new \SplFixedArray(0));
        $this->sut->setRequest($requestMock);

        $inputFilterMock = $this->getMock('Zend\InputFilter\InputFilter', ['init', 'setCurrentDir']);
        $inputFilterMock->expects($this->any())->method('setCurrentDir')->will($this->returnSelf());

        $this->uploadFileFormMock
            ->expects($this->once())
            ->method('getInputFilter')
            ->will($this->returnValue($inputFilterMock));
        $this->uploadFileFormMock
            ->expects($this->once())
            ->method('isValid')
            ->will($this->returnValue(false));
        $this->uploadFileFormMock
            ->expects($this->once())
            ->method('getMessages')
            ->will($this->returnValue([['foo'], ['bar']]));

        $this->sut->setCurrentPath('');

        $actualResult = $this->sut->uploadAction();

        $this->assertEquals($responseMock, $actualResult);
    }

    /**
     * @covers DmFileman\Controller\UploadFileController
     */
    public function testUploadActionAddsSuccessMessageToFlashMessengerWhenFormIsValid()
    {
        $responseMock = $this->getResponseMock();

        $flashMessengerMock = $this->getFlashMessengerPluginMock(1, 0);
        $redirectMock       = $this->getRedirectPluginMock($responseMock);
        $pluginMock         = $this->getPluginMock($flashMessengerMock, $redirectMock);

        $this->sut->setPluginManager($pluginMock);

        $requestMock = $this->getRequestMock(new \SplFixedArray(0), new \SplFixedArray(0));
        $this->sut->setRequest($requestMock);

        $inputFilterMock = $this->getMock('Zend\InputFilter\InputFilter', ['init', 'setCurrentDir']);
        $inputFilterMock->expects($this->any())->method('setCurrentDir')->will($this->returnSelf());

        $this->uploadFileFormMock
            ->expects($this->once())
            ->method('getInputFilter')
            ->will($this->returnValue($inputFilterMock));
        $this->uploadFileFormMock
            ->expects($this->once())
            ->method('isValid')
            ->will($this->returnValue(true));
        $this->uploadFileFormMock
            ->expects($this->once())
            ->method('getData')
            ->will($this->returnValue(['file' => ['type' => '']]));

        $this->sut->setCurrentPath('');

        $actualResult = $this->sut->uploadAction();

        $this->assertEquals($responseMock, $actualResult);
    }
}
