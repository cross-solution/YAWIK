<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace ApplicationsTest\Entity;

use PHPUnit\Framework\TestCase;

use Applications\Entity\Attachment;

/**
 * Tests the Attachment entity.
 *
 * @covers \Applications\Entity\Attachment
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Applications
 * @group Applications.Entity
 */
class AttachmentTest extends TestCase
{
    /**
     * @var \Applications\Entity\Attachment
     */
    private $target;

    /**
     * @var string
     */
    private $uriSpec = '/file/Applications.Attachment/test/%s';

    protected function setUp(): void
    {
        $this->target = new Attachment();
        $this->target->setId('test');
    }

    /**
     * @testdox Extends \Core\Entity\FileEntity
     */
    public function testExtendsFileEntity()
    {
        $this->assertInstanceOf('\Core\Entity\FileEntity', $this->target);
    }

    /**
     * @testdox getUri() returns the URI to load the attachment in the browser.
     */
    public function testGetUriReturnsExpectedUri()
    {
        $name = 'testFile';
        $this->target->setName('testFile');

        $expected = sprintf($this->uriSpec, $name);

        $this->assertEquals($expected, $this->target->getUri());
    }

    /**
     * @testdox getUri() urlencodes the file name
     */
    public function testGetUriUsesUrlEncodeToEncodeTheFileName()
    {
        $name = 'Name-with /%& $#criticalCharacters';
        $this->target->setName($name);

        $expected    = sprintf($this->uriSpec, urlencode($name));
        $notExpected = sprintf($this->uriSpec, $name);
        $actual      = $this->target->getUri();

        $this->assertEquals($expected, $actual);
        $this->assertNotEquals($notExpected, $actual);
    }
}
