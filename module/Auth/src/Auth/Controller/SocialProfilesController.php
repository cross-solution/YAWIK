<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** Auth controller */
namespace Auth\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;

//@codeCoverageIgnoreStart 

/**
 *
 */
class SocialProfilesController extends AbstractActionController
{
    /**
     * attaches further Listeners for generating / processing the output
     * @return $this
     */
    public function attachDefaultListeners()
    {
        parent::attachDefaultListeners();
        $serviceLocator  = $this->getServiceLocator();
        $defaultServices = $serviceLocator->get('DefaultListeners');
        $events          = $this->getEventManager();
        $events->attach($defaultServices);
        return $this;
    }

    public function fetchAction()
    {
        $network = $this->params()->fromQuery('network', false);
        if (!$network) {
            throw new \RuntimerException('Missing required parameter "network"');
        }
        
        $profile = $this->plugin('Auth/SocialProfiles')->fetch($network);
        
        return array(
            'profile' => $profile
        );
    }

    /**
     *
     */
    public function testhybridAction()
    {
        $oAuth = $this->OAuth('XING');
        if ($oAuth->isAvailable()) {
            $adapter = $oAuth->getAdapter();
            $api = $adapter->api();

            $proj = array(
                'api_preview' => true,
                'project' => array(
                    'order_id' => '968180',
                    'organization_id' => '5160',
                    'categories' => 'IT',
                    'city' => 'Frankfurt',
                    'country' => 'DE',
                    'description' => 'PHP-Programmer',
                    'duration' => '30',
                    'duration_unit' => 'DAY',
                    'position' => 'fulltime',
                    'skills' => 'Zend,Git,jQuery,Mongo',
                    'title' => 'PHP-Programmer',
                )
            );
            $result1 = (array) $api->post('https://api.xing.com/vendor/projects/projects', $proj);
            $result2 = (array) $api->get('https://api.xing.com/v1/users/me');

            /*
            $result3 = (array) $api->post('/v1/request_token',
                array(
                    'oauth_consumer_key' => '',
                    'oauth_callback' => '',
                    'oauth_signature_method' => '',
                    'oauth_signature' => '',

                )
            );
            */
        }

        $oAuth = $this->OAuth('XING');
        $adapter = $oAuth->getAdapter();
        //$oAuth->sweepProvider();


        /*
        $providerKey    = 'XING';

        $serviceManager = $this->getServiceLocator();;
        $user           = $serviceManager->get('AuthenticationService')->getUser();
        $hybridAuth     = $serviceManager->get('HybridAuth');

        $sessionDataStored = $user->getAuthSession($providerKey);
        if (!empty($sessionDataStored)) {
            $status = $hybridAuth->restoreSessionData($sessionDataStored);
        }

        $hauthAdapter   = $hybridAuth->authenticate($providerKey);
        $api            = $hauthAdapter->api();
        $sessionData    = $hybridAuth->getSessionData();
        $user->updateAuthSession('XING', $sessionData);
        */


        /*
        $proj = array(
            'api_preview' => true,
            'project' => array(
                'order_id' => '968180',
                'organization_id' => '5160',
                'categories' => 'IT',
                'city' => 'Frankfurt',
                'country' => 'DE',
                'description' => 'PHP-Programmer',
                'duration' => '30',
                'duration_unit' => 'DAY',
                'position' => 'fulltime',
                'skills' => 'Zend,Git,jQuery,Mongo',
                'title' => 'PHP-Programmer',
            )
        );
        $result = (array) $api->post('https://api.xing.com/vendor/projects/projects', $proj);
        $hybridAuth->logoutAllProviders();
        */
        return;
    }
}

// @codeCoverageIgnoreEnd 
