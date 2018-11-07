<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Jobs\Factory\Listener;

use Interop\Container\ContainerInterface;
use Jobs\Listener\MailSender;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Factory for \Jobs\Listener\NewJobMailSender
 *
 * @since 0.19
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
class MailSenderFactory implements FactoryInterface
{

    /**
     * Create an object
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
        /* @var $mailService \Core\Mail\MailService */
        $mailService = $container->get('Core/MailService');
        $jobsOptions = $container->get('Jobs/Options');
        $coreOptions = $container->get('Core/Options');
        $options = array(
            'siteName' => $coreOptions->siteName,
            'adminEmail' => $jobsOptions->getMultipostingApprovalMail(),
        );

        $listener = new MailSender($mailService, $options);
        return $listener;
    }
}
