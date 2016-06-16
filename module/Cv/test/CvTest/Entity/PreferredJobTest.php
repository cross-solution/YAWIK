<?php


namespace CvTest\Entity;


use Cv\Entity\PreferredJob;
use Doctrine\Common\Collections\ArrayCollection;

class PreferredJobTest extends \PHPUnit_Framework_TestCase
{

    public function testSetAndGetTypeOfApplication()
    {
        $target = new PreferredJob();

        $this->assertEquals(
            array(),
            $target->getTypeOfApplication(),
            '::getTypeOfApplication() init value should return an empty array'
        );
    }

    /**
     * @param $propertyName
     * @param $propertyValue
     * @dataProvider getTestSetAndGet
     */
    public function testSetAndGet($propertyName, $propertyValue)
    {
        $target = new PreferredJob();
        $setter = 'set' . $propertyName;
        $getter = 'get' . $propertyName;

        call_user_func(array($target, $setter), $propertyValue);
        $this->assertEquals(
            $propertyValue,
            call_user_func(array($target, $getter)),
            '::' . $setter . '() and ::' . $getter . '() should executed properly'
        );
    }

    public function getTestSetAndGet()
    {
        return [
            ['typeOfApplication', ['temporary']],
            ['desiredJob', 'Software Developer'],
            ['desiredLocation', 'Some City'],
            ['desiredLocations', new ArrayCollection()],
            ['willingnessToTravel', 'Yes'],
            ['expectedSalary', '1000 USD']
        ];
    }
}
