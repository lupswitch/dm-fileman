<?php

namespace DmFilemanTest\InputFilter;

use DmFileman\InputFilter\DeleteFile;
use DmFileman\Form\DeleteFileForm as Form;

class DeleteFileTest extends \PHPUnit_Framework_TestCase
{
    /** @var DeleteFile */
    protected $sut;

    protected function setUp()
    {
        $this->sut = new DeleteFile();

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
                Form::NAME
            ],
            [
                false,
                ['isEmpty'],
                Form::NAME
            ],
            [
                '',
                ['isEmpty'],
                Form::NAME
            ],
            [
                'foo',
                [],
                Form::NAME
            ],
            [
                str_repeat('abcdefghij', 16),
                [],
                Form::NAME
            ],
            [
                str_repeat('abcdefghij', 16) . 'a',
                ['stringLengthTooLong'],
                Form::NAME
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
            $this->fileDataProvider(),
            $this->securityDataProvider(),
            []
        );
    }

    /**
     * @covers       DmFileman\InputFilter\DeleteFile
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
}
