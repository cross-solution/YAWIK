<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright https://yawik.org/COPYRIGHT.php
 */

/** */
namespace OrganizationsTest\Entity;

use Core\Entity\ImageInterface;
use PHPUnit\Framework\TestCase;
use Organizations\Entity\OrganizationImage;

/**
 * Test the organization entity.
 *
 * @covers \Organizations\Entity\OrganizationImage
 * @author Carsten Bleek <bleek@cross-solution.de>
 * @group Organizations
 * @group Organizations.Entity
 */
class OrganizationImageTest extends TestCase
{

    /**
     * Class under Test
     *
     * @var OrganizationImage
     */
    private $target;

    protected function setUp(): void
    {
        $this->target = new OrganizationImage();
    }

    /**
     * Does the entity implement the correct interface?
     */
    public function testTemplateImplementsInterface()
    {
        $this->assertInstanceOf('\Organizations\Entity\OrganizationImage', $this->target);
        $this->assertInstanceOf(ImageInterface::class, $this->target);
    }

    public function testGetUriWithAndWithoutFilename()
    {
        $filename="My File.png";
        $this->target->setName($filename);
        $this->assertSame($this->target->getUri(), '/file/Organizations.OrganizationImage/'.$this->target->getId().'/'.urlencode($this->target->getName()));

        $org = new OrganizationImage();
        $org->setId('id');
        $this->assertSame(
            $org->getUri(),
            '/file/Organizations.OrganizationImage/'.$org->getId()
        );
    }
}
