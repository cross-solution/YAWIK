<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2104 Cross Solution (http://cross-solution.de)
 * @license   AGPLv3
 */

/** SocialProfilesButton.php */ 
namespace Auth\Form\Element;

use Zend\Form\Element\Button;
use Core\Form\ViewPartialProviderInterface;

class SocialProfilesButton extends Button implements ViewPartialProviderInterface
{
    protected $partial = 'auth/form/social-profiles-button';
    
    protected $fetchUrl;
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
    
    public function setFetchUrl($url)
    {
        $this->fetchUrl = $url;
        $this->setAttribute('data-fetch-url', $url);
        return $this;
    }
    
    public function getFetchUrl()
    {
        if (!$this->fetchUrl) {
            $url = $this->getAttribute('data-fetch-url');
            $this->fetchUrl = $url;
        }
        return $this->fetchUrl;
    }
    
    public function setPreviewUrl($url)
    {
        $this->previewUrl = $url;
        $this->setAttribute('data-preview-url', $url);
        return $this;
    }
    
    public function getPreviewUrl()
    {
        if (!$this->previewUrl) {
            $url = $this->getAttribute('data-preview-url');
            $this->previewUrl = $url;
        }
        return $this->previewUrl;
    }
    
    public function setValue($value)
    {
        return parent::setValue(
            \Zend\Json\Json::encode($value)
        );
    }
    
    public function getValue($raw = false)
    {
        $value = parent::getValue();
        if (!$raw) {
            $value = \Zend\Json\Json::decode($value);
        }
        return $value;
    }
    
    /**
     * Set options. Accepted options are:
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
        
        return $this;
    }
}

