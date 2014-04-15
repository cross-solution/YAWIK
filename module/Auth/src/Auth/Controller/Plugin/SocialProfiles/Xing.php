<?php
/**
 * Cross Applicant Management
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   AGPLv3
 */

/** Facebook.php */ 
namespace Auth\Controller\Plugin\SocialProfiles;

class Xing extends AbstractAdapter
{
    
    
    protected function queryApi($api)
    {
        $result = (array) $api->get('users/me');
        return isset($result['users'][0])
               ? $result['users'][0]
               : false;
    }
}

