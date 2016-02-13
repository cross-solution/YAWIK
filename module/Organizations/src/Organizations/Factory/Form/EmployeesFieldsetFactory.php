<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Organizations\Factory\Form;

use Core\Entity\Hydrator\EntityHydrator;
use Core\Form\Hydrator\Strategy\CollectionStrategy;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Organizations\Form\EmployeesFieldset;

/**
 * Creates an EmployeesFieldset and injects the needed javascript to the HeadScript View Helper
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0.18
 */
class EmployeesFieldsetFactory implements FactoryInterface
{
    /**
     * Creates fieldset
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
        $hydrator = new EntityHydrator();

        $hydrator->addStrategy('employees', new CollectionStrategy());
        $fieldset->setHydrator($hydrator);

        $headscript->appendFile($basepath('Organizations/js/organizations.employees.js'));

        return $fieldset;
    }
}
