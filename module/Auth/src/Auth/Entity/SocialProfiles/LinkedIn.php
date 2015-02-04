<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2014 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** LinkedIn.php */
namespace Auth\Entity\SocialProfiles;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;


/**
 * 
 * @ODM\EmbeddedDocument
 */
class LinkedIn extends AbstractProfile
{
    protected $name = 'LinkedIn';
    
    protected $config = array(
        'educations' => array(
            'key' => 'educations',
        ),
        'employments' => array(
            'key' => 'positions',
        ),
        'properties_map' => array(
            'link' => 'public-profile-url',
        ),
    );
    
    protected function filterEducation($data)
    {
        $return = array();
        if (isset($data['start-date'])) {
            $return['startDate'] = $data['start-date']['year'] . '-' . $data['start-date']['month'] . '-' . $data['start-date']['day'];
        }
        if (isset($data['end-date'])) {
            $return['endDate'] = $data['end_date']['year'] . '-' . $data['end_date']['month'] . '-' . $data['end_date']['day'];
        }
        if (isset($data['school-name'])) {
            $return['organizationName'] = $data['school-name'];
        }
        
        return $return;
    }
    
    protected function filterEmployment($data)
    {
        $return = array();
        
        if (isset($data['positions']) && is_array($data['positions'])) {
            $data = $data['positions'][0];

            if (isset($data['start-date'])) {
                $return['startDate'] = $data['start-date']['year'] . '-' . $data['start-date']['month'] . '-' . $data['start-date']['day'];
            }
            if (isset($data['end-date'])) {
                $return['endDate'] = $data['end_date']['year'] . '-' . $data['end_date']['month'] . '-' . $data['end_date']['day'];
            } else if (isset($data['is-current']) && 'true' == $data['is-current']) {
                $return['currentIndicator'] = true;
            }
            if (isset($data['company'])) {
                $return['organizationName'] = $data['company'];
            }
            if (isset($data['summary'])) {
                $return['description'] = $data['summary'];
            }
        }
        return $return;
    }
    
} 

