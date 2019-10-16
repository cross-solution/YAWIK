<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */

namespace OrganizationsTest\Form;

use PHPUnit\Framework\TestCase;

use Core\Entity\Hydrator\EntityHydrator;
use Organizations\Entity\Organization;
use Organizations\Form\OrganizationsProfileFieldset;

/**
 * Class OrganizationsProfileFieldsetTest
 *
 * @author  Anthonius Munthi <me@itstoni.com>
 * @since   0.30
 * @covers  \Organizations\Form\OrganizationsProfileFieldset
 * @package OrganizationsTest\Form
 */
class OrganizationsProfileFieldsetTest extends TestCase
{
    /**
     * @var OrganizationsProfileFieldset
     */
    protected $target;

    protected function setUp(): void
    {
        $this->target = new OrganizationsProfileFieldset();
        $this->target->init();
    }

    public function testNameFormFields()
    {
        $this->assertEquals('profile-setting', $this->target->getName());
        $this->assertTrue($this->target->has('profileSetting'));
    }

    public function testGetInputFilterSpesification()
    {
        $this->assertEmpty($this->target->getInputFilterSpecification());
    }

    public function testAllowObjectBinding()
    {
        $this->assertTrue($this->target->allowObjectBinding(new Organization()));
    }

    public function testGetHydrator()
    {
        $this->assertEquals($this->target->getHydrator(), new EntityHydrator());
    }
}
