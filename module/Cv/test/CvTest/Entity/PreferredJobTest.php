<?php


namespace CvTest\Entity;

use PHPUnit\Framework\TestCase;


use Core\Entity\AbstractIdentifiableEntity;
use CoreTestUtils\TestCase\TestInheritanceTrait;
use CoreTestUtils\TestCase\TestSetterGetterTrait;
use Cv\Entity\PreferredJob;
use Cv\Entity\PreferredJobInterface;
use Doctrine\Common\Collections\ArrayCollection;

/**
 *
 * @covers \Cv\Entity\PreferredJob
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Cv
 * @group Cv.Entity
 */
class PreferredJobTest extends TestCase
{
    use TestInheritanceTrait, TestSetterGetterTrait;

    private $target = PreferredJob::class;

    private $inheritance = [ AbstractIdentifiableEntity::class, PreferredJobInterface::class ];

    private $properties = [
        [ 'typeOfApplication', [ 'default' => [], 'value' => ['temporary']]],
        [ 'desiredJob', 'Software Developer' ],
        [ 'desiredLocation', 'SomeCity' ],
        [ 'desiredLocations', [ '@default' => ArrayCollection::class, '@value' => ArrayCollection::class]],
        [ 'willingnessToTravel', 'Yes' ],
        [ 'expectedSalary', '1000 USD' ],
    ];
}
