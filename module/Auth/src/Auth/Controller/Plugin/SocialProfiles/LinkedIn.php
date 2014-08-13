<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2104 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** LinkedIn.php */ 
namespace Auth\Controller\Plugin\SocialProfiles;

class LinkedIn extends AbstractAdapter
{
    protected function queryApi($api)
    {
       $result = (array) $api->profile('~:(id,first-name,last-name,location,industry,public-profile-url,picture-url,email-address,date-of-birth,phone-numbers,summary,positions,educations,languages,last-modified-timestamp)');
       if( isset( $result['success'] ) && $result['success'] === TRUE ){
       	  $data = @ new \SimpleXMLElement( $result['linkedin'] );
       }	   
       return isset($data)
               ? $data
               : false;
    }
}

