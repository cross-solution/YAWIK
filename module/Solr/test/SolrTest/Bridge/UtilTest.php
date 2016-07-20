<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace SolrTest\Bridge;


use Core\Entity\LocationInterface;
use Jobs\Entity\Location;
use Solr\Bridge\Manager;
use Solr\Bridge\Util;

/**
 * Class UtilTest
 * 
 * @author Anthonius Munthi <me@itstoni.com>
 * @since  0.26
 * @package SolrTest\Bridge
 */
class UtilTest extends \PHPUnit_Framework_TestCase
{
    public function testConvertDateTime()
    {
        $date = new \DateTime();
        $expected = $date->setTimezone(new \DateTimeZone('UTC'))->format(Manager::SOLR_DATE_FORMAT);

        $this->assertEquals($expected,Util::convertDateTime($date));
    }

    public function testConvertLocationCoordinates()
    {
        $coordinates = $this->getMockBuilder(LocationInterface::class)
            ->getMock()
        ;
        $location = $this->getMockBuilder(Location::class)
            ->setMethods(['getCoordinates'])
            ->getMock()
        ;

        $location->expects($this->once())
            ->method('getCoordinates')
            ->willReturn($coordinates)
        ;

        $coordinates->expects($this->once())
            ->method('getCoordinates')
            ->willReturn([0.1,0.2])
        ;

        $this->assertEquals('0.1,0.2',Util::convertLocationCoordinates($location));
    }

    /**
     * @dataProvider getTestValidateDate
     */
    public function testValidateDate($expectConverted,$value)
    {
        $result = Util::validateDate($value);
        if($expectConverted){
            $this->assertInstanceOf(
                \DateTime::class,
                $result,
                '::validateDate() should convert "'.$value.'" into DateTime object'
            );
        }else{
            $this->assertEquals(
                $value,
                $result,
                '::validateDate() should return original value if passed argument is not in date time format string'
            );
        }
    }

    public function getTestValidateDate()
    {
        return [
            [true,'2016-06-28T08:48:37Z'],
            [false,'test']
        ];
    }
}
