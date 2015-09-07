<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2015 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Auth\Factory\Form\Element;

use Zend\Form\Element\Text;
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
     * Create a user search bar textfield with typeahead and bloodhound support.
     *
     * Injects the needed javascript to the headscript view helper.
     *
     * {@inheritdoc}
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        // we are in FormElementManager, so get the main service manager.
        /* @var $serviceLocator \Zend\ServiceManager\AbstractPluginManager
         * @var $headscript     \Zend\View\Helper\HeadScript */
        $services   = $serviceLocator->getServiceLocator();
        $viewHelpers= $services->get('ViewHelperManager');
        $headscript = $viewHelpers->get('headscript');
        $basepath   = $viewHelpers->get('basepath');

        $headscript->appendFile($basepath('Auth/js/form.usersearchbar.js'));

        $input = $serviceLocator->get('text');
        $input->setAttribute('class', 'usersearchbar');

        return $input;
    }
}
