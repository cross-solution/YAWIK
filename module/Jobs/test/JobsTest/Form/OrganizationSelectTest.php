<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace JobsTest\Form;

use PHPUnit\Framework\TestCase;

use Jobs\Form\OrganizationSelect;
use Organizations\Entity\Organization;
use Organizations\Entity\OrganizationContact;
use Organizations\Entity\OrganizationName;

/**
 * Tests for \Jobs\Form\OrganizationSelect
 *
 * @covers \Jobs\Form\OrganizationSelect
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 *
 */
class OrganizationSelectTest extends TestCase
{
    /**
     *
     *
     * @var OrganizationSelect
     */
    private $target;

    protected function setUp(): void
    {
        $this->target = new OrganizationSelect();
    }

    /**
     * @testdox Extends \Core\Form\Element\Select and implements \Core\Form\HeadscriptProviderInterface
     */
    public function testExtendsSelectAndImplementsHeadscriptProviderInterface()
    {
        $this->assertInstanceOf('\Core\Form\Element\Select', $this->target);
        $this->assertInstanceOf('\Core\Form\HeadscriptProviderInterface', $this->target);
    }


    /**
     * @testdox Allow setting and getting of headscripts
     */
    public function testSetAndGetHeadscripts()
    {
        $this->assertEquals([ 'modules/Jobs/js/form.organization-select.js' ], $this->target->getHeadscripts());
        $this->assertSame($this->target, $this->target->setHeadscripts([ 'script1' ]), 'Fluent interface broken on "setHeadscripts"');
        $this->assertEquals([ 'script1' ], $this->target->getHeadscripts());
    }

    public function testSetsDefaultAttributesOnInit()
    {
        $this->target->init();

        $atts = $this->target->getAttributes();
        $this->assertArrayHasKey('data-autoinit', $atts);
        $this->assertArrayHasKey('data-element', $atts);
        $this->assertEquals('false', $atts['data-autoinit']);
        $this->assertEquals('organization-select', $atts['data-element']);
    }

    public function provideSetSelectableOrganizationsTestData()
    {
        $org1 = new OrganizationEntityMock([
            'id' => 'org1',
            'name' => 'Org1',
            'city' => 'City1',
            'street' => 'Street1',
            'number' => 'Number1',
        ]);

        return [
            [ [ $org1 ], false ],
            [ [ $org1 ], true ],
        ];
    }

    /**
     *
     * @dataProvider provideSetSelectableOrganizationsTestData
     *
     * @param $orgs
     * @param $addEmptyOption
     */
    public function testSetSelectableOrganizations($orgs, $addEmptyOption)
    {
        $this->assertSame($this->target, $this->target->setSelectableOrganizations($orgs, $addEmptyOption), 'Fluent interface broken');

        $values = $this->target->getValueOptions();

        if ($addEmptyOption) {
            $this->assertArrayHasKey('0', $values, 'Empty option was not created');
            $this->assertEquals(count($orgs) + 1, count($values));
        } else {
            $this->assertEquals(count($orgs), count($values));
        }
    }
}

class OrganizationEntityMock extends Organization
{
    public function __construct(array $values)
    {
        $this->id = $values['id'];
        $this->organizationName = new OrganizationName($values['name']);
        $contact = new OrganizationContact();
        $contact->setCity($values['city'])->setStreet($values['street'])->setHouseNumber($values['number']);
        $this->contact = $contact;
    }
}
