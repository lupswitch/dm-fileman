<?php

namespace DmFilemanTest\Controller;

use DmFileman\Controller\CreateDirectoryController;

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

        $this->userTextMock = $this->getMockBuilder('DmFileman\View\Helper\UserText')
            ->setMethods(['getMessage'])
            ->getMock();

        $this->sut = new CreateDirectoryController(
            $this->fileManagerMock,
            $this->createDirFormMock,
            $this->thumbnailerMock,
            $this->userTextMock
        );
    }


    /**
     * @param \PHPUnit_Framework_MockObject_MockObject $flashMessengerMock
     * @param \PHPUnit_Framework_MockObject_MockObject $redirectMock
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getPluginMock($flashMessengerMock, $redirectMock)
    {
        $pluginManagerMock = $this->getMockBuilder('Zend\Mvc\Controller\PluginManager')
            ->setMethods(['get'])
            ->getMock();

        $pluginManagerMock
            ->expects($this->at(0))
            ->method('get')
            ->will($this->returnValue($flashMessengerMock));

        $pluginManagerMock
            ->expects($this->at(1))
            ->method('get')
            ->will($this->returnValue($redirectMock));

        return $pluginManagerMock;
    }

    /**
     * @param int $successCount
     * @param int $errorCount
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getFlashMessengerPluginMock($successCount = 0, $errorCount = 0)
    {
        $flashMessengerMock = $this->getMockBuilder('Zend\Mvc\Controller\Plugin\FlashMessenger')
            ->setMethods(['addSuccessMessage', 'addErrorMessage'])
            ->getMock();

        $flashMessengerMock->expects($this->exactly($successCount))->method('addSuccessMessage');
        $flashMessengerMock->expects($this->exactly($errorCount))->method('addErrorMessage');

        return $flashMessengerMock;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getResponseMock()
    {
        $responseMock = $this->getMockBuilder('Zend\Http\Response')
            ->setMethods([])
            ->getMock();

        return $responseMock;
    }

    /**
     * @param \PHPUnit_Framework_MockObject_MockObject
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getRedirectPluginMock($responseMock)
    {
        $redirectMock = $this->getMockBuilder('Zend\Mvc\Controller\Plugin\Redirect')
            ->setMethods(['toRoute'])
            ->getMock();

        $redirectMock->expects($this->once())->method('toRoute')->will($this->returnValue($responseMock));

        return $redirectMock;
    }

    /**
     * @param array $post
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getRequestMock($post = null)
    {
        $requestMock = $this->getMockBuilder('Zend\Http\Request')
            ->setMethods(['isPost', 'getPost'])
            ->getMock();

        $isPost = !is_null($post);

        $requestMock->expects($this->once())->method('isPost')->will($this->returnValue($isPost));
        $requestMock->expects($this->once())->method('getPost')->will($this->returnValue($post));

        return $requestMock;
    }

    /**
     * @covers DmFileman\Controller\CreateDirectoryController
     */
    public function testCreateActionAddsErrorMessageToFlashMessengerByDefault()
    {
        $responseMock = $this->getResponseMock();

        $flashMessengerMock = $this->getFlashMessengerPluginMock(0, 1);
        $redirectMock       = $this->getRedirectPluginMock($responseMock);
        $pluginMock         = $this->getPluginMock($flashMessengerMock, $redirectMock);

        $this->sut->setPluginManager($pluginMock);

        $actualResult = $this->sut->createAction();

        $this->assertEquals($responseMock, $actualResult);
    }

    /**
     * @covers DmFileman\Controller\CreateDirectoryController
     */
    public function testCreateActionAddsErrorMessageToFlashMessengerWhenFormIsNotValid()
    {
        $responseMock = $this->getResponseMock();

        $flashMessengerMock = $this->getFlashMessengerPluginMock(0, 1);
        $redirectMock       = $this->getRedirectPluginMock($responseMock);
        $pluginMock         = $this->getPluginMock($flashMessengerMock, $redirectMock);

        $this->sut->setPluginManager($pluginMock);

        $requestMock = $this->getRequestMock([]);
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
        $responseMock = $this->getResponseMock();

        $flashMessengerMock = $this->getFlashMessengerPluginMock(1, 0);
        $redirectMock       = $this->getRedirectPluginMock($responseMock);
        $pluginMock         = $this->getPluginMock($flashMessengerMock, $redirectMock);

        $this->sut->setPluginManager($pluginMock);

        $requestMock = $this->getRequestMock([]);
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
