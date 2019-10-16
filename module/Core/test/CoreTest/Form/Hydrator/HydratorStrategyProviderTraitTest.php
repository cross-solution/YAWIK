<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace CoreTest\Form\Hydrator;

use PHPUnit\Framework\TestCase;

use Core\Form\Hydrator\HydratorStrategyProviderTrait;
use Zend\Hydrator\Strategy\BooleanStrategy;
use Zend\Hydrator\Strategy\DefaultStrategy;

/**
 * Tests for \Core\Form\Hydrator\HydratorStrategyProviderTrait
 *
 * @covers \Core\Form\Hydrator\HydratorStrategyProviderTrait
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Core
 * @group Core.Form
 * @group Core.Form.Hydrator
 */
class HydratorStrategyProviderTraitTest extends TestCase
{

    /**
     *
     *
     * @var HydratorStrategyProvider
     */
    private $target;

    protected function setUp(): void
    {
        $this->target = new HydratorStrategyProvider();
    }

    public function testSetAndGetHydratorStrategy()
    {
        $strategy = new \Zend\Hydrator\Strategy\BooleanStrategy('true', 'false');
        $this->target->setHydratorStrategy($strategy);

        $this->assertSame($strategy, $this->target->getHydratorStrategy());
    }

    public function testGetHydratorSetsDefaultStrategy()
    {
        $this->assertInstanceOf(DefaultStrategy::class, $this->target->getHydratorStrategy());
    }

    public function testGetHydratorStrategyUsesGetDefaultHydratorStrategy()
    {
        $target = new HydratorStrategyProviderWithGetDefaultStrategy();

        $this->assertInstanceOf(BooleanStrategy::class, $target->getHydratorStrategy());
    }
}

class HydratorStrategyProvider
{
    use HydratorStrategyProviderTrait;
}

class HydratorStrategyProviderWithGetDefaultStrategy
{
    use HydratorStrategyProviderTrait;

    public function getDefaultHydratorStrategy()
    {
        return new BooleanStrategy('true', 'false');
    }
}
