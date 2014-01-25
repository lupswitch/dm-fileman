<?php

namespace DmFilemanTest\Helper\FileInfo;

use DmFileman\Helper\FileInfo\Formatter;
use PHPUnit_Framework_MockObject_MockObject;

class FormatterTest extends \PHPUnit_Framework_TestCase
{
    /** @var Formatter */
    protected $sut;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    protected $splFileInfoMock;

    protected function setUp()
    {
        $this->sut = new Formatter();

        $this->splFileInfoMock = $this->getMockBuilder('SplFileInfo')
            ->setMethods(['getSize', 'getPerms', 'getOwner', 'getGroup'])
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @covers DmFileman\Helper\FileInfo\Formatter
     */
    public function testFormatSizeReturnsZeroIfSplInfoIsMissing()
    {
        $actualResult = $this->sut->formatSize(null);

        $this->assertEquals(0, $actualResult);
    }

    /**
     * @covers DmFileman\Helper\FileInfo\Formatter
     */
    public function testFormatSizeReturnsZeroOnZeroSizedSplInfo()
    {
        $this->splFileInfoMock->expects($this->any())->method('getSize')->will($this->returnValue(0));

        $actualResult = $this->sut->formatSize($this->splFileInfoMock);

        $this->assertEquals(0, $actualResult);
    }

    /**
     * @covers DmFileman\Helper\FileInfo\Formatter
     */
    public function testFormatSizeReturnsFileSizeIfRawIsGiven()
    {
        $this->splFileInfoMock->expects($this->any())->method('getSize')->will($this->returnValue(100));

        $actualResult = $this->sut->formatSize($this->splFileInfoMock, true);

        $this->assertEquals(100, $actualResult);
    }

    /**
     * @return array
     */
    public function formatSizeProvider()
    {
        return [
            [100, '100 B'],
            [1024, '1 KB'],
            [10240, '10 KB'],
            [3145728, '3 MB'],
            [3200000, '3.05 MB'],
            [1024010, '1.000,01 KB', ',', '.'],
        ];
    }

    /**
     * @dataProvider formatSizeProvider
     * @covers DmFileman\Helper\FileInfo\Formatter
     *
     * @param int         $size
     * @param string      $expectedResult
     * @param null|string $decPoint
     * @param null|string $thousandsSep
     */
    public function testFormatSizeReturnsHumanReadable($size, $expectedResult, $decPoint = null, $thousandsSep = null)
    {
        if ($decPoint || $thousandsSep) {
            $this->sut = new Formatter($decPoint, $thousandsSep);
        }

        $this->splFileInfoMock->expects($this->any())->method('getSize')->will($this->returnValue($size));

        $actualResult = $this->sut->formatSize($this->splFileInfoMock);

        $this->assertEquals($expectedResult, $actualResult);
    }

    /**
     * @covers DmFileman\Helper\FileInfo\Formatter
     */
    public function testFormatSizeReturnsRawIfUnitIsNotSet()
    {
        $size = pow(10, 20);

        $this->splFileInfoMock->expects($this->any())->method('getSize')->will($this->returnValue($size));

        $actualResult = $this->sut->formatSize($this->splFileInfoMock);

        $this->assertEquals($size, $actualResult);
    }

    /**
     * @covers DmFileman\Helper\FileInfo\Formatter
     */
    public function testFormatPermissionsReturnEmptyStringOnEmptySplFileInfo()
    {
        $actualResult = $this->sut->formatPermissions();

        $this->assertEquals('', $actualResult);
    }

    /**
     * @covers DmFileman\Helper\FileInfo\Formatter
     */
    public function testFormatPermissions()
    {
        $this->splFileInfoMock->expects($this->any())->method('getPerms')->will($this->returnValue(33188));

        $actualResult = $this->sut->formatPermissions($this->splFileInfoMock);

        $this->assertEquals('0644', $actualResult);
    }

    /**
     * @covers DmFileman\Helper\FileInfo\Formatter
     */
    public function testFormatOwnerReturnEmptyStringOnEmptySplFileInfo()
    {
        $actualResult = $this->sut->formatOwner();

        $this->assertEquals('', $actualResult);
    }

    /**
     * @covers DmFileman\Helper\FileInfo\Formatter
     */
    public function testFormatOwner()
    {
        $userId = posix_geteuid();

        $this->splFileInfoMock->expects($this->any())->method('getOwner')->will($this->returnValue($userId));

        $actualResult = $this->sut->formatOwner($this->splFileInfoMock);

        $this->assertEquals(posix_getpwuid($userId)['name'], $actualResult);
    }

    /**
     * @covers DmFileman\Helper\FileInfo\Formatter
     */
    public function testFormatGroupReturnEmptyStringOnEmptySplFileInfo()
    {
        $actualResult = $this->sut->formatGroup();

        $this->assertEquals('', $actualResult);
    }

    /**
     * @covers DmFileman\Helper\FileInfo\Formatter
     */
    public function testFormatGroup()
    {
        $groupId = posix_getegid();

        $this->splFileInfoMock->expects($this->any())->method('getGroup')->will($this->returnValue($groupId));

        $actualResult = $this->sut->formatGroup($this->splFileInfoMock);

        $this->assertEquals(posix_getpwuid($groupId)['name'], $actualResult);
    }
}
