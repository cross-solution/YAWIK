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
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
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
     * Create a EmployeesFieldset fieldset
     *
     * @param  ContainerInterface $container
     * @param  string             $requestedName
     * @param  null|array         $options
     *
     * @return EmployeesFieldset
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {

        /* @var $headScript     \Zend\View\Helper\HeadScript */
        $helpers  = $container->get('ViewHelperManager');
        $headScript = $helpers->get('headscript');
        $basePath   = $helpers->get('basepath');
        $fieldset = new EmployeesFieldset();
        $hydrator = new EntityHydrator();

        $hydrator->addStrategy('employees', new CollectionStrategy());
        $fieldset->setHydrator($hydrator);

        $headScript->appendFile($basePath('Organizations/js/organizations.employees.js'));

        return $fieldset;
    }
}
