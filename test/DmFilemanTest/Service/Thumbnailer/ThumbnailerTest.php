<?php

namespace DmFilemanTest\Service\Thumbnailer;

use DmFileman\Service\Thumbnailer\Thumbnailer;
use org\bovigo\vfs;

class ThumbnailerTest extends \PHPUnit_Framework_TestCase
{
    /** @var Thumbnailer */
    private $sut;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    private $imagineMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    private $factoryMock;

    /**
     * @var vfs\vfsStreamDirectory
     */
    private $root;

    protected function setUp()
    {
        $this->imagineMock = $this->getMockBuilder('Imagine\Gd\Imagine')
            ->setMethods(['open', 'resize', 'crop', 'save'])
            ->disableAutoload()
            ->getMock();

        $this->imagineMock->expects($this->any())->method('resize')->will($this->returnSelf());
        $this->imagineMock->expects($this->any())->method('crop')->will($this->returnSelf());

        $this->factoryMock = $this->getMockBuilder('DmFileman\Service\Thumbnailer\Factory')
            ->setMethods(['getBox', 'getPoint'])
            ->getMock();

        $this->root = vfs\vfsStream::setup('root');

        $this->sut = new Thumbnailer($this->imagineMock, $this->factoryMock);
    }

    /**
     * @covers \DmFileman\Service\Thumbnailer\Thumbnailer
     */
    public function testResizeGetsOnlyCopiesWhenImageIsSmallerThanAllowedThumbnailSize()
    {
        $this->factoryMock
            ->expects($this->never())
            ->method('getBox');

        $this->factoryMock
            ->expects($this->never())
            ->method('getPoint');

        $this->imagineMock
            ->expects($this->never())
            ->method('open');

        $this->imagineMock
            ->expects($this->never())
            ->method('resize');

        $this->imagineMock
            ->expects($this->never())
            ->method('crop');

        $this->imagineMock
            ->expects($this->never())
            ->method('save');

        $this->sut->setThumbConfig([Thumbnailer::CONFIG_WIDTH => 100, Thumbnailer::CONFIG_HEIGHT => 100]);

        $origData = '12345678901';

        $origFile  = vfs\vfsStream::url('root/orig.txt');
        $thumbFile = vfs\vfsStream::url('root/thumb.txt');

        file_put_contents($origFile, $origData);

        $this->sut->resize($origFile, $thumbFile, [50, 50]);

        $this->assertEquals(file_get_contents($thumbFile), $origData);
    }

    /**
     * @return array
     */
    public function getResizeProvider()
    {
        return [
            // dataSet #1
            [
                'orig1',
                'thumb1',
                [
                    Thumbnailer::CONFIG_WIDTH => 100,
                    Thumbnailer::CONFIG_HEIGHT => 100
                ],
                [
                    0 => 200,
                    1 => 200
                ],
                [
                    100,
                    100
                ],
                [
                    0,
                    0
                ],
                [
                    100,
                    100
                ]
            ],
            // dataSet #2
            [
                'orig1',
                'thumb1',
                [
                    Thumbnailer::CONFIG_WIDTH => 100,
                    Thumbnailer::CONFIG_HEIGHT => 100
                ],
                [
                    0 => 200,
                    1 => 300
                ],
                [
                    100,
                    150
                ],
                [
                    0,
                    25
                ]
            ],
            // dataSet #3
            [
                'orig1',
                'thumb1',
                [
                    Thumbnailer::CONFIG_WIDTH => 100,
                    Thumbnailer::CONFIG_HEIGHT => 100
                ],
                [
                    0 => 300,
                    1 => 200
                ],
                [
                    150,
                    100
                ],
                [
                    25,
                    0
                ]
            ]
        ];
    }

    /**
     * @covers \DmFileman\Service\Thumbnailer\Thumbnailer
     * @dataProvider getResizeProvider
     *
     * @param string $origName
     * @param string $thumbName
     * @param array  $thumbConfig
     * @param array  $origInfo
     * @param array  $resizeParameters
     * @param array  $cropStartParameters
     */
    public function testResizeGetsSizesRight(
        $origName,
        $thumbName,
        array $thumbConfig,
        array $origInfo,
        array $resizeParameters,
        array $cropStartParameters
    ) {
        $this->factoryMock
            ->expects($this->at(0))
            ->method('getBox')
            ->with($this->equalTo($resizeParameters[0]), $this->equalTo($resizeParameters[1]))
            ->will($this->returnValue('foo-resizeSize'));

        $this->factoryMock
            ->expects($this->at(1))
            ->method('getPoint')
            ->with($this->equalTo($cropStartParameters[0]), $this->equalTo($cropStartParameters[1]))
            ->will($this->returnValue('bar-cropStart'));

        $this->factoryMock
            ->expects($this->at(2))
            ->method('getBox')
            ->with(
                $this->equalTo($thumbConfig[Thumbnailer::CONFIG_WIDTH]),
                $this->equalTo($thumbConfig[Thumbnailer::CONFIG_HEIGHT])
            )
            ->will($this->returnValue('bar-cropSize'));

        $this->imagineMock
            ->expects($this->any())
            ->method('open')
            ->with($this->equalTo($origName))
            ->will($this->returnSelf());

        $this->imagineMock
            ->expects($this->any())
            ->method('resize')
            ->with($this->equalTo('foo-resizeSize'))
            ->will($this->returnSelf());

        $this->imagineMock
            ->expects($this->any())
            ->method('crop')
            ->with($this->equalTo('bar-cropStart'), $this->equalTo('bar-cropSize'))
            ->will($this->returnSelf());

        $this->imagineMock
            ->expects($this->any())
            ->method('save')
            ->with($this->equalTo($thumbName))
            ->will($this->returnSelf());

        $this->sut->setThumbConfig($thumbConfig);

        $this->sut->resize($origName, $thumbName, $origInfo);
    }

    /**
     * @covers \DmFileman\Service\Thumbnailer\Thumbnailer
     * @expectedException \InvalidArgumentException
     */
    public function testConstructorThrowsExceptionOnUnknownImagineImplementation()
    {
        $this->imagineMock = $this->getMockBuilder('Imagine\Gd\ImagineInterface')
            ->setMethods([])
            ->disableAutoload()
            ->getMock();

        $this->factoryMock = $this->getMockBuilder('DmFileman\Service\Thumbnailer\Factory')
            ->setMethods([])
            ->getMock();

        $this->sut = new Thumbnailer($this->imagineMock, $this->factoryMock);
    }
}
