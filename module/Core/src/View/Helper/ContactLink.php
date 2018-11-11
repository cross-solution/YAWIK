<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** Core view helpers */
namespace Core\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Auth\Entity\Info;

/**
 * This helper generates HTML email link markup from a user info entity.
 *
 * <code>
 *
 *      $this->contactLink($user->getInfo());
 *      // Outputs: <a href="mailto:email@address.net">Fancy Name</a>
 *
 *      $this->contactLink($user->getInfo());
 *      // Outputs: <a href="mailto:email@address.net">email@address.net</a>
 *
 *      $this->contactLink($user->getInfo());
 *      // Outputs: Fancy Name
 * </code>
 * 
 */
class ContactLink extends AbstractHelper
{

    /**
     * generates an email link from a user info entity
     *
     * @param \Auth\Entity\Info $userInfo
     * @param array $attributes
     * @return string
     */
    public function __invoke(Info $userInfo, array $attributes = array())
    {
        $email = $userInfo->getEmail();
        $displayName = $userInfo->getDisplayName(false);
        
        if ($email) {
            $label = $displayName
                       ? $displayName
                       : $email;
            
            $attributesStr = $attributes
                       ? (' ' . $this->createAttributesString($attributes))
                       : '';
            
            return sprintf('<a%s href="mailto:%s">%s</a>', $attributesStr, $email, $label);
        } else {
            return $displayName
                       ? $displayName
                       : '';
        }
    }
    
    protected function createAttributesString(array $attributes)
    {
        $renderer   = $this->getView();
        $escape     = $renderer->plugin('escapehtml');
        $escapeAttr = $renderer->plugin('escapehtmlattr');
        $attr       = array();
        
        foreach ($attributes as $name => $value) {
            $attr[] = $escape($name) . (strlen($value) ? ('="' . $escapeAttr($value) . '"') : '');
        }
        
        return implode(' ', $attr);
    }
}
