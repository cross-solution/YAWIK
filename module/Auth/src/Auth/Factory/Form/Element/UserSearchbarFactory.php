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
 * ${CARET}
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test 
 */
class UserSearchbarFactory implements FactoryInterface
{
    /**
     * Create a user search bar textfield with typeahead and bloodhound support.
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

        $input = new Text();
        $input->setAttribute('class', 'usersearchbar');

        return $input;
    }

}