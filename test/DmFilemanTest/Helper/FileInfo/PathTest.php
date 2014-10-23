<?php

namespace DmFilemanTest\Helper\FileInfo;

use DmFileman\Helper\FileInfo\Path;
use org\bovigo\vfs;

class PathTest extends \PHPUnit_Framework_TestCase
{
    /** @var Path */
    protected $sut;

    /**
     * @var vfs\vfsStreamDirectory
     */
    private $uploadDir;

    protected function setUp()
    {
        $this->sut = new Path();

        $structure = [
            'orig'  => [
                'img.jpg' => 'abcd'
            ],
            'thumb' => [
                'img.jpg' => 'bcde'
            ]
        ];

        $this->uploadDir = vfs\vfsStream::setup('upload', 0777, $structure);
    }

    /**
     * @covers DmFileman\Helper\FileInfo\Path
     */
    public function testGetRelativePathReturnsGivenPathIfSplInfoIsMissing()
    {
        $relativePath = 'foo';

        $actualResult = $this->sut->getRelativePath(true, false, null, $relativePath);

        $this->assertEquals($relativePath, $actualResult);
    }

    /**
     * @covers   DmFileman\Helper\FileInfo\Path
     * @requires PHP 5.6
     */
    public function testGetRelativePathReturnsGivenPathIfWithFilenameIsFalse()
    {
        $relativePath = 'foo';

        $splFileInfoMock = $this->getMock('\SplFileInfo', [], [], '', false);

        $actualResult = $this->sut->getRelativePath(false, false, $splFileInfoMock, $relativePath);

        $this->assertEquals($relativePath, $actualResult);
    }

    /**
     * @covers   DmFileman\Helper\FileInfo\Path
     * @requires PHP 5.6
     */
    public function testGetRelativePathReturnsGivenPathIfWithFilenameIsDisabled()
    {
        $relativePath = 'foo';

        $splFileInfoMock = $this->getMock('\SplFileInfo', [], [], '', false);

        $actualResult = $this->sut->getRelativePath(true, true, $splFileInfoMock, $relativePath);

        $this->assertEquals($relativePath, $actualResult);
    }

    /**
     * @covers   DmFileman\Helper\FileInfo\Path
     * @requires PHP 5.6
     */
    public function testGetRelativePathReturnsGivenPathWithAppendedFileName()
    {
        $relativePath = 'foo/';
        $filename     = 'bar.jpg';

        $splFileInfoMock = $this->getMock('\SplFileInfo', ['getFilename', 'isDir'], [], '', false);

        $splFileInfoMock->expects($this->any())->method('getFilename')->will($this->returnValue($filename));
        $splFileInfoMock->expects($this->any())->method('isDir')->will($this->returnValue(false));

        $actualResult = $this->sut->getRelativePath(true, false, $splFileInfoMock, $relativePath);

        $this->assertEquals($relativePath . $filename, $actualResult);
    }

    /**
     * @covers   DmFileman\Helper\FileInfo\Path
     * @requires PHP 5.6
     */
    public function testGetRelativePathReturnsGivenPathWithAppendedFileNameAndSlashForDirectories()
    {
        $relativePath = 'foo/';
        $filename     = 'bar';

        $splFileInfoMock = $this->getMock('\SplFileInfo', ['getFilename', 'isDir'], [], '', false);

        $splFileInfoMock->expects($this->any())->method('getFilename')->will($this->returnValue($filename));
        $splFileInfoMock->expects($this->any())->method('isDir')->will($this->returnValue(true));

        $actualResult = $this->sut->getRelativePath(true, false, $splFileInfoMock, $relativePath);

        $this->assertEquals($relativePath . $filename . '/', $actualResult);
    }

    /**
     * @covers DmFileman\Helper\FileInfo\Path
     */
    public function testGetThumbnailPathReturnEmptyOnMissingSplFileInfo()
    {
        $actualResult = $this->sut->getThumbnailPath();

        $this->assertEquals('', $actualResult);
    }

    /**
     * @covers   DmFileman\Helper\FileInfo\Path
     * @requires PHP 5.6
     */
    public function testGetThumbnailPathTriesToGetImageThumbnailPathForImages()
    {
        $image = vfs\vfsStream::url('upload/orig/img.jpg');

        $splFileInfoMock = $this->getMock('\SplFileInfo', ['isFile', 'getExtension'], [], '', false);

        $splFileInfoMock->expects($this->any())->method('isFile')->will($this->returnValue(true));
        $splFileInfoMock->expects($this->any())->method('isDir')->will($this->returnValue(false));
        $splFileInfoMock->expects($this->any())->method('getExtension')->will($this->returnValue('jpg'));

        $pathname      = $image;
        $displayName   = basename($image);
        $thumbBasePath = '/upload/thumb';
        $relativePath  = '/';

        $actualResult = $this->sut
            ->getThumbnailPath($splFileInfoMock, $pathname, $displayName, $thumbBasePath, $relativePath);

        $this->assertEquals('/upload/thumb/img.jpg', $actualResult);
    }

    /**
     * @covers   DmFileman\Helper\FileInfo\Path
     * @requires PHP 5.6
     */
    public function testGetThumbnailPathReturnsDirectoryThumbnailForDirectories()
    {
        $splFileInfoMock = $this->getMock('\SplFileInfo', ['isFile', 'isDir'], [], '', false);

        $splFileInfoMock->expects($this->any())->method('isFile')->will($this->returnValue(false));
        $splFileInfoMock->expects($this->any())->method('isDir')->will($this->returnValue(true));

        $actualResult = $this->sut->getThumbnailPath($splFileInfoMock);

        $this->assertEquals('/img/filemanager/folder.png', $actualResult);
    }

    /**
     * @covers   DmFileman\Helper\FileInfo\Path
     * @requires PHP 5.6
     */
    public function testGetThumbnailPathReturnsDirectoryThumbnailForDirectoriesWhenThumbnailCreationFails()
    {
        $splFileInfoMock = $this->getMock('\SplFileInfo', ['isFile', 'isDir', 'getExtension'], [], '', false);

        $splFileInfoMock->expects($this->any())->method('isFile')->will($this->returnValue(true));
        $splFileInfoMock->expects($this->any())->method('isDir')->will($this->returnValue(false));
        $splFileInfoMock->expects($this->any())->method('getExtension')->will($this->returnValue('jpg'));

        $pathname      = '';
        $displayName   = '';
        $thumbBasePath = '/upload/thumb';
        $relativePath  = '/';

        $actualResult = $this->sut
            ->getThumbnailPath($splFileInfoMock, $pathname, $displayName, $thumbBasePath, $relativePath);

        $this->assertEquals('/img/filemanager/image.png', $actualResult);
    }

    /**
     * @covers   DmFileman\Helper\FileInfo\Path
     * @requires PHP 5.6
     */
    public function testGetThumbnailPathGetsTypeThumbnailPathIfNoSpecialImagePathWasFound()
    {
        $image = vfs\vfsStream::url('upload/orig/img.jpg');

        $splFileInfoMock = $this->getMock('\SplFileInfo', ['isFile', 'getExtension', 'isDir'], [], '', false);

        $splFileInfoMock->expects($this->any())->method('isFile')->will($this->returnValue(true));
        $splFileInfoMock->expects($this->any())->method('isDir')->will($this->returnValue(false));
        $splFileInfoMock->expects($this->any())->method('getExtension')->will($this->returnValue('xyz'));

        $pathname      = $image;
        $displayName   = basename($image);
        $thumbBasePath = '/upload/thumb';
        $relativePath  = '/';

        $actualResult = $this->sut
            ->getThumbnailPath($splFileInfoMock, $pathname, $displayName, $thumbBasePath, $relativePath);

        $this->assertEquals('/img/filemanager/fileicon_bg.png', $actualResult);
    }

    /**
     * @covers DmFileman\Helper\FileInfo\Path
     */
    public function testGetExtensionReturnsEmptyOnMissingSplFileInfo()
    {
        $actualResult = $this->sut->getExtension(null, 'prefix');

        $this->assertEquals('', $actualResult);
    }

    /**
     * @covers   DmFileman\Helper\FileInfo\Path
     * @requires PHP 5.6
     */
    public function testGetExtensionReturnsExtensionAndPrependsPrepend()
    {
        $splFileInfoMock = $this->getMock('\SplFileInfo', ['getExtension'], [], '', false);
        $splFileInfoMock->expects($this->any())->method('getExtension')->will($this->returnValue('foo'));

        $actualResult = $this->sut->getExtension($splFileInfoMock, '.');

        $this->assertEquals('.foo', $actualResult);
    }

    /**
     * @covers DmFileman\Helper\FileInfo\Path
     */
    public function testGetImageThumbnailPathReturnsEmptyStringOnMissingSplFileInfo()
    {
        $actualResult = $this->sut->getImageThumbnailPath();

        $this->assertEquals('', $actualResult);
    }

    /**
     * @covers   DmFileman\Helper\FileInfo\Path
     * @requires PHP 5.6
     */
    public function testGetImageThumbnailPathReturnsEmptyStringIfThumbnailIsNotFound()
    {
        $image = vfs\vfsStream::url('upload/orig/img2.jpg');

        $splFileInfoMock = $this->getMock('\SplFileInfo', [], [], '', false);

        $pathname      = $image;
        $displayName   = basename($image);
        $thumbBasePath = '/upload/thumb';
        $relativePath  = '/';

        $actualResult = $this->sut
            ->getImageThumbnailPath($splFileInfoMock, $pathname, $displayName, $thumbBasePath, $relativePath);

        $this->assertEquals('', $actualResult);
    }

    /**
     * @covers   DmFileman\Helper\FileInfo\Path
     * @requires PHP 5.6
     */
    public function testGetImageThumbnailPath()
    {
        $image = vfs\vfsStream::url('upload/orig/img.jpg');

        $splFileInfoMock = $this->getMock('\SplFileInfo', [], [], '', false);

        $pathname      = $image;
        $displayName   = basename($image);
        $thumbBasePath = '/upload/thumb';
        $relativePath  = '/';

        $actualResult = $this->sut
            ->getImageThumbnailPath($splFileInfoMock, $pathname, $displayName, $thumbBasePath, $relativePath);

        $this->assertEquals('/upload/thumb/img.jpg', $actualResult);
    }

    /**
     * @covers DmFileman\Helper\FileInfo\Path
     */
    public function testGetTypeThumbnailPathReturnsFileIconOnMissingSplFileInfo()
    {
        $actualResult = $this->sut->getTypeThumbnailPath();

        $this->assertEquals('/img/filemanager/fileicon_bg.png', $actualResult);
    }

    /**
     * @covers   DmFileman\Helper\FileInfo\Path
     * @requires PHP 5.6
     */
    public function testGetTypeThumbnailPathReturnsDirectoryIconForDirectories()
    {
        $splFileInfoMock = $this->getMock('\SplFileInfo', ['isDir'], [], '', false);

        $splFileInfoMock->expects($this->any())->method('isDir')->will($this->returnValue(true));

        $actualResult = $this->sut->getTypeThumbnailPath($splFileInfoMock);

        $this->assertEquals('/img/filemanager/folder.png', $actualResult);
    }

    /**
     * @covers   DmFileman\Helper\FileInfo\Path
     * @requires PHP 5.6
     */
    public function testGetTypeThumbnailPathReturnsFileIconOnUnknownExtension()
    {
        $splFileInfoMock = $this->getMock('\SplFileInfo', ['getExtension', 'isDir'], [], '', false);

        $splFileInfoMock->expects($this->any())->method('isDir')->will($this->returnValue(false));
        $splFileInfoMock->expects($this->any())->method('getExtension')->will($this->returnValue('xyz'));

        $actualResult = $this->sut->getTypeThumbnailPath($splFileInfoMock);

        $this->assertEquals('/img/filemanager/fileicon_bg.png', $actualResult);
    }

    /**
     * @covers   DmFileman\Helper\FileInfo\Path
     * @requires PHP 5.6
     */
    public function testGetTypeThumbnailPathReturnsExtensionIconWhenFound()
    {
        $splFileInfoMock = $this->getMock('\SplFileInfo', ['getExtension', 'isDir'], [], '', false);

        $splFileInfoMock->expects($this->any())->method('isDir')->will($this->returnValue(false));
        $splFileInfoMock->expects($this->any())->method('getExtension')->will($this->returnValue('jpg'));

        $actualResult = $this->sut->getTypeThumbnailPath($splFileInfoMock);

        $this->assertEquals('/img/filemanager/image.png', $actualResult);
    }
}
