<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/**
 *
 */
namespace Auth\Controller\Plugin\SocialProfiles;

class Xing extends AbstractAdapter
{
    
    
    protected function queryApi($api)
    {
        $result = (array) $api->get('users/me');
        return isset($result['users'][0])
               ? $this->convert($result['users'][0])
               : false;
    }

    protected function convert($value)
    {
        if (!$value instanceOf \stdClass) {
            return $value;
        }
        $result = [];

        foreach ((array) $value as $key => $value) {
            $result[$key] = $this->convert($value);
        }

        return $result;

    }


}
