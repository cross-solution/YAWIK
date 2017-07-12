<?php
/**
 * YAWIK
 *
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Cv\Factory\Controller;


use Cv\Controller\IndexController;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class IndexControllerFactory implements FactoryInterface
{
    /**
     * Create a IndexController controller
     *
     * @param  ContainerInterface $container
     * @param  string             $requestedName
     * @param  null|array         $options
     *
     * @return IndexController
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        //$searchForm = $container->get('forms')->get('Cv/SearchForm');

        return new IndexController();
    }

}