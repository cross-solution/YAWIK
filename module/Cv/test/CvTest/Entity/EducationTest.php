<?php
/**
 * YAWIK
 *
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace CvTest\Entity;

use PHPUnit\Framework\TestCase;


use CoreTestUtils\TestCase\SetupTargetTrait;
use CoreTestUtils\TestCase\TestSetterGetterTrait;
use Cv\Entity\Education;

/**
 *
 * @covers \Cv\Entity\Education
 *
 * @group Cv
 * @group Cv.Entity
 */
class EducationTest extends TestCase
{
    use SetupTargetTrait, TestSetterGetterTrait;

    private $target = Education::class;

    private $properties = [
        ['startDate', '01-01-2000'],
        ['endDate', '01-01-2003'],
        ['currentIndicator', true],
        ['competencyName', 'some-name'],
        ['organizationName', 'some-school'],
        ['description', 'Some Description'],
        ['country', 'Some Country'],
        ['city', 'Some City'],
    ];
}
