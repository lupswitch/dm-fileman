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

        $this->sut->setCurrentDir('');

        $this->sut->init();
    }

    /**
     * @return array
     */
    private function fileDataProvider()
    {
        return [
            [
                null,
                ['isEmpty'],
                Form::FILE
            ],
            [
                false,
                ['isEmpty'],
                Form::FILE
            ],
            [
                '',
                ['isEmpty'],
                Form::FILE
            ],
            [
                'foo',
                [],
                Form::FILE
            ],
            [
                str_repeat('abcdefghij', 10000),
                [],
                Form::FILE
            ],
            [
                str_repeat('abcdefghij', 10000) . 'a',
                ['stringLengthTooLong'],
                Form::FILE
            ],
        ];
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
            //$this->fileDataProvider(),
            $this->securityDataProvider(),
            []
        );
    }

    /**
     * @covers \DmFileman\InputFilter\UploadFile
     * @dataProvider inputDataProvider
     *
     * @param mixed  $nameData
     * @param array  $expectedMessages
     * @param string $inputName
     */
    public function testValidation($nameData, array $expectedMessages, $inputName)
    {
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

    public function testSkipped()
    {
        $this->markTestIncomplete('File upload is not yet tested');
    }
}
