<?php
/**
 * Cross Applicant Management
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

namespace Core\View\Helper;

use Zend\View\Helper\AbstractHelper;


class Link extends AbstractHelper {

    /**
     * generates a link from a text snippet
     *
     * @param string $gender
     * @return string
     */
    public function __invoke($urlOrEmail, $label=null)
    {
        if (null === $label) {
            $label = $urlOrEmail;
        }
        
        if (false !== strpos($urlOrEmail, '@')) {
            $urlOrEmail = 'mailto:' . $urlOrEmail;
        }
        
        return sprintf('<a href="%s">%s</a>', $urlOrEmail, $label);
        
    }
}

