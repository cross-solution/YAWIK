<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace OrganizationsTest\Entity;

use PHPUnit\Framework\TestCase;

use Organizations\Entity\Organization;
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
        $this->target->setId('12345');
    }

    /**
     * Does the entity implement the correct interface?
     */
    public function testTemplateImplementsInterface()
    {
        $this->assertInstanceOf('\Organizations\Entity\OrganizationImage', $this->target);
        $this->assertInstanceOf('\Core\Entity\FileEntity', $this->target);
    }

    public function testGetResourceId()
    {
        $this->assertSame($this->target->getResourceId(), 'Entity/OrganizationImage');
    }

    public function testGetUriWithAndWithoutFilename()
    {
        $filename="My File.png";
        $this->target->setName($filename);
        $this->assertSame($this->target->getUri(), '/file/Organizations.OrganizationImage/'.$this->target->getId().'/'.urlencode($this->target->getName()));
        $this->target->setName(null);
        $this->assertSame($this->target->getUri(), '/file/Organizations.OrganizationImage/'.$this->target->getId());
    }

    public function testSetGetOrganization()
    {
        $organization = new Organization();
        $this->target->setOrganization($organization);
        $this->assertSame($this->target->getOrganization(), $organization);
    }
}
