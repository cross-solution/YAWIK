<?php


namespace CvTest\Entity;


use CoreTestUtils\TestCase\SimpleSetterAndGetterTrait;
use Cv\Entity\PreferredJob;
use Doctrine\Common\Collections\ArrayCollection;

class PreferredJobTest extends \PHPUnit_Framework_TestCase
{
    use SimpleSetterAndGetterTrait;

    public function testSetAndGetTypeOfApplication()
    {
        $target = new PreferredJob();

        $this->assertEquals(
            array(),
            $target->getTypeOfApplication(),
            '::getTypeOfApplication() init value should return an empty array'
        );
    }

    public function getSetterAndGetterDataProvider()
    {
        $ob = new PreferredJob();
        return [
            [$ob, 'typeOfApplication', ['temporary']],
            [$ob, 'desiredJob', 'Software Developer'],
            [$ob, 'desiredLocation', 'Some City'],
            [$ob, 'desiredLocations', new ArrayCollection()],
            [$ob, 'willingnessToTravel', 'Yes'],
            [$ob, 'expectedSalary', '1000 USD']
        ];
    }
}
