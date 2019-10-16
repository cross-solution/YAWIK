<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @author cbleek
 * @license   AGPLv3
 */

namespace JobsTest\Options;

use PHPUnit\Framework\TestCase;

use Jobs\Options\ModuleOptions as Options;

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
     * @covers \Jobs\Options\ModuleOptions::getMultipostingApprovalMail
     * @covers \Jobs\Options\ModuleOptions::setMultipostingApprovalMail
     */
    public function testSetGetMultipostingApprovalMail()
    {
        $mail="abc@mail.de";

        $this->options->setMultipostingApprovalMail($mail);
        $this->assertEquals($mail, $this->options->getMultipostingApprovalMail());
    }

    /**
     * @covers \Jobs\Options\ModuleOptions::getDefaultLogo
     * @covers \Jobs\Options\ModuleOptions::setDefaultLogo
     */
    public function testSetGetDefaultLogo()
    {
        $image="image.png";

        $this->options->setDefaultLogo($image);
        $this->assertEquals($image, $this->options->getDefaultLogo());
    }

    /**
     * @covers \Jobs\Options\ModuleOptions::getMultipostingTargetUri
     * @covers \Jobs\Options\ModuleOptions::setMultipostingTargetUri
     */
    public function testSetGetMultipostingTargetUri()
    {
        $uri="http://test.de/uri";
        $this->options->setMultipostingTargetUri($uri);
        $this->assertEquals($uri, $this->options->getMultipostingTargetUri());
    }

    /**
     * @covers \Jobs\Options\ModuleOptions::getCompanyLogoMaxSize
     * @covers \Jobs\Options\ModuleOptions::setCompanyLogoMaxSize
     */
    public function testSetGetCompanyLogoMaxSize()
    {
        $size='1234';

        $this->options->setCompanyLogoMaxSize($size);
        $this->assertEquals($size, $this->options->getCompanyLogoMaxSize());
    }

    /**
     * @covers \Jobs\Options\ModuleOptions::getCompanyLogoMimeType
     * @covers \Jobs\Options\ModuleOptions::setCompanyLogoMimeType
     */
    public function testSetGetCompanyLogoMimeType()
    {
        $mime=array("text/plain");

        $this->options->setCompanyLogoMimeType($mime);
        $this->assertEquals($mime, $this->options->getCompanyLogoMimeType());
    }
}
