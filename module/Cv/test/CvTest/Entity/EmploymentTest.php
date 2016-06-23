<?php
/**
 * YAWIK
 *
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace CvTest\Entity;

use Cv\Entity\Employment;
use CoreTestUtils\TestCase\SimpleSetterAndGetterTrait;

/**
 * Class EmploymentTest
 *
 * @package CvTest\Entity
 * @covers  Cv\Entity\Employment
 */
class EmploymentTest extends \PHPUnit_Framework_TestCase
{
    use SimpleSetterAndGetterTrait;

    public function getSetterAndGetterDataProvider()
    {
        $ob = new Employment();
        return [
            [$ob, 'startDate', '01-01-2001'],
            [$ob, 'endDate', '01-01-2005'],
            [$ob, 'currentIndicator', true],
            [$ob, 'description', 'Some Description'],
            [$ob, 'organizationName', 'Organization Name']
        ];
    }
}
