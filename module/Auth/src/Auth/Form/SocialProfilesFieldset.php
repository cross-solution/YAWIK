<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2104 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** Auth forms */ 
namespace Auth\Form;

use Zend\Form\Fieldset;
use Core\Form\ViewPartialProviderInterface;
use Auth\Form\Hydrator\SocialProfilesHydrator;
use Doctrine\Common\Collections\Collection;

class SocialProfilesFieldset extends Fieldset implements ViewPartialProviderInterface
{
    
    /**
     * View partial name.
     * @var string
     */
    protected $partial = 'auth/form/social-profiles-fieldset';
    
    /**
     * Url spec to fetch profiles from.
     * @var string
     */
    protected $fetchUrl;
    
    /**
     * Url spec to render the profile preview.
     * @var string
     */
    protected $previewUrl;
    

    /**
     * {@inheritDoc}
     * @see \Core\Form\ViewPartialProviderInterface::getViewPartial()
     */
    public function getViewPartial()
    {
        return $this->partial;
    }
    
    /**
     * {@inheritDoc}
     * @see \Core\Form\ViewPartialProviderInterface::setViewPartial()
     */
    public function setViewPartial($partial)
    {
        $this->partial = $partial;
        return $this;
    }

    /**
     * Gets the fetch url specification.
     * 
     * @return string
     */
    public function getFetchUrl ()
    {
        return $this->fetchUrl;
    }
    
    /**
     * Sets the fetch url specification.
     * 
     * @param string $url
     * @return self
     */
    public function setFetchUrl ($url)
    {
        $this->fetchUrl = $url;
        return $this;
    }
    

    /**
     * Sets the preview url specification.
     * 
     * @param string $url
     * @return self
     */
    public function setPreviewUrl ($url)
    {
        $this->previewUrl = $url;
        return $this;
    }
    
    /**
     * Gets the preview url specification.
     * 
     * @return string
     */
    public function getPreviewUrl ()
    {
        return $this->previewhUrl;
    }
    
    /**
     * Get the hydrator.
     * 
     * If no hydrator is set, it sets a {@link SocialProfilesHydrator}.
     * 
     * @return HydratorInterface
     * @see \Zend\Form\Fieldset::getHydrator()
     */
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
     * - 'fetch_url': Fetch url specification
     * - 'preview_url': Preview url specification
     * - 'profiles': Array of profiles specification for use in 
     *               {@link addProfileButton()}
     * {@inheritDoc}
     * @param  array|Traversable $options
     * @return self
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
    
    /**
     * Adds a profile button.
     * 
     * if <b>$options</b> is null, the <b>$name</b> will be used as label.
     * if <b>$options</b> is a string, this string will be used as label.
     * if <b>$options</b> is an array, it must provide a key 'label'.
     * 
     * @param string $name
     * @param null|string|array $options
     * @return self
     */
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
    
    /**
     * {@inheritDoc} 
     * @see \Zend\Form\Element::init()
     */
    public function init()
    {
        $this->setAttribute('class', 'social-profiles-fieldset');
    }
}

