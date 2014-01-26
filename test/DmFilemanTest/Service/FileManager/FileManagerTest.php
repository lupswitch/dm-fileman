<?php

namespace DmFilemanTest\Service\FileManager;

use DmFileman\Service\FileManager\FileManager as FileManagerFileManager;

class FileManagerTest extends \PHPUnit_Framework_TestCase
{
    /** @var FileManagerFileManager */
    private $sut;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    private $factory;

    /** @var string */
    private $namespace = 'foo';

    /** @var string */
    private $baseDir = 'bar';

    /** @var string */
    private $basePath = 'baz';

    public function setUp()
    {
        $this->factory = $this->getMockBuilder('DmFileman\Service\FileManager\Factory')
            ->setMethods(['getFilesystemIterator', 'getFileInfo', 'getSplFileInfo'])
            ->getMock();

        $config = [
            FileManagerFileManager::CONFIG_NAMESPACE => $this->namespace,
            FileManagerFileManager::CONFIG_BASE_DIR  => $this->baseDir,
            FileManagerFileManager::CONFIG_BASE_PATH => $this->basePath
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

        $fileInfoMock = $this->getMockBuilder('DmFileman\Entity\FileInfo')
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
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getFilesystemIterator()
    {
        $fsIteratorMock = $this->getMockBuilder('FilesystemIterator')
            ->disableOriginalConstructor()
            ->getMock();

        return $fsIteratorMock;
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
            ->will($this->returnValue($this->getFilesystemIterator()));

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
            ->will($this->returnValue($this->getFilesystemIterator()));

        $result = $this->sut->getList('ok');

        $this->assertSame([$fileInfoMock], $result);
    }

    /**
     * @covers \DmFileman\Service\FileManager\FileManager
     */
    public function testCreate()
    {
        $this->markTestIncomplete();
    }

    /**
     * @covers \DmFileman\Service\FileManager\FileManager
     */
    public function testDelete()
    {
        $this->markTestIncomplete();
    }

    /**
     * @covers \DmFileman\Service\FileManager\FileManager
     */
    public function testDeleteTree()
    {
        $this->markTestIncomplete();
    }
}
