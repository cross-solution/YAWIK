<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace CvTest\Options;

use PHPUnit\Framework\TestCase;

use CoreTestUtils\TestCase\TestInheritanceTrait;
use CoreTestUtils\TestCase\TestSetterGetterTrait;
use Cv\Options\ModuleOptions;
use Zend\Stdlib\AbstractOptions;

/**
 * Tests for \Cv\Options\ModuleOptions
 *
 * @covers \Cv\Options\ModuleOptions
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Cv
 * @group Cv.Options
 */
class ModuleOptionsTest extends TestCase
{
    use TestInheritanceTrait, TestSetterGetterTrait;

    private $target = ModuleOptions::class;

    private $inheritance = [ AbstractOptions::class ];

    private $properties = [
        ['attachmentsMaxSize', ['value' => '1234', 'default' => 5000000] ],
        ['attachmentsMimeType', [
            'value' => ['mime/type'],
            'default' => [
                'image','applications/pdf',
                'application/x-pdf',
                'application/acrobat',
                'applications/vnd.pdf',
                'text/pdf',
                'text/x-pdf'
            ]
        ]],
        ['attachmentsCount', ['value' => 10, 'default' => 3]]
    ];
}
