<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @author Carsten Bleek <bleek@cross-solution.de>
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @license   MIT
 */

namespace ApplicationsTest\Options;

use PHPUnit\Framework\TestCase;

use Applications\Options\ModuleOptions as Options;

/**
 * @coversDefaultClass Applications\Options\ModuleOptions
 */
class ModuleOptionsTest extends TestCase
{
    /**
     * @var Options $options
     */
    protected $options;

    protected function setUp(): void
    {
        $options = new Options;
        $this->options = $options;
    }

    /**
     * @covers ::getAttachmentsMaxSize
     * @covers ::setAttachmentsMaxSize
     */
    public function testSetGetAttachmentsMaxSize()
    {
        $this->options->setAttachmentsMaxSize(12345);
        $this->assertEquals(12345, $this->options->getAttachmentsMaxSize());
    }

    /**
     * @covers ::getAttachmentsMimeType
     * @covers ::setAttachmentsMimeType
     */
    public function testSetGetAttachmentsMimeType()
    {
        $mime=array('image','text/plain');

        $this->options->setAttachmentsMimeType($mime);
        $this->assertEquals($mime, $this->options->getAttachmentsMimeType());
    }

    /**
     * @covers ::getAttachmentsCount
     * @covers ::setAttachmentsCount
     */
    public function testSetGetAttachmentsCount()
    {
        $this->options->setAttachmentsCount(7);
        $this->assertEquals(7, $this->options->getAttachmentsCount());
    }

    /**
     * @covers ::getContactImageMimeType
     * @covers ::setContactImageMimeType
     */
    public function testSetGetContactImageMimeType()
    {
        $mime=array('image');

        $this->options->setContactImageMimeType($mime);
        $this->assertEquals($mime, $this->options->getContactImageMimeType());
    }

    /**
     * @covers ::getContactImageMaxSize
     * @covers ::setContactImageMaxSize
     */
    public function testSetGetContactImageMaxSize()
    {
        $size=765432;

        $this->options->setContactImageMaxSize($size);
        $this->assertEquals($size, $this->options->getContactImageMaxSize());
    }

    /**
     * @covers ::setAllowedMimeTypes
     * @covers ::getAllowedMimeTypes
     */
    public function testSetGetAllowedMimeTypes()
    {
        $mime=array('image','application/pdf');

        $this->options->setAllowedMimeTypes($mime);
        $this->assertEquals($mime, $this->options->getAllowedMimeTypes());
    }

    /**
     * @covers ::setAllowSubsequentAttachmentUpload
     * @covers ::getAllowSubsequentAttachmentUpload
     */
    public function testSetGetAllowSubsequentAttachmentUpload()
    {
        $this->assertFalse($this->options->getAllowSubsequentAttachmentUpload());
        
        $this->options->setAllowSubsequentAttachmentUpload(true);
        $this->assertTrue($this->options->getAllowSubsequentAttachmentUpload());
        
        $this->options->setAllowSubsequentAttachmentUpload('1');
        $this->assertTrue($this->options->getAllowSubsequentAttachmentUpload());
        
        $this->options->setAllowSubsequentAttachmentUpload(false);
        $this->assertFalse($this->options->getAllowSubsequentAttachmentUpload());
        
        $this->options->setAllowSubsequentAttachmentUpload('0');
        $this->assertFalse($this->options->getAllowSubsequentAttachmentUpload());
    }
}
