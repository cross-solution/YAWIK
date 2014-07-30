<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2014 Cross Solution (http://cross-solution.de)
 * @license   AGPLv3
 */

/**  */ 
namespace Applications\Entity\Validator;

use Applications\Entity\ApplicationInterface;
use Zend\Validator\AbstractValidator;

/**
 *
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
class Application extends AbstractValidator
{
    
    public function isValid($value)
    {
        if (!$value instanceOf ApplicationInterface) {
            $this->error('NO_APPLICATION');
            return false;
        }
        
        $error = false;
        
        if ('' ==  $value->contact->email) {
            $error = true;
            $this->error('NO_EMAIL');
        }
        
        if (!$value->attributes->acceptedPrivacyPolicy) {
            $error = true;
            $this->error('NO_ACCEPT_PP');
        }
        
        return !$error;
    }
}