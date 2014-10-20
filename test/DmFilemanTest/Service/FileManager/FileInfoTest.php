<?php

namespace DmFilemanTest\Service\FileManager;

use DmFileman\Service\FileManager\FileInfo;
use org\bovigo\vfs;

class FileInfoTest extends \PHPUnit_Framework_TestCase
{
    /** @var FileInfo */
    private $sut;

    /** @var string */
    private $relativePath = '';

    /** @var string */
    private $origBasePath = '';

    /** @var string */
    private $thumbBasePath = '';

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    private $formatterMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    private $pathMock;

    /** @var \org\bovigo\vfs\vfsStreamDirectory */
    private $uploadDir;

    /** @var string */
    private $baseDir;

    public function setUp()
    {
        $this->uploadDir = vfs\vfsStream::setup('upload', 0777, []);
        $this->baseDir   = vfs\vfsStream::url('upload');

        $this->formatterMock = $this->getMockBuilder('DmFileman\Helper\FileInfo\Formatter')
            ->disableOriginalConstructor()
            ->setMethods(['formatSize', 'formatPermissions', 'formatOwner', 'formatGroup'])
            ->getMock();

        $pathMethods    = [
            'getExtension',
            'getRelativePath',
            'getThumbnailPath',
            'getImageThumbnailPath',
            'getTypeThumbnailPath'
        ];
        $this->pathMock = $this->getMockBuilder('DmFileman\Helper\FileInfo\Path')
            ->disableOriginalConstructor()
            ->setMethods($pathMethods)
            ->getMock();

        $this->sut = new FileInfo(
            $this->relativePath,
            $this->origBasePath,
            $this->thumbBasePath,
            $this->formatterMock,
            $this->pathMock
        );
    }

    private function getSplFileInfoMock()
    {
        return new \SplFileInfo($this->baseDir);
    }

    public function testGetSizeCallsFormatterHelper()
    {
        $expectedResult = 654;

        $this->formatterMock
            ->expects($this->once())
            ->method('formatSize')
            ->will($this->returnValue($expectedResult));

        $actualResult = $this->sut->getSize();

        $this->assertEquals($expectedResult, $actualResult);
    }

    public function testGetPermissionCallsFormatterHelper()
    {
        $expectedResult = 654;

        $this->formatterMock
            ->expects($this->once())
            ->method('formatPermissions')
            ->will($this->returnValue($expectedResult));

        $actualResult = $this->sut->getPermissions();

        $this->assertEquals($expectedResult, $actualResult);
    }

    public function testGetOwnerCallsFormatterHelper()
    {
        $expectedResult = 654;

        $this->formatterMock
            ->expects($this->once())
            ->method('formatOwner')
            ->will($this->returnValue($expectedResult));

        $actualResult = $this->sut->getOwner();

        $this->assertEquals($expectedResult, $actualResult);
    }

    public function testGetGroupCallsFormatterHelper()
    {
        $expectedResult = 654;

        $this->formatterMock
            ->expects($this->once())
            ->method('formatGroup')
            ->will($this->returnValue($expectedResult));

        $actualResult = $this->sut->getGroup();

        $this->assertEquals($expectedResult, $actualResult);
    }

    public function testGetAccessedAtReturnsEmptyStringWhenSplFileInfoIsNotSet()
    {
        $expectedResult = '';

        $actualResult = $this->sut->getAccessedAt();

        $this->assertEquals($expectedResult, $actualResult);
    }

    public function testGetAccessedAtCallsSplFileInfo()
    {
        $splFileInfo = $this->getSplFileInfoMock();

        $this->sut->setSplFileInfo($splFileInfo);

        $actualResult = $this->sut->getAccessedAt();

        $this->assertLessThanOrEqual(time(), $actualResult);
        $this->assertGreaterThanOrEqual(time() - 10, $actualResult);
    }

    public function testGetCreatedAtReturnsEmptyStringWhenSplFileInfoIsNotSet()
    {
        $expectedResult = '';

        $actualResult = $this->sut->getCreatedAt();

        $this->assertEquals($expectedResult, $actualResult);
    }

    public function testGetCreatedAtCallsSplFileInfo()
    {
        $splFileInfo = $this->getSplFileInfoMock();

        $this->sut->setSplFileInfo($splFileInfo);

        $actualResult = $this->sut->getCreatedAt();

        $this->assertLessThanOrEqual(time(), $actualResult);
        $this->assertGreaterThanOrEqual(time() - 10, $actualResult);
    }

    public function testGetModifiedAtReturnsEmptyStringWhenSplFileInfoIsNotSet()
    {
        $actualResult = $this->sut->getModifiedAt();

        $this->assertSame('', $actualResult);
    }

    public function testGetModifiedAtCallsSplFileInfo()
    {
        $splFileInfo = $this->getSplFileInfoMock();

        $this->sut->setSplFileInfo($splFileInfo);

        $actualResult = $this->sut->getModifiedAt();

        $this->assertLessThanOrEqual(time(), $actualResult);
        $this->assertGreaterThanOrEqual(time() - 10, $actualResult);
    }

    public function testGetExtensionCallsPathHelper()
    {
        $expectedResult = '';

        $this->pathMock->expects($this->once())->method('getExtension')->will($this->returnValue($expectedResult));

        $actualResult = $this->sut->getExtension();

        $this->assertEquals($expectedResult, $actualResult);
    }

    public function testGetDisplayNameReturnsEmptyStringByDefault()
    {
        $expectedResult = '';

        $actualResult = $this->sut->getDisplayName();

        $this->assertEquals($expectedResult, $actualResult);
    }

    public function testGetDisplayNameReturnsDisplayNameIfSet()
    {
        $expectedResult = 'foo';

        $this->sut->setDisplayName($expectedResult);

        $actualResult = $this->sut->getDisplayName();

        $this->assertEquals($expectedResult, $actualResult);
    }

    public function testGetDisplayNameCallsSplFileInfoIfDisplayNameIsNotSet()
    {
        $expectedResult = 'upload';

        $splFileInfoMock = $this->getSplFileInfoMock();

        $this->sut->setSplFileInfo($splFileInfoMock);

        $actualResult = $this->sut->getDisplayName();

        $this->assertEquals($expectedResult, $actualResult);
    }

    public function testGetRelativePathCallsPathHelper()
    {
        $expectedResult = 'foo';

        $this->pathMock->expects($this->once())->method('getRelativePath')->will($this->returnValue($expectedResult));

        $actualResult = $this->sut->getRelativePath();

        $this->assertEquals($expectedResult, $actualResult);
    }

    public function testGetOrigPathReturnsEmptyStringByDefault()
    {
        $expectedResult = '';

        $actualResult = $this->sut->getOrigPath();

        $this->assertEquals($expectedResult, $actualResult);
    }

    public function testGetOrigPathCallsPathHelperIfSplFileInfoIsSet()
    {
        $expectedResult = 'foo';

        $splFileInfoMock = $this->getSplFileInfoMock();
        $this->sut->setSplFileInfo($splFileInfoMock);

        $this->pathMock->expects($this->once())->method('getRelativePath')->will($this->returnValue($expectedResult));

        $actualResult = $this->sut->getOrigPath();

        $this->assertEquals($expectedResult, $actualResult);
    }

    public function testGetThumbnailPathCallsPathHelper()
    {
        $expectedResult = 'foo';

        $splFileInfoMock = $this->getSplFileInfoMock();
        $this->sut->setSplFileInfo($splFileInfoMock);

        $this->pathMock
            ->expects($this->once())
            ->method('getThumbnailPath')
            ->will($this->returnValue($expectedResult));

        $actualResult = $this->sut->getThumbnailPath();

        $this->assertEquals($expectedResult, $actualResult);
    }

    public function testGetImageThumbnailPathCallsPathHelper()
    {
        $expectedResult = 'foo';

        $splFileInfoMock = $this->getSplFileInfoMock();
        $this->sut->setSplFileInfo($splFileInfoMock);

        $this->pathMock
            ->expects($this->once())
            ->method('getImageThumbnailPath')
            ->will($this->returnValue($expectedResult));

        $actualResult = $this->sut->getImageThumbnailPath();

        $this->assertEquals($expectedResult, $actualResult);
    }

    /**
     * @expectedException \BadMethodCallException
     */
    public function testGetThumbnailPathThrowsExceptionIfSplFileInfoIsNotSet()
    {
        $this->sut->getThumbnailPath();
    }

    /**
     * @expectedException \BadMethodCallException
     */
    public function testGetImageThumbnailPathThrowsExceptionIfSplFileInfoIsNotSet()
    {
        $this->sut->getImageThumbnailPath();
    }

    public function testGetTypeThumbnailPathCallsPathHelper()
    {
        $expectedResult = 'foo';

        $this->pathMock
            ->expects($this->once())
            ->method('getTypeThumbnailPath')
            ->will($this->returnValue($expectedResult));

        $actualResult = $this->sut->getTypeThumbnailPath();

        $this->assertEquals($expectedResult, $actualResult);
    }
}
