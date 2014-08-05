<?php
/**
 * YAWIK
 * 
 * @filesource
 * @copyright (c) 2013-2104 Cross Solution (http://cross-solution.de)
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
    
}

// @codeCoverageIgnoreEnd 
