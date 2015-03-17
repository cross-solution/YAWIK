<?php
/**
 * YAWIK
 * 
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    weitz@cross-solution.de
 */

namespace Jobs\Listener;

use Auth\AuthenticationService;
use Zend\Mvc\Controller\PluginManager;
use Zend\ServiceManager\ServiceManagerAwareInterface;
use Zend\ServiceManager\ServiceManager;
use Jobs\Listener\Events\JobEvent;

/**
 * Job listener for triggering actions like sending mail notification
 *
 * @package Jobs\Listener
 */
class StatusChanged implements ServiceManagerAwareInterface
{
    /**
     * @var ServiceManager
     */
    protected $serviceManager;

    /**
     * @param ServiceManager $serviceManager
     * @return $this
     */
    public function setServiceManager(ServiceManager $serviceManager) {
        $this->serviceManager = $serviceManager;
        return $this;
    }

    /**
     * @return ServiceManager
     */
    public function getServiceManager() {
        return $this->serviceManager;
    }

    /**
     * allows an event attachment just by class
     *
     * @param JobEvent $e
     */
    public function __invoke(JobEvent $e)
    {
        /** @var ServiceManager $serviceManager */
        $serviceManager = $this->getServiceManager();
        /** @var PluginManager $controllerPluginManager */
        $controllerPluginManager = $serviceManager->get('controllerPluginManager');
        $translator = $serviceManager->get('translator');

        /** @var \Jobs\Options\ModuleOptions $options */
        $options = $serviceManager->get('Jobs/Options');
        $optionsCore = $serviceManager->get('Core/Options');

        $prices = array();
        $config = $serviceManager->get('Config');
        if (array_key_exists('multiposting', $config) && array_key_exists('channels', $config['multiposting'])) {
            foreach ($config['multiposting']['channels'] as $name => $data) {
                $prices[$name] = isset($data['price'])?$data['price']:$translator->translate('free');
            }
        }

        /**
         * the sender of the mail is the currently logged in user
         */
        /** @var AuthenticationService $authService */
        $authService             = $serviceManager->get('authenticationservice');
        $user                    = $authService->getUser();
        $userEmail               = $authService->getUser()->getInfo()->email;
        $userName                = $authService->getUser()->getInfo()->displayName;
        $job                     = $e->getJobEntity();


        /** @var \Zend\Mvc\Controller\Plugin\Url $urlPlugin */
        $urlPlugin = $controllerPluginManager->get('url');

        $previewLink = $urlPlugin->fromRoute('lang/jobs/approval', array(),
            array('force_canonical' => True,
                  'query' => array('id' => $job->getId())));


        /** @var \Core\Mail\MailService $mailService */
        $mailService = $serviceManager->get('Core/MailService');

        /** @var \Core\Mail\HTMLTemplateMessage $mail */
        $mail = $mailService->get('htmltemplate');

        $mail->siteName = $optionsCore->siteName;
        $mail->prices = $prices;
        $mail->userName = $user->getInfo()->getDisplayName();
        $mail->setVariable('job', $job);
        $mail->setVariable('link' ,$previewLink);
        $mail->setTemplate('mail/jobCreatedMail');
        $mail->setSubject( /*translate*/ 'A New Job was created');
        $mail->setFrom($userEmail, $userName);
        $mail->setTo($options->getMultipostingApprovalMail());

        $mailService->send($mail);

        return;
    }

}