<?php
/**
 * YAWIK
 *
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace CvTest\Entity;


use Cv\Entity\Education;
use CoreTestUtils\TestCase\SimpleSetterAndGetterTrait;

class EducationTest extends \PHPUnit_Framework_TestCase
{
    use SimpleSetterAndGetterTrait;

    public function getSetterAndGetterDataProvider()
    {
        $ob = new Education();
        return [
            [$ob, 'startDate', '01-01-2000'],
            [$ob, 'endDate', '01-01-2003'],
            [$ob, 'currentIndicator', true],
            [$ob, 'competencyName', 'some-name'],
            [$ob, 'organizationName', 'some-school'],
            [$ob, 'description', 'Some Description'],
            [$ob, 'country', 'Some Country'],
            [$ob, 'city', 'Some City'],
        ];
    }
}
