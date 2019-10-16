<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license       MIT
 */

namespace AuthTest\Form;

use PHPUnit\Framework\TestCase;

use Auth\Form\UserStatusFieldset;
use Zend\Form\Fieldset;
use Core\Form\ViewPartialProviderInterface;

class UserStatusFieldsetTest extends TestCase
{
    /**
     * @var UserStatusFieldset
     */
    private $fieldset;

    protected function setUp(): void
    {
        $this->fieldset = new UserStatusFieldset();
    }

    public function testInstance()
    {
        $this->assertInstanceOf(UserStatusFieldset::class, $this->fieldset);
        $this->assertInstanceOf(Fieldset::class, $this->fieldset);
        $this->assertInstanceOf(ViewPartialProviderInterface::class, $this->fieldset);
        $this->assertSame('form/auth/status', $this->fieldset->getViewPartial());
    }
    
    public function testInit()
    {
        $this->fieldset->init();
        $this->assertEquals($this->fieldset->count(), 1);
        $this->assertTrue($this->fieldset->has('status'));
        
        $status = $this->fieldset->get('status');
        $this->assertInstanceOf(\Core\Form\Element\Select::class, $status);
        $this->assertSame([], $status->getValueOptions());
    }
    
    /**
     * @dataProvider statusOptions
     */
    public function testSetStatusOptions(array $statusOptions)
    {
        $this->assertSame($this->fieldset, $this->fieldset->setStatusOptions($statusOptions));
        $this->fieldset->init();
        $status = $this->fieldset->get('status');
        $this->assertSame($statusOptions, $status->getValueOptions());
    }
    
    public function testHydrator()
    {
        $this->assertInstanceOf(\Core\Entity\Hydrator\EntityHydrator::class, $this->fieldset->getHydrator());
    }
    
    public function statusOptions()
    {
        return [
            [
                []
            ],
            [
                [
                    'one' => 'first',
                    'two' => 'second'
                ]
            ]
        ];
    }
}
