<?php

namespace DmFilemanTest\InputFilter;

use DmFileman\InputFilter\CreateDirectory;
use DmFileman\Form\CreateDirectoryForm as Form;

class CreateDirectoryTest extends \PHPUnit_Framework_TestCase
{
    /** @var CreateDirectory */
    protected $sut;

    protected function setUp()
    {
        $this->sut = new CreateDirectory();

        $this->sut->init();
    }

    /**
     * @return array
     */
    private function directoryDataProvider()
    {
        return [
            [
                null,
                ['isEmpty'],
                Form::DIRECTORY
            ],
            [
                false,
                ['isEmpty'],
                Form::DIRECTORY
            ],
            [
                '',
                ['isEmpty'],
                Form::DIRECTORY
            ],
            [
                'foo',
                [],
                Form::DIRECTORY
            ],
            [
                '1',
                [],
                Form::DIRECTORY
            ],
            [
                10,
                [],
                Form::DIRECTORY
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
            $this->directoryDataProvider(),
            $this->securityDataProvider(),
            []
        );
    }

    /**
     * @covers       DmFileman\InputFilter\CreateDirectory
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
