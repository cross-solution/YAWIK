<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2014 Cross Solution (http://cross-solution.de)
 * @license   MIT
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

