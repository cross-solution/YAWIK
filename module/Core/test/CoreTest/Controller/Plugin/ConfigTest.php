<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */

namespace CoreTest\Controller\Plugin;

use Core\Controller\IndexController;
use Core\Controller\Plugin\Config;
use Core\Listener\DefaultListener;
use Zend\ModuleManager\ModuleManager;

/**
 * Class ConfigTest
 *
 * @package CoreTest\Controller\Plugin
 * @author Anthonius Munthi <me@itstoni.com>
 * @covers \Core\Controller\Plugin\Config
 * @since 0.30
 */
class ConfigTest extends \PHPUnit_Framework_TestCase
{
    private $config;

    public function setUp()
    {
        $this->config = [
            'Disabled' => [
                'enabled' => false,
            ],
            'Jobs' => [
                'dashboard' => [
                    'enabled' => true,
                    'widgets' => [
                        'recentJobs' => [
                            'controller' => 'Jobs/Index',
                            'params' => ['type' => 'recent'],
                        ],
                        'scriptTest' => [
                            'script' => 'some-script'
                        ],
                        'contentTest' => [
                            'content' => 'some-content'
                        ]
                    ],
                ]
            ]
        ];
    }

    private function getController()
    {
        $defaultListener = $this->createMock(DefaultListener::class);
        $config = include __DIR__.'/fixtures/config-dump.php';
        $moduleManager = $this->createMock(ModuleManager::class);
        return new IndexController($defaultListener,$config,$moduleManager);
    }

    /**
     * @param string $key
     * @param string $module
     * @dataProvider getTestInvoke
     */
    public function testInvoke($config,$key=null,$module=null,$expected)
    {
        $plugin = new Config($config);
        $plugin->setController($this->getController());
        $value = $plugin($key,$module);
        $this
            ->assertEquals($expected,$value)
        ;
    }

    public function getTestInvoke()
    {
        $config = include __DIR__.'/fixtures/config-dump.php';

        $jobsDashboard = $config['Jobs']['dashboard'];
        $applicationDashboard = $config['Applications']['dashboard'];
        $combinedOutput = [
            'Jobs' => $jobsDashboard,
            'Applications' => $applicationDashboard,
        ];

        return [
            [   $config, 'dashboard', 'Jobs', $config['Jobs']['dashboard']  ],
            [   $config, 'dashboard', ['Jobs','Applications'], $combinedOutput  ],
            [   $config, 'settings', null, $config['Core']['settings']  ],
            [   $config, 'Core', true, $config['Core']  ],
            [   $config, ['dashboard','form'],['Jobs','Application'], ['Jobs' =>$config['Jobs']]    ],
        ];
    }

    public function testGetter()
    {
        $config = include __DIR__.'/fixtures/config-dump.php';
        $plugin = new Config($config);
        $plugin->setController($this->getController());

        $this
            ->assertEquals($config['Core']['settings'],$plugin->settings)
        ;
    }
}
