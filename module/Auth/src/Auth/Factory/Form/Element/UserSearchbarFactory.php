<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Auth\Factory\Form\Element;

use Core\Form\View\Helper\FormElement;
use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use Zend\Form\Element\Text;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * A text input element with typeahead and bloodhound abilities to search users.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
class UserSearchbarFactory implements FactoryInterface
{
    /**
     * Create a FormElement
     *
     * Create a user search bar textfield with typeahead and bloodhound support.
     *
     * Injects the needed javascript to the headscript view helper.
     *
     * @param  ContainerInterface $container
     * @param  string             $requestedName
     * @param  null|array         $options
     *
     * @return FormElement
     * @throws ServiceNotFoundException if unable to resolve the service.
     * @throws ServiceNotCreatedException if an exception is raised when
     *     creating a service.
     * @throws ContainerException if any other error occurs
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /* @var \Zend\View\Helper\HeadScript $headScript */

        $viewHelpers= $container->get('ViewHelperManager');
        $headScript = $viewHelpers->get('headscript');
        $basePath   = $viewHelpers->get('basepath');
        $headScript->appendFile($basePath('Auth/js/form.usersearchbar.js'));

        $input = $container->get('forms')->get('text');
        $input->setAttribute('class', 'usersearchbar');

        return $input;
    }

    /**

     *
     * {@inheritdoc}
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator->getServiceLocator(), UserStatusFieldset::class);
    }
}
