<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Core\Factory\Controller\Plugin;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use \Core\Controller\Plugin\SearchForm;

/**
 * Factory for \Core\Controller\Plugin\SearchForm
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0.25
 */
class SearchFormFactory implements FactoryInterface
{
    /**
     * Creates a SearchForm plugin.
     *
     * @param ServiceLocatorInterface|\Zend\Mvc\Controller\ControllerManager $serviceLocator
     *
     * @return SearchForm
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $services = $serviceLocator->getServiceLocator();
        $forms = $services->get('forms');
        $plugin = new SearchForm($forms);

        return $plugin;
    }
}