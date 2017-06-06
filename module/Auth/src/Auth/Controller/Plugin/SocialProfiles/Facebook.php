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

use Hybrid_Provider_Adapter;

class Facebook extends AbstractAdapter
{
    
    /**
     * {@inheritDoc}
     * @see \Auth\Controller\Plugin\SocialProfiles\AbstractAdapter::initFetch()
     */
    public function init($api, Hybrid_Provider_Adapter $hauthAdapter)
    {
        $api->setDefaultAccessToken($hauthAdapter->getAccessToken()['access_token']);
    }
    
    protected function queryApi($api)
    {
        return $api->get('/me')->getDecodedBody();
    }
}
