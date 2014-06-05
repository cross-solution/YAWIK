<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2104 Cross Solution (http://cross-solution.de)
 * @license   AGPLv3
 */

namespace Applications\Form\Element;

use Core\Form\Element\PolicyCheckFactory;

class PrivacyPolicyFactory extends PolicyCheckFactory 
{
    protected function getElement() {
        return new PrivacyPolicy();
    }
}