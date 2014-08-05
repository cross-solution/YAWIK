<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2104 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** AbstractProfile.php */ 
namespace Auth\Controller\Plugin\SocialProfiles;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Auth\Entity\SocialProfiles\Facebook;

abstract class AbstractAdapter extends AbstractPlugin
{
    
    public function __invoke($api)
    {
        return $this->fetch($api);
    }
    
    public function fetch($api)
    {
        $result  = $this->queryApi($api);
        if (!$result) {
            return false;
        }
        
        $profile = $this->getProfile();
        $profile->setData($result); 
                
        return $profile;
    }
    
    abstract protected function queryApi($api);
    
    protected function getProfile()
    {
        $class   = $this->getProfileClass();
        $profile = new $class();
        
        return $profile;
    }
    
    protected function getProfileClass()
    {
        $class = get_class($this);
        $class = explode('\\', $class);
        $class = array_pop($class); 
        $class = '\\Auth\\Entity\\SocialProfiles\\' . $class;
        
        return $class;
    }
}

