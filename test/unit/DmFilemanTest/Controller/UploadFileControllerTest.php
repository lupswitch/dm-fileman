<?php

namespace DmFilemanTest\Controller;

use DmFileman\Controller\UploadFileController;
use DmTest\Controller\PluginMockFactory;

class UploadFileControllerTest extends \PHPUnit_Framework_TestCase
{
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

    /** @var PluginMockFactory */
    protected $mockFactory;

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
            ->setMethods(['resizeOrigImage'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->userTextMock = $this->getMockBuilder('DmCommon\View\Helper\UserText')
            ->setMethods(['getMessage'])
            ->getMock();

        $this->sut = new UploadFileController(
            $this->fileManagerMock,
            $this->uploadFileFormMock,
            $this->thumbnailerMock,
            $this->userTextMock
        );

        $this->mockFactory = new PluginMockFactory($this);
    }

    /**
     * @covers DmFileman\Controller\UploadFileController
     */
    public function testUploadActionAddsErrorMessageToFlashMessengerByDefault()
    {
        $responseMock = $this->mockFactory->getResponseMock();

        $flashMessengerMock = $this->mockFactory->getFlashMessengerPluginMock(0, 1);
        $redirectMock       = $this->mockFactory->getRedirectPluginMock($responseMock);
        $pluginMock         = $this->mockFactory->getPluginMock($flashMessengerMock, $redirectMock);

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
        $responseMock = $this->mockFactory->getResponseMock();

        $flashMessengerMock = $this->mockFactory->getFlashMessengerPluginMock(0, 1);
        $redirectMock       = $this->mockFactory->getRedirectPluginMock($responseMock);
        $pluginMock         = $this->mockFactory->getPluginMock($flashMessengerMock, $redirectMock);

        $this->sut->setPluginManager($pluginMock);

        $requestMock = $this->mockFactory->getRequestMock(new \SplFixedArray(0), new \SplFixedArray(0));
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
        $responseMock = $this->mockFactory->getResponseMock();

        $flashMessengerMock = $this->mockFactory->getFlashMessengerPluginMock(0, 3);
        $redirectMock       = $this->mockFactory->getRedirectPluginMock($responseMock);
        $pluginMock         = $this->mockFactory->getPluginMock(
            $flashMessengerMock,
            $flashMessengerMock,
            $flashMessengerMock,
            $redirectMock
        );

        $this->sut->setPluginManager($pluginMock);

        $requestMock = $this->mockFactory->getRequestMock(new \SplFixedArray(0), new \SplFixedArray(0));
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
        $responseMock = $this->mockFactory->getResponseMock();

        $flashMessengerMock = $this->mockFactory->getFlashMessengerPluginMock(1, 0);
        $redirectMock       = $this->mockFactory->getRedirectPluginMock($responseMock);
        $pluginMock         = $this->mockFactory->getPluginMock($flashMessengerMock, $redirectMock);

        $this->sut->setPluginManager($pluginMock);

        $requestMock = $this->mockFactory->getRequestMock(new \SplFixedArray(0), new \SplFixedArray(0));
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

    /**
     * @covers DmFileman\Controller\UploadFileController
     */
    public function testUploadActionCreatesThumbnail()
    {
        $responseMock = $this->mockFactory->getResponseMock();

        $flashMessengerMock = $this->mockFactory->getFlashMessengerPluginMock(1, 0);
        $redirectMock       = $this->mockFactory->getRedirectPluginMock($responseMock);
        $pluginMock         = $this->mockFactory->getPluginMock($flashMessengerMock, $redirectMock);

        $this->sut->setPluginManager($pluginMock);

        $requestMock = $this->mockFactory->getRequestMock(new \SplFixedArray(0), new \SplFixedArray(0));
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
            ->will($this->returnValue(['file' => ['type' => 'image/jpeg', 'tmp_name' => '']]));

        $this->thumbnailerMock
            ->expects($this->once())
            ->method('resizeOrigImage')
            ->will($this->returnValue(true));

        $this->sut->setCurrentPath('');

        $actualResult = $this->sut->uploadAction();

        $this->assertEquals($responseMock, $actualResult);
    }
}
