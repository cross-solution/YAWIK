<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license       MIT
 */

/**  */
namespace Applications\Entity\Validator;

use Applications\Entity\ApplicationInterface;
use Zend\Validator\AbstractValidator;

/**
 * Validates if an application is complete.
 *
 * Currently that means, there is at least an email address given and
 * the privacy policy has been accepted.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
class Application extends AbstractValidator
{

    /**
     * Returns true, if and only if there's at least an email address set and the privacy policy accepted in the application.
     *
     * {@inheritDoc}
     *
     * @param ApplicationInterface $value
     */
    public function isValid($value)
    {
        if (!$value instanceof ApplicationInterface) {
            $this->error('NO_APPLICATION');

            return false;
        }

        $error = false;

        if ('' == $value->getContact()->getEmail()) {
            $error = true;
            $this->error('NO_EMAIL');
        }

        if (!$value->getAttributes()->getAcceptedPrivacyPolicy()) {
            $error = true;
            $this->error('NO_ACCEPT_PP');
        }

        return !$error;
    }
}
