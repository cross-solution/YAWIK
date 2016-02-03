<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
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
