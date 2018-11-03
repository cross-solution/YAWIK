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

/**
 * This helper generates HTML link markup from a URI or email address.
 *
 * <code>
 *
 *      $this->link('http://test.com');
 *      // Outputs: <a href="http://test.com">http://test.com</a>
 *
 *      $this->link('http://test.com', 'Test.Com');
 *      // Outputs: <a href="http://test.com">Test.Com</a>
 *
 *      $this->link('test@host.tld');
 *      // Ouptpus: <a href="mailto:test@host.tld">test@host.tld</a>
 *
 *      $this->link('test@host.tld', 'send mail');
 *      // Outputs: <a href="mailto:test@host.tld">send mail</a>
 * </code>
 *
 * @todo Scramble mail address to prevent spider grabs.
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
class Link extends AbstractHelper
{

    /**
     * generates a link from a text snippet
     *
     * @param string $urlOrEmail
     * @param string $label
     * @return string
     */
    public function __invoke($urlOrEmail, $label = null, array $attributes = array())
    {
        if (null === $label) {
            $label = $urlOrEmail;
        } elseif (is_array($label)) {
            $attributes = $label;
            $label      = $urlOrEmail;
        }
        
        $attributesStr = count($attributes)
                       ? $this->createAttributesString($attributes)
                       : '';
        
        if (false !== strpos($urlOrEmail, '@')) {
            $urlOrEmail = 'mailto:' . $urlOrEmail;
        }
        
        return sprintf('<a %s href="%s">%s</a>', $attributesStr, $urlOrEmail, $label);
    }
    
    protected function createAttributesString(array $attributes)
    {
        $renderer   = $this->getView();
        $escape     = $renderer->plugin('escapehtml');
        $escapeAttr = $renderer->plugin('escapehtmlattr');
        $attr       = array();
        
        foreach ($attributes as $name => $value) {
            $attr[] = $escape($name) . '="' . $escapeAttr($value) . '"';
        }
        
        return implode(' ', $attr);
    }
}
