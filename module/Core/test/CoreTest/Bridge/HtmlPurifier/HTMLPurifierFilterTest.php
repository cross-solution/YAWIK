<?php

/*
 * This file is part of the Yawik project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CoreTest\Bridge\HtmlPurifier;

use Core\Bridge\HtmlPurifier\HTMLPurifierFilter;
use PHPUnit\Framework\TestCase;
use HTMLPurifier;

class HTMLPurifierFilterTest extends TestCase
{
    /**
     * @var HTMLPurifierFilter
     */
    private $filter;

    public function setUp()
    {
        $this->filter = new HTMLPurifierFilter();
    }

    public function testFilter()
    {
        $purifier = $this->createMock(HTMLPurifier::class);
        $purifier->expects($this->once())
            ->method('purify')
            ->with($this->equalTo('input'))
            ->will($this->returnValue('output'));
        $this->filter->setHtmlPurifier($purifier);
        $this->assertEquals('output', $this->filter->filter('input'));
    }

    public function testCacheSerializerPathSetWhenNotProvidedWithConfig()
    {
        $purifier = $this->filter->getHtmlPurifier();
        $this->assertEquals(sys_get_temp_dir(), $purifier->config->get('Cache.SerializerPath'));
    }

    public function testSetConfig()
    {
        $this->filter->setConfig(array(
            'Cache.SerializerPath' => '/dev/null',
        ));
        $purifier = $this->filter->getHtmlPurifier();
        $this->assertEquals('/dev/null', $purifier->config->get('Cache.SerializerPath'));
    }

    public function testGetConfig()
    {
        $expected = array(
            'Cache.SerializerPath' => '/dev/null',
        );
        $this->filter->setConfig($expected);
        $this->assertEquals($expected, $this->filter->getConfig());
    }
}
