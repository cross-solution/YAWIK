<?php

/*
 * This file is part of the Yawik project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CoreTest\Filter;

use Core\Filter\XssFilter;
use PHPUnit\Framework\TestCase;
use HTMLPurifier;

class XssFilterTest extends TestCase
{
    public function testFilter()
    {
        $purifier = $this->createMock(HTMLPurifier::class);
        $purifier->expects($this->once())
            ->method('purify')
            ->with('some-value')
            ->willReturn('purified');

        $ob = new XssFilter($purifier);
        $this->assertEquals($purifier, $ob->getHtmlPurifier());
        $this->assertEquals('purified', $ob->filter('some-value'));
    }
}
