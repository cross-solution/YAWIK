<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2104 Cross Solution (http://cross-solution.de)
 * @license   AGPLv3
 */

/** SocialProfilesHydrator.php */ 
namespace Auth\Form\Hydrator;

use Zend\Stdlib\Hydrator\AbstractHydrator;
use Core\Entity\Collection\ArrayCollection;

class SocialProfilesHydrator extends AbstractHydrator
{
    
    protected $profileClassMap = array(
        'facebook' => '\Auth\Entity\SocialProfiles\Facebook',
        'xing'     => '\Auth\Entity\SocialProfiles\Xing',
    	'linkedin' => '\Auth\Entity\SocialProfiles\LinkedIn',
    );
    
    public function __construct(array $profileClassMap = array())
    {
        parent::__construct();
        $this->profileClassMap = array_merge($this->profileClassMap, $profileClassMap);
    }
    
    public function hydrate(array $data, $object)
    {
        foreach ($data as $name => $value) {
            if (!isset($this->profileClassMap[$name]) || '' == trim($value)) {
                continue;
            }
            
            if (is_string($value)) {
                $value = \Zend\Json\Json::decode($value, \Zend\Json\Json::TYPE_ARRAY);
            }

            foreach ($object as $p) {
                if ($p->name == $name) {
                    $p->setData($value);
                    continue 2;
                }
            }
                
            $class = $this->profileClassMap[$name];
            $profile = new $class();
            $profile->setData($value);
            $object->add($profile);
        }
        return $object;
    }
    
    public function extract($object)
    {
        $return = array();
        foreach ($object as $profile) {
            $return[$profile->getName()] = $profile->getData();
        }
        return $return;
    }
}
