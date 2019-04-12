<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace SolrTest\Options;

use PHPUnit\Framework\TestCase;

use CoreTestUtils\TestCase\TestSetterGetterTrait;
use CoreTestUtils\TestCase\SetupTargetTrait;
use Jobs\Options\JobboardSearchOptions;

/**
 * Class ModuleOptionsTest
 *
 * @author  Carsten Bleek <bleek@cross-solution.de>
 * @since   0.27
 * @covers \Jobs\Options\JobboardSearchOptions
 * @package JobsTest\Options
 */
class JobboardSearchOptionsTest extends TestCase
{
    use TestSetterGetterTrait, SetupTargetTrait;

    protected $target = [
        'class' => JobboardSearchOptions::class
    ];

    public function propertiesProvider()
    {
        return [
            ['fields', [
                'value' => [
                    'q' => [
                        'enabled' => false
                    ],
                    'l' => [
                        'enabled' => true
                    ],
                    'd' => [
                        'enabled' => true
                    ],
                    'c' => [
                        'enabled' => true
                    ],
                    't' => [
                        'enabled' => true,
                    ]
                ],
                'default' => [
                    'q' => [
                        'enabled' => true
                    ],
                    'l' => [
                        'enabled' => true
                    ],
                    'd' => [
                        'enabled' => true
                    ],
                    'c' => [
                        'enabled' => true
                    ],
                    't' => [
                        'enabled' => true,
                    ]
                ]
            ]],
            ['perPage', [
                'value' => 20,
                'default' => 10
            ]],
        ]
        ;
    }
}
