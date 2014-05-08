<?php
/**
 * Cross Applicant Management
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
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
    
    public function getFetchUrl($url)
    {
        if (!$this->fetchUrl) {
            $url = $this->getAttribute('data-fetch-url');
            $this->fetchUrl = $url;
        }
        return $this->fetchUrl;
    }
    
    public function setValue($value)
    {
        return parent::setValue(
            \Zend\Json\Json::encode($value)
        );
    }
    
    public function getValue()
    {
        $value = parent::getValue();
        return \Zend\Json\Json::decode($value);
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
        
        return $this;
    }
}

