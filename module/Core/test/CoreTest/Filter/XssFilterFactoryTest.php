<?php

/*
 * This file is part of the Yawik project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CoreTest\Filter;

use Core\Bridge\HtmlPurifier\HTMLPurifierFilter;
use Core\Filter\XssFilter;
use Core\Filter\XssFilterFactory;
use Core\Options\ModuleOptions;
use Interop\Container\ContainerInterface;
use PHPUnit\Framework\TestCase;
use HTMLPurifier;

class XssFilterFactoryTest extends TestCase
{
    public function testInvoke()
    {
        $purifier = $this->createMock(HTMLPurifier::class);
        $container = $this->createMock(ContainerInterface::class);
        $container->expects($this->once())
            ->method('get')
            ->with('Core/HtmlPurifier')
            ->willReturn($purifier);

        $factory = new XssFilterFactory();
        $ob = $factory->__invoke($container,'some-name');

        $this->assertInstanceOf(XssFilter::class, $ob);
        $this->assertInstanceOf(HTMLPurifier::class, $ob->getHtmlPurifier());
    }
}
