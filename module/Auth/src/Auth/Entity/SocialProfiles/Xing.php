<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2104 Cross Solution (http://cross-solution.de)
 * @license   AGPLv3
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
class Xing extends AbstractProfile
{
    protected $name = 'Xing';
    
    protected $config = array(
        'educations' => array(
            'key' => 'educational_background.schools',
        ),
        'employments' => array(
            'key' => 'professional_experience.merged',
        ),
        'properties_map' => array(
            'link' => 'permalink'
        )
    );
    
    protected function getCollection($type)
    {
        $data = $this->data;
        if ('educations' == $type) {
            $key = $this->config['educations']['key'];
            if (isset($data['educational_background']['schools'])) {
                $this->data[$key] = $data['educational_background']['schools']; 
            }
        } else {
            $key = $this->config['employments']['key'];
            $employments = array();
            if (isset($data['professional_experience']['companies'])) {
                $this->data[$key] = $data['professional_experience']['companies'];
            }
        }
        $collection = parent::getCollection($type);
        unset($this->data[$key]);
        return $collection;
    }
    
    protected function filterData($data, $map)
    {
        $return = array();
        foreach ($data as $key => $value) {
            if (null !== $value && array_key_exists($key, $map)) {
                $return[$map[$key]] = $value;
            }
        }
        return $return;
    }
    
    protected function filterEducation($data)
    {
        return $this->filterData($data, array(
            'begin_date' => 'startDate',
            'end_date'   => 'endDate',
            'name'       => 'organizationName',
            'degree'     => 'compentencyName',
        ));
    }
    
    protected function filterEmployment($data)
    {
        $return = $this->filterData($data, array(
            'begin_date'  => 'startDate',
            'end_date'    => 'endDate',
            'description' => 'description',
            'name'        => 'organizationName',
        ));
        $return['currentIndicator'] = !isset($return['endDate']);
        return $return;
    }
    
} 

