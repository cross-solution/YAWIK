<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** Auth forms */
namespace Auth\Factory\Form;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Auth\Form\SocialProfilesFieldset;

/**
 * Factory for a SocialProfilesFieldset
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @author Anthonius Munthi <me@itstoni.com>
 */
class SocialProfilesFieldsetFactory implements FactoryInterface
{
    /**
     * Creates a {@link SocialProfilesFieldset}
     *
     * Uses config from the config key [form_element_config][attach_social_profiles_fieldset]
     * to configure fetch_url, preview_url and name or uses the defaults:
     *  - fetch_url: Route named "auth-social-profiles" with the suffix "?network=%s"
     *  - preview_url: Route named "lang/applications/detail" with the suffix "?action=social-profile&network=%s"
     *  - name: "social_profiles"
     *
     * @param  ContainerInterface $container
     * @param  string             $requestedName
     * @param  null|array         $options
     *
     * @return object
     * @throws ServiceNotFoundException if unable to resolve the service.
     * @throws ServiceNotCreatedException if an exception is raised when
     *     creating a service.
     * @throws ContainerException if any other error occurs
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /* @var $router         \Zend\Mvc\Router\RouteStackInterface */

        $router = $container->get('Router');
        $config = $container->get('Config');
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
        $options['is_disable_capable'] = false;
        $options['is_disable_elements_capable'] = false;

        $fieldset = new SocialProfilesFieldset($name, $options);

        return $fieldset;
    }
}
