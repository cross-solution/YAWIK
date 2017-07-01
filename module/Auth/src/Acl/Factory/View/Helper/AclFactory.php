<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** AclFactory.php */
namespace Acl\Factory\View\Helper;

use Interop\Container\ContainerInterface;
use Zend\Mvc\Controller\PluginManager;
use Zend\ServiceManager\Factory\FactoryInterface;
use Acl\View\Helper\Acl;

/**
 * Class AclFactory
 * @package Acl\View\Helper
 */
class AclFactory implements FactoryInterface
{

    /**
     * Create an object
     *
     * @param  ContainerInterface $container
     * @param  string             $requestedName
     * @param  null|array         $options
     *
     * @return object
     * @inheritdoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $plugins  = $container->get(PluginManager::class);
        $acl      = $plugins->get('Acl');

        $helper = new Acl($acl);
        return $helper;
    }
}
