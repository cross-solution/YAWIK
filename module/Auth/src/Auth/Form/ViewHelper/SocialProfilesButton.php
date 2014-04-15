<?php
/**
 * Cross Applicant Management
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   AGPLv3
 */

/** SocialProfilesFieldset.php */ 
namespace Auth\Form\ViewHelper;

use Zend\Form\ElementInterface;
use Zend\Form\View\Helper\AbstractHelper;

class SocialProfilesButton extends AbstractHelper
{
    
    public function __invoke(ElementInterface $element)
    {
        return $this->render($element);
    }
    
    public function render(ElementInterface $element)
    {
        $name = $element->getName();
        $value = $element->getValue();
        $icon = $element->getOption('icon', $name);
        $element->setName(null)->setValue(null);
        $attributes = array_merge(
            $element->getAttributes(), array('class' => 'btn btn-default social-profiles-button')
        );
        $attrStr = $this->createAttributesString($attributes);
        $markup = sprintf(
            '<button %s><span class="yk-icon yk-icon-plus"></span> %s</button><textarea id="%s" class="hide" name="%s">%s</textarea>',
            $attrStr, 
            $this->translator->translate($element->getLabel(), $this->getTranslatorTextDomain()),
            $element->getAttribute('id') . '-data', $name, $value
        );
        
        return $markup;
    }
}

