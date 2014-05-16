<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2104 Cross Solution (http://cross-solution.de)
 * @license   AGPLv3
 */

/** AttachSocialProfilesFieldsetFactory.php */ 
namespace Auth\Form;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class SocialProfilesFieldsetFactory implements FactoryInterface
{
    
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $services = $serviceLocator->getServiceLocator();
        $router = $services->get('Router');
        $config = $services->get('Config');
        $options = isset($config['form_element_config']['attach_social_profiles_fieldset'])
                ? $config['form_element_config']['attach_social_profiles_fieldset']
                : array();
        
        if (!isset($options['fetch_url'])) {
            $options['fetch_url'] = 
                $router->assemble(array('action' => 'fetch'), array('name' => 'auth-social-profiles'))
                . '?network=%s';
        }
        if (!isset($options['preview_url'])) {
            $options['preview_url'] = 
                $router->assemble(array('id' => 'null'), array('name' => 'lang/applications/detail'), true)
                . '?action=social-profile&network=%s';
        }
        if (isset($options['name'])) {
            $name = $options['name'];
            unset($options['name']);
        } else {
            $name = 'social_profiles';
        }
        
        $fieldset = new SocialProfilesFieldset($name, $options);
        
        return $fieldset;
        
    }
}

