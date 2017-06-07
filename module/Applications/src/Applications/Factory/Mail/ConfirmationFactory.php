<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Applications\Factory\Mail;

use Applications\Mail\Confirmation;
use Applications\Mail\NewApplication;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\MutableCreationOptionsInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Factory for \Applications\Mail\Confirmation
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test  
 */
class ConfirmationFactory implements FactoryInterface, MutableCreationOptionsInterface
{
    private $options = [];

    /**
     * Set creation options
     *
     * @param  array $options
     *
     * @return void
     */
    public function setCreationOptions(array $options)
    {
        $this->options = $options;
    }


    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $options = $options ?: $this->options;
        $router = $container->get('Router');
        $options['router'] = $router;
        $auth = $container->get('AuthenticationService');
        $user = $auth->getUser();
        $options['user'] = $user;

        $mail   = new Confirmation($options);

        return $mail;
    }
    
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $mail = $this($serviceLocator->getServiceLocator(), NewApplication::class);
        $this->options = [];

        return $mail;
    }
}
