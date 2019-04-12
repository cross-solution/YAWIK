<?php
/**
 * YAWIK
 *
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace CvTest\Entity;

use PHPUnit\Framework\TestCase;

use CoreTestUtils\TestCase\TestInheritanceTrait;
use CoreTestUtils\TestCase\TestSetterGetterTrait;
use Cv\Entity\Employment;

/**
 * Class EmploymentTest
 *
 *
 * @covers \Cv\Entity\Employment
 * @group Cv
 * @group Cv.Entity
 */
class EmploymentTest extends TestCase
{
    use TestInheritanceTrait, TestSetterGetterTrait;

    private $target = Employment::class;

    private $inheritance = [ 'Core\Entity\AbstractIdentifiableEntity' ];

    private $properties = [
        ['startDate', '01-01-2001'],
        ['endDate', '01-01-2005'],
        ['currentIndicator', true],
        ['description', 'Some Description'],
        ['organizationName', 'Organization Name']
    ];
}
