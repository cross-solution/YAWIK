<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2015 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Organizations\Factory\Form;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Organizations\Form\EmployeesFieldset;

/**
 * ${CARET}
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test
 * @since 0.18
 */
class EmployeesFieldsetFactory implements FactoryInterface
{
    /**
     * Creates service
     * {@inheritdoc}
     *
     * @return EmployeesFieldset
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /* @var $serviceLocator \Zend\ServiceManager\AbstractPluginManager
         * @var $headscript     \Zend\View\Helper\HeadScript */
        $services = $serviceLocator->getServiceLocator();
        $helpers  = $services->get('ViewHelperManager');
        $headscript = $helpers->get('headscript');
        $basepath   = $helpers->get('basepath');
        $fieldset = new EmployeesFieldset();

        $headscript->appendFile($basepath('Organizations/js/organizations.employees.js'));

        return $fieldset;
    }
}