<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2015 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Jobs\Factory\Listener;

use Jobs\Listener\MailSender;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Factory for \Jobs\Listener\NewJobMailSender
 *
 * @since 0.19
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
class MailSenderFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return NewJobMailSender
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /* @var $mailService \Core\Mail\MailService */
        $mailService = $serviceLocator->get('Core/MailService');
        $jobsOptions = $serviceLocator->get('Jobs/Options');
        $coreOptions = $serviceLocator->get('Core/Options');
        $options = array(
            'siteName' => $coreOptions->siteName,
            'adminEmail' => $jobsOptions->getMultipostingApprovalMail(),
        );

        $listener = new MailSender($mailService, $options);

        return $listener;
    }
}
