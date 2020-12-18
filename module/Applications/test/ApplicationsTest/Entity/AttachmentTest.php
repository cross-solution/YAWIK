<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright https://yawik.org/COPYRIGHT.php
 */

/** */
namespace ApplicationsTest\Entity;

use Core\Entity\FileInterface;
use Core\Entity\FileMetadata;
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
        $this->assertInstanceOf(FileInterface::class, $this->target);
    }

    /**
     * @testdox getUri() returns the URI to load the attachment in the browser.
     */
    public function testGetUriReturnsExpectedUri()
    {
        $name = 'testFile';

        $metadata = $this->createMock(FileMetadata::class);
        $metadata->expects($this->once())
            ->method('getName')
            ->willReturn($name);
        $this->target->setMetadata($metadata);

        $expected = sprintf($this->uriSpec, $name);

        $this->assertEquals($expected, $this->target->getUri());
    }

    /**
     * @testdox getUri() urlencodes the file name
     */
    public function testGetUriUsesUrlEncodeToEncodeTheFileName()
    {
        $name = 'Name-with /%& $#criticalCharacters';

        $metadata = $this->createMock(FileMetadata::class);
        $metadata->expects($this->once())
            ->method('getName')
            ->willReturn($name);
        $this->target->setMetadata($metadata);

        $expected    = sprintf($this->uriSpec, urlencode($name));
        $notExpected = sprintf($this->uriSpec, $name);
        $actual      = $this->target->getUri();

        $this->assertEquals($expected, $actual);
        $this->assertNotEquals($notExpected, $actual);
    }
}
