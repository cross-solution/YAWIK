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

class Facebook extends AbstractAdapter
{
    
    
    protected function queryApi($api)
    {
        return $api->api('/me');
    }
}

