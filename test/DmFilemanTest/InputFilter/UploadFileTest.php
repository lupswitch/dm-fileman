<?php

namespace DmFilemanTest\InputFilter;

use DmFileman\InputFilter\UploadFile;
use DmFileman\Form\UploadFileForm as Form;

class UploadFileTest extends \PHPUnit_Framework_TestCase
{
    /** @var UploadFile */
    protected $sut;

    protected function setUp()
    {
        $this->sut = new UploadFile();
    }

    /**
     * @return array
     */
    private function securityDataProvider()
    {
        return [
            [
                null,
                ['isEmpty'],
                Form::SECURITY
            ],
            [
                false,
                ['isEmpty'],
                Form::SECURITY
            ],
            [
                '',
                ['isEmpty'],
                Form::SECURITY
            ],
            [
                'foo',
                [],
                Form::SECURITY
            ],
        ];
    }

    /**
     * @return array
     */
    public function inputDataProvider()
    {
        return array_merge(
            $this->securityDataProvider(),
            []
        );
    }

    /**
     * @covers       DmFileman\InputFilter\UploadFile
     * @dataProvider inputDataProvider
     *
     * @param mixed  $nameData
     * @param array  $expectedMessages
     * @param string $inputName
     */
    public function testValidation($nameData, array $expectedMessages, $inputName)
    {
        $this->sut->setCurrentDir('');

        $this->sut->init();

        $this->sut->setData([$inputName => $nameData]);

        $this->sut->isValid();

        $actualMessages = $this->sut->getMessages();

        $this->assertInternalType('array', $actualMessages);

        if ($expectedMessages) {
            $this->assertArrayHasKey($inputName, $actualMessages);
            $this->assertInternalType('array', $actualMessages[$inputName]);
            $message = 'Found message keys: ' . implode(', ', array_keys($actualMessages[$inputName]));
            foreach ($expectedMessages as $expectedMessage) {
                $this->assertArrayHasKey($expectedMessage, $actualMessages[$inputName], $message);
            }
        } else {
            $message = '';
            if (isset($actualMessages[$inputName])) {
                $message = 'Found message keys: ' . implode(', ', array_keys($actualMessages[$inputName]));
            }
            $this->assertArrayNotHasKey($inputName, $actualMessages, $message);
        }
    }

    /**
     * @covers DmFileman\InputFilter\UploadFile
     */
    public function testFileValidatorSAreSet()
    {
        $validatorChainMock = $this->getMockBuilder('Zend\Validator\ValidatorChain')
            ->setMethods(['attachByName'])
            ->disableOriginalConstructor()
            ->getMock();
        $filterChainMock    = $this->getMockBuilder('Zend\Filter\FilterChain')
            ->setMethods(['attachByName'])
            ->disableOriginalConstructor()
            ->getMock();
        $fileInputMock      = $this->getMockBuilder('Zend\InputFilter\FileInput')
            ->setMethods(['getValidatorChain', 'getFilterChain'])
            ->disableOriginalConstructor()
            ->getMock();

        $fileInputMock
            ->expects($this->exactly(2))
            ->method('getValidatorChain')
            ->will($this->returnValue($validatorChainMock));
        $validatorChainMock
            ->expects($this->exactly(2))
            ->method('attachByName')
            ->will($this->returnValue($validatorChainMock));

        $fileInputMock
            ->expects($this->never())
            ->method('getFilterChain')
            ->will($this->returnValue($filterChainMock));
        $filterChainMock
            ->expects($this->never())
            ->method('attachByName')
            ->will($this->returnValue($filterChainMock));

        $this->sut->setFileInput($fileInputMock);

        $this->sut->setMaxSize(1000);
        $this->sut->setExtensions(['jpg']);

        $this->sut->init();
    }

    /**
     * @covers DmFileman\InputFilter\UploadFile
     */
    public function testRenameFilterIsSet()
    {
        $validatorChainMock = $this->getMockBuilder('Zend\Validator\ValidatorChain')
            ->setMethods(['attachByName'])
            ->disableOriginalConstructor()
            ->getMock();
        $filterChainMock    = $this->getMockBuilder('Zend\Filter\FilterChain')
            ->setMethods(['attachByName'])
            ->disableOriginalConstructor()
            ->getMock();
        $fileInputMock      = $this->getMockBuilder('Zend\InputFilter\FileInput')
            ->setMethods(['getValidatorChain', 'getFilterChain', 'setName', 'setRequired'])
            ->disableOriginalConstructor()
            ->getMock();

        $fileInputMock
            ->expects($this->never())
            ->method('getValidatorChain')
            ->will($this->returnValue($validatorChainMock));
        $validatorChainMock
            ->expects($this->never())
            ->method('attachByName')
            ->will($this->returnValue($validatorChainMock));

        $fileInputMock
            ->expects($this->exactly(1))
            ->method('getFilterChain')
            ->will($this->returnValue($filterChainMock));
        $filterChainMock
            ->expects($this->exactly(1))
            ->method('attachByName')
            ->will($this->returnValue($filterChainMock));

        $this->sut->setFileInput($fileInputMock);

        $this->sut->setCurrentDir('');

        $this->sut->init();
    }
}
