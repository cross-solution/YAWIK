<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace CoreTest\Entity;

use PHPUnit\Framework\TestCase;

use Core\Entity\MetaDataProviderInterface;
use Core\Entity\MetaDataProviderTrait;
use CoreTestUtils\TestCase\SetupTargetTrait;
use CoreTestUtils\TestCase\TestSetterGetterTrait;

/**
 * Tests for \Core\Entity\MetaDataProviderTrait
 *
 * @covers \Core\Entity\MetaDataProviderTrait
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 *
 */
class MetaDataProviderTraitTest extends TestCase
{
    use SetupTargetTrait, TestSetterGetterTrait;

    /**
     *
     *
     * @var string|MetaDataProviderMock
     */
    private $target = MetaDataProviderMock::class;

    private $properties = [
        // Test setting and getting single meta data
        ['metaData', ['value' => 'test', 'setter_args' => ['metaValue'], 'getter_args' => ['test'], 'expect' => 'metaValue']],

        // Test getting default value for non-existing meta-data-key
        ['metaData', ['value' => 'test', 'setter_args' => ['metaValue'], 'getter_args' => ['test2', 'default'], 'expect' => 'default']],

        // Test getting null for non-existing meta data key and no default value provided.
        ['metaData', ['value' => 'test', 'setter_args' => ['metaValue'], 'getter_args' => ['test2'], 'expect' => null]],

        // Test hasMetaData returns false for non-existant key
        ['metaData', ['value' => false, 'ignore_setter' => true, 'getter_method' => 'has*', 'getter_args' => ['test']]],

        // Test hasMetaData returns true for existant key
        ['metaData', ['value' => 'test', 'setter_args' => ['metaValue'], 'getter_method' => 'has*', 'getter_args' => ['test'], 'expect' => true]],
    ];

    public function testGetMetaDataArray()
    {
        $metaData = [
            'test' => 'metaValue',
            'test2' => 'metaValue2'
        ];

        foreach ($metaData as $key => $value) {
            $this->target->setMetaData($key, $value);
        }

        $actual = $this->target->getMetaData();

        $this->assertEquals($metaData, $actual);
    }
}

class MetaDataProviderMock implements MetaDataProviderInterface
{
    use MetaDataProviderTrait;
}
