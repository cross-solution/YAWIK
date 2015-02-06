<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @author cbleek
 * @license   MIT
 */

namespace ApplicationsTest\Options;

use Applications\Options\ModuleOptions as Options;

class ModuleOptionsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Options $options
     */
    protected $options;

    public function setUp()
    {
        $options = new Options;
        $this->options = $options;
    }

    /**
     * @covers Applications\Options\ModuleOptions::getAttachmentsMaxSize
     * @covers Applications\Options\ModuleOptions::setAttachmentsMaxSize
     */
    public function testSetGetAttachmentsMaxSize()
    {
        $this->options->setAttachmentsMaxSize(12345);
        $this->assertEquals(12345, $this->options->getAttachmentsMaxSize());
    }

    /**
     * @covers Applications\Options\ModuleOptions::getAttachmentsMimeType
     * @covers Applications\Options\ModuleOptions::setAttachmentsMimeType
     */
    public function testSetGetAttachmentsMimeType()
    {
        $mime=array('image','text/plain');

        $this->options->setAttachmentsMimeType($mime);
        $this->assertEquals($mime, $this->options->getAttachmentsMimeType());
    }

    /**
     * @covers Applications\Options\ModuleOptions::getAttachmentsCount
     * @covers Applications\Options\ModuleOptions::setAttachmentsCount
     */
    public function testSetGetAttachmentsCount()
    {
        $this->options->setAttachmentsCount(7);
        $this->assertEquals(7, $this->options->getAttachmentsCount());
    }

    /**
     * @covers Applications\Options\ModuleOptions::getContactImageMimeType
     * @covers Applications\Options\ModuleOptions::setContactImageMimeType
     */
    public function testSetGetContactImageMimeType()
    {
        $mime=array('image');

        $this->options->setContactImageMimeType($mime);
        $this->assertEquals($mime, $this->options->getContactImageMimeType());
    }

    /**
     * @covers Applications\Options\ModuleOptions::getContactImageMaxSize
     * @covers Applications\Options\ModuleOptions::setContactImageMaxSize
     */
    public function testSetGetContactImageMaxSize()
    {
        $size=765432;

        $this->options->setContactImageMaxSize($size);
        $this->assertEquals($size, $this->options->getContactImageMaxSize());
    }


}
