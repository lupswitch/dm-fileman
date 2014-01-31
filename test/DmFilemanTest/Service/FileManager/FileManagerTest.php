<?php

namespace DmFilemanTest\Service\FileManager;

use DmFileman\Service\FileManager\FileManager as FileManagerFileManager;
use org\bovigo\vfs;

class FileManagerTest extends \PHPUnit_Framework_TestCase
{
    /** @var FileManagerFileManager */
    private $sut;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    private $factory;

    /** @var string */
    private $baseDir;

    /** @var string */
    private $basePath;

    public function setUp()
    {
        $structure = [
            'orig' => [
                'img.jpg' => 'abcd',
                'old' => [
                    'img.jpg' => 'cdab'
                ],
            ],
            'thumb' => [
                'img.jpg' => 'bcde',
                'old' => [
                    'img.jpg' => 'cdab'
                ],
            ]
        ];

        $this->uploadDir = vfs\vfsStream::setup('upload', 0777, $structure);
        $this->baseDir   = vfs\vfsStream::url('upload');
        $this->basePath  = '/upload';

        $this->factory = $this->getMockBuilder('DmFileman\Service\FileManager\Factory')
            ->setMethods(['getFilesystemIterator', 'getFileInfo', 'getSplFileInfo'])
            ->getMock();

        $config = [
            FileManagerFileManager::CONFIG_BASE_DIR  => $this->baseDir,
            FileManagerFileManager::CONFIG_BASE_PATH => $this->basePath,
        ];

        $this->sut = new FileManagerFileManager($this->factory, $config);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getFileInfo()
    {
        $fileInfoMethods = [
            'setFromSplFileInfo',
            'getOrigBasePath',
            'getThumbBasePath',
            'disableWithFilename',
            'getSize',
            'getPermissions',
            'getOwner',
            'getGroup',
            'getAccessedAt',
            'getCreatedAt',
            'getModifiedAt',
            'getExtension',
            'getDisplayName',
            'setDisplayName',
            'getRelativePath',
            'getOrigPath',
            'getThumbnailPath',
            'getTypeThumbnailPath'
        ];

        $fileInfoMock = $this->getMockBuilder('DmFileman\Service\FileManager\FileInfo')
            ->setMethods($fileInfoMethods)
            ->disableOriginalConstructor()
            ->getMock();

        return $fileInfoMock;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getSplFileInfo()
    {
        $splFileInfoMock = $this->getMockBuilder('SplFileInfo')
            ->disableOriginalConstructor()
            ->getMock();

        return $splFileInfoMock;
    }

    /**
     * @covers \DmFileman\Service\FileManager\FileManager
     */
    public function testGetListReturnsEmptyByDefault()
    {
        $this->factory
            ->expects($this->any())
            ->method('getFileInfo')
            ->will($this->returnValue($this->getFileInfo()));

        $this->factory
            ->expects($this->any())
            ->method('getSplFileInfo')
            ->will($this->returnValue($this->getSplFileInfo()));

        $this->factory
            ->expects($this->any())
            ->method('getFilesystemIterator')
            ->will($this->returnValue([]));

        $result = $this->sut->getList();

        $this->assertSame([], $result);
    }

    /**
     * @covers \DmFileman\Service\FileManager\FileManager
     */
    public function testGetListReturnsList()
    {
        $fileInfoMock = $this->getFileInfo();

        $this->factory
            ->expects($this->any())
            ->method('getFileInfo')
            ->will($this->returnValue($fileInfoMock));

        $this->factory
            ->expects($this->any())
            ->method('getSplFileInfo')
            ->will($this->returnValue($this->getSplFileInfo()));

        $this->factory
            ->expects($this->any())
            ->method('getFilesystemIterator')
            ->will($this->returnValue([]));

        $result = $this->sut->getList('ok');

        $this->assertSame([$fileInfoMock], $result);
    }

    /**
     * @covers \DmFileman\Service\FileManager\FileManager
     */
    public function testGetListReturnsListWithRealFiles()
    {
        $fileInfoMock = $this->getFileInfo();
        $splFileInfoMock = $this->getSplFileInfo();

        $this->factory
            ->expects($this->any())
            ->method('getFileInfo')
            ->will($this->returnValue($fileInfoMock));

        $this->factory
            ->expects($this->any())
            ->method('getSplFileInfo')
            ->will($this->returnValue($splFileInfoMock));

        $this->factory
            ->expects($this->any())
            ->method('getFilesystemIterator')
            ->will($this->returnValue([$splFileInfoMock, $splFileInfoMock]));

        $result = $this->sut->getList('/');

        $this->assertSame([$fileInfoMock, $fileInfoMock], $result);
    }

    /**
     * @covers \DmFileman\Service\FileManager\FileManager
     */
    public function testCreateCreatesNewDirectories()
    {
        $actualResult = $this->sut->create('/new');

        $this->assertTrue($actualResult);

        $this->assertFileExists(vfs\vfsStream::url('upload/orig/new'));
        $this->assertFileExists(vfs\vfsStream::url('upload/thumb/new'));
    }

    /**
     * @covers \DmFileman\Service\FileManager\FileManager
     */
    public function testCreateReturnsFalseIfDirectoryAlreadyExists()
    {
        $actualResult = $this->sut->create('/old');

        $this->assertFalse($actualResult);
    }

    /**
     * @covers \DmFileman\Service\FileManager\FileManager
     */
    public function testDeleteDeletesFiles()
    {
        $this->assertFileExists(vfs\vfsStream::url('upload/orig/old/img.jpg'));

        $actualResult = $this->sut->delete('/old/img.jpg');

        $this->assertTrue($actualResult);
        $this->assertFileExists(vfs\vfsStream::url('upload/orig/old'));
        $this->assertFileNotExists(vfs\vfsStream::url('upload/orig/old/img.jpg'));
    }

    /**
     * @covers \DmFileman\Service\FileManager\FileManager
     */
    public function testDeleteDeletesFolders()
    {
        $this->assertFileExists(vfs\vfsStream::url('upload/orig/old'));

        $actualResult = $this->sut->delete('/old');

        $this->assertTrue($actualResult);
        $this->assertFileExists(vfs\vfsStream::url('upload/orig'));
        $this->assertFileNotExists(vfs\vfsStream::url('upload/orig/old'));
    }

    /**
     * @covers \DmFileman\Service\FileManager\FileManager
     */
    public function testDeleteTree()
    {
        $actualResult = $this->sut->deleteTree(vfs\vfsStream::url('upload/orig'));

        $this->assertTrue($actualResult);
        $this->assertFileNotExists(vfs\vfsStream::url('upload/orig'));
    }
}
