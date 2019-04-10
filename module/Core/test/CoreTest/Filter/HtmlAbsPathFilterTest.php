<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    weitz@cross-solution.de
 */

namespace CoreTest\Filter;

use PHPUnit\Framework\TestCase;

use Core\Filter\HtmlAbsPathFilter;

class HtmlAbsPathFilterTest extends TestCase
{
    protected $htmlTests = array(
        array(
            'original' =>
'
<html>
<head>
<link rel="stylesheet" type="text/css" href="http://php.net/cached.php?t=1421837618&amp;f=/fonts/Fira/fira.css" media="screen">
<link rel="stylesheet" type="text/css" href="//php.net/cached.php?t=1421837618&amp;f=/fonts/Fira/fira.css" media="screen">
<link rel="stylesheet" type="text/css" href="localCSS.css" media="screen">
</head>
    <body>
        <div>
        <table>
            <tr>
                <td class="foo">
                    <div>
                        Lorem ipsum <span class="bar">
                            <a href="/foo/bar" id="one" onClick="JavaScript:void()">One</a>
                            <a href="/foo/baz" id="two">Two</a>
                            <a src="/foo/bat" id="three">Three</a>
                            <img src="/foo/bla" />
                            <img src="../foo/bla">
                            <img src="foo/bla">
                            <img src="../foo/bli">
                        </span>
                    </div>
                </td>
            </tr>
        </table>
        </div>
    </body>
</html>
',
),
array(
'original' =>
'
<html>
<head>
<link rel="stylesheet" type="text/css" href=\'http://php.net/cached.php?t=1421837618&amp;f=/fonts/Fira/fira.css\' media="screen">
<link rel="stylesheet" type="text/css" href=\'//php.net/cached.php?t=1421837618&amp;f=/fonts/Fira/fira.css\' media="screen">
<link rel="stylesheet" type="text/css" href=\'localCSS.css\' media="screen">
</head>
    <body>
        <table>
            <tr>
                <td class="foo">
                    <div>
                        Lorem ipsum <span class="bar">
                            <a href="/foo/bar" id="one" onClick="JavaScript:void()">One</a>
                        </span>
                </td>
            </tr>
        </table>
        </div>
    </body>
</html>
',

        )
    );


    public function testFilter()
    {
        $filter = new HtmlAbsPathFilter();
        foreach ($this->htmlTests as $test) {
            $filter->setUri('http://aaa.bbb.cc/ddd/');
            $f = $filter->filter($test['original']);
            preg_match_all('/(?:href|src)\s*=\s*"([^"]*)"/', $f, $matches);
            foreach ($matches[1] as $uri) {
                $this->assertRegExp("/^https?:\/\//", $uri);
            }
        }
    }
}
