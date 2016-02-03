<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** Facebook.php */
namespace Auth\Entity\SocialProfiles;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Core\Entity\AbstractEntity;
use Core\Entity\Collection\ArrayCollection;
use Cv\Entity\Education;

/**
 *
 * @ODM\EmbeddedDocument
 */
class Facebook extends AbstractProfile
{
    protected $name = 'Facebook';
    
    protected $config = array(
        'educations' => array(
            'key' => 'education',
        ),
        'employments' => array(
            'key' => 'work',
        ),
        'properties_map' => array(
            'link' => 'link',
        ),
    );
    
    protected function filterEducation($data)
    {
        $return = array();
        if (isset($data['year']['name'])) {
            $return['endDate'] = $data['year']['name'];
        }
        if (isset($data['school']['name'])) {
            $return['organizationName'] = $data['school']['name'];
        }
        
        return $return;
    }
    
    protected function filterEmployment($data)
    {
        $return = array();
        
        if (isset($data['start_date'])) {
            $return['startDate'] = $data['start_date'];
        }
        if (isset($data['end_date'])) {
            $return['endDate'] = $data['end_date'];
        } else {
            $return['currentIndicator'] = true;
        }
        if (isset($data['employer']['name'])) {
            $return['organizationName'] = $data['employer']['name'];
        }
        if (isset($data['description'])) {
            $return['description'] = $data['description'];
        }
        
        return $return;
    }
}
