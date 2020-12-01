<?php

declare(strict_types=1);

namespace CoreTest\Entity;

use Core\Entity\Collection\ArrayCollection;
use Core\Entity\EntityTrait;
use Core\Entity\ImageSet;
use Core\Entity\ImageSetInterface;
use CoreTestUtils\TestCase\TestInheritanceTrait;
use CoreTestUtils\TestCase\TestSetterGetterTrait;
use CoreTestUtils\TestCase\TestUsesTraitsTrait;
use Doctrine\Common\Collections\Collection;
use PHPUnit\Framework\TestCase;

/**
 * Class ImageSetTest
 *
 * @covers \Core\Entity\ImageSet
 * @package CoreTest\Entity
 */
class ImageSetTest extends TestCase
{
    use TestInheritanceTrait,
        TestUsesTraitsTrait,
        TestSetterGetterTrait;

    /**
     * @var string|ImageSet
     */
    protected $target = ImageSet::class;

    protected $inheritance = [
        ImageSetInterface::class
    ];

    protected $traits = [
        EntityTrait::class
    ];

    public function testIdShouldNotNull()
    {
        $this->assertNotNull($this->target->getId());
    }

    public function propertiesProvider()
    {
        $collection = $this->createMock(Collection::class);
        return [
            ['images', [
                'default' => '@'.ArrayCollection::class,
                'setter_args' => $collection,
                'expect' => $collection
            ]]
        ];
    }
}
