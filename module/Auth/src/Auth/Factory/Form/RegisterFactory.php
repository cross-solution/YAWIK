<?php
/**
 * YAWIK
 *
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Auth\Factory\Form;

use Auth\Form\Register;
use Auth\Form\RegisterInputFilter;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Auth\Options\CaptchaOptions;

class RegisterFactory implements FactoryInterface
{
    /**
     * Create a Register form
     *
     * @param  ContainerInterface $container
     * @param  string             $requestedName
     * @param  null|array         $options
     *
     * @return Register
     * @throws ServiceNotFoundException if unable to resolve the service.
     * @throws ServiceNotCreatedException if an exception is raised when
     *     creating a service.
     * @throws ContainerException if any other error occurs
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /* @var RegisterInputFilter $filter */
        $filter = $container->get('Auth\Form\RegisterInputFilter');

        /* @var CaptchaOptions $config */
        $config = $container->get('Auth/CaptchaOptions');

        $form = new Register(null, $config);
        $form->setAttribute('id', 'registration');
        $form->setInputfilter($filter);

        return $form;
    }
}
