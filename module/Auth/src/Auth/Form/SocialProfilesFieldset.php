<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2104 Cross Solution (http://cross-solution.de)
 * @license   AGPLv3
 */

/** AttachSocialProfilesFieldset.php */ 
namespace Auth\Form;

use Core\Form\ButtonsFieldset;
use Zend\Form\Fieldset;
use Core\Form\ViewPartialProviderInterface;
use Auth\Form\Hydrator\SocialProfilesHydrator;
use Doctrine\Common\Collections\Collection;

class SocialProfilesFieldset extends Fieldset implements ViewPartialProviderInterface
{
    
    protected $partial = 'auth/form/social-profiles-fieldset';
    
    protected $fetchUrl;
    protected $previewUrl;
    
    protected $isInitialized = false;
    
    public function getViewPartial()
    {
        return $this->partial;
    }
    
    public function setViewPartial($partial)
    {
        $this->partial = $partial;
        return $this;
    }
    


    /**
     * @return the $fetchUrl
     */
    public function getFetchUrl ()
    {
        return $this->fetchUrl;
    }
    
    /**
     * @param field_type $fetchUrl
     */
    public function setFetchUrl ($url)
    {
        $this->fetchUrl = $url;
        return $this;
    }
    

    /**
     * @param field_type $fetchUrl
     */
    public function setPreviewUrl ($url)
    {
        $this->previewUrl = $url;
        return $this;
    }
    /**
     * @return the $fetchUrl
     */
    public function getPreviewUrl ()
    {
        return $this->previewhUrl;
    }
    
   

    public function getHydrator()
    {
        if (!$this->hydrator) {
            $this->setHydrator(new SocialProfilesHydrator());
        }
        return $this->hydrator;
    }
    
    /**
     * Checks if the object can be set in this fieldset
     *
     * @param object $object
     * @return bool
     */
    public function allowObjectBinding($object)
    {
        return ($object instanceOf Collection);
    }
    
    /**
     * Set options for a fieldset. Accepted options are:
     * - 
     *
     * @param  array|Traversable $options
     * @return Element|ElementInterface
     * @throws Exception\InvalidArgumentException
     */
    public function setOptions($options)
    {
        parent::setOptions($options);
    
        if (isset($options['fetch_url'])) {
            $this->setFetchUrl($options['fetch_url']);
        }
        
        if (isset($options['preview_url'])) {
            $this->setPreviewUrl($options['preview_url']);
        }
        
        if (isset($options['profiles'])) {
            foreach ($options['profiles'] as $name => $options) {
                $this->addProfileButton($name, $options);
            }
        }
    
        return $this;
    }
    
    public function addProfileButton($name, $options = null)
    {
        if (null === $options) {
            $options['label'] = ucfirst($name);
        } else if (is_string($options)) {
            $options = array('label' => $options);
        }
        
        if (!isset($options['fetch_url'])) {
            $options['fetch_url'] = sprintf($this->fetchUrl, $name);
        }
        
        if (!isset($options['preview_url']) && $this->previewUrl) {
            $options['preview_url'] = sprintf($this->previewUrl, $options['label']);
        }
        
        if (!isset($options['icon'])) {
            $options['icon'] = $name;
        }
        
        $this->add(array(
            'type' => 'Auth/SocialProfilesButton',
            'name' => $name,
            'options' => $options,
        ));
        return $this;
    }
    
    public function init()
    {
        $this->setLabel(/*@translate*/ 'Social Profiles');
        $this->setAttribute('class', 'social-profiles-fieldset');
    }
}

