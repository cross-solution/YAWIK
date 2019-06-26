<?php

/*
 * This file is part of the Yawik project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CoreTest\Filter;

use Core\Filter\XssFilter;
use Core\Filter\XssFilterFactory;
use Core\Options\ModuleOptions;
use Interop\Container\ContainerInterface;
use PHPUnit\Framework\TestCase;
use Core\Bridge\HtmlPurifier\HTMLPurifierFilter;

class XssFilterTest extends TestCase
{
    public function testFilter()
    {
        $purifier = $this->createMock(HTMLPurifierFilter::class);
        $purifier->expects($this->once())
            ->method('filter')
            ->with('some-value')
            ->willReturn('purified');

        $ob = new XssFilter($purifier);
        $this->assertEquals($purifier, $ob->getHtmlPurifier());
        $this->assertEquals('purified', $ob->filter('some-value'));
    }

    public function testFilterFunctional()
    {
        $tempDir = sys_get_temp_dir().'/yawik/cache/html-purifier';

        $options = $this->createMock(ModuleOptions::class);
        $options->expects($this->once())
            ->method('getCacheDir')
            ->willReturn($tempDir);
        $container = $this->createMock(ContainerInterface::class);
        $container->expects($this->once())
            ->method('get')
            ->with(ModuleOptions::class)
            ->willReturn($options);

        $factory = new XssFilterFactory();
        $xssFilter = $factory($container,'some-name');

        $content = <<<EOC
<div>
<img src="http://url.to.file.which/not.exist" onerror="alert(document.cookie);">
<img src="j&#X41vascript:alert('test2')">
</div>
EOC;

        $purified = $xssFilter->filter($content);
        $this->assertStringNotContainsString('onerror', $purified);
        $this->assertStringNotContainsString('alert', $purified);
    }
}
