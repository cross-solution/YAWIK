<?php
/**
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license MIT
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @since 0.27
 */

namespace AuthTest\Dependency;

use PHPUnit\Framework\TestCase;

use Auth\Dependency\ListItem;

/**
 * @coversDefaultClass \Auth\Dependency\ListItem
 */
class ListItemTest extends TestCase
{

    /**
     * @covers ::__construct
     * @covers ::getTitle
     * @covers ::getUrl
     * @dataProvider data
     */
    public function testListItem($title, $url)
    {
        $listItem = new ListItem($title, $url);
        
        $this->assertSame($title, $listItem->getTitle());
        $this->assertSame($url, $listItem->getUrl());
    }
    
    public function data()
    {
        return [
            ['withoutUrl', null],
            ['withUrl', 'url']
        ];
    }
}
