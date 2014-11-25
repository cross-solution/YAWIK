<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2014 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** Auth forms */ 
namespace Auth\Form;


use Core\Form\DisableElementsCapableInterface;
use Zend\Form\Fieldset;
use Core\Form\ViewPartialProviderInterface;
use Auth\Form\Hydrator\SocialProfilesHydrator;
use Doctrine\Common\Collections\Collection;
use Zend\Form\FieldsetInterface;

class SocialProfilesFieldset extends Fieldset implements ViewPartialProviderInterface, DisableElementsCapableInterface
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

    public function getViewPartial()
    {
        return $this->partial;
    }

    public function setViewPartial($partial)
    {
        $this->partial = $partial;
        return $this;
    }

    public function setIsDisableCapable($flag)
    {
        $this->options['is_disable_capable'] = $flag;

        return $this;
    }

    public function isDisableCapable()
    {
        return isset($this->options['is_disable_capable'])
               ? $this->options['is_disable_capable'] : false;
    }

    public function setIsDisableElementsCapable($flag)
    {
        $this->options['is_disable_elements_capable'] = $flag;

        return $this;
    }

    public function isDisableElementsCapable()
    {
        return isset($this->options['is_disable_elements_capable'])
               ? $this->options['is_disable_elements_capable'] : true;
    }

    public function disableElements(array $map)
    {
        if (!$this->isDisableElementsCapable()) {
            return $this;
        }

        foreach ($map as $key => $name) {

            if (is_numeric($key)) {
                $key = $name;
                $name = null;
            }

            if (!$this->has($key)) {
                continue;
            }

            $element = $this->get($key);

            if (null === $name) {
                if (false !== $element->getOption('is_disable_capable')) {
                    $this->remove($key);
                }
                continue;
            }

            if ($element instanceOf FieldsetInterface
                && $element instanceOf DisableElementsCapableInterface
                && $element->isDisableElementsCapable()
            ) {
                $element->disableElements($name);
            }
        }
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
        return $this->previewUrl;
    }
    
    public function getHydrator()
    {
        if (!$this->hydrator) {
            $this->setHydrator(new SocialProfilesHydrator());
        }
        return $this->hydrator;
    }

    public function allowObjectBinding($object)
    {
        return ($object instanceOf Collection);
    }
    
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

        $options['disable_capable']['description'] = sprintf(
            /*@translate*/ 'Allow users to attach their %s profile.',
            $options['label']
        );
        
        $this->add(array(
            'type' => 'Auth/SocialProfilesButton',
            'name' => $name,
            'options' => $options,
        ));
        return $this;
    }

    public function init()
    {
        $this->setAttribute('class', 'social-profiles-fieldset');
    }
}

