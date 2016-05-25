<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Jobs\Entity;

use Core\Entity\EntityInterface;

/**
 * Interface of application tracking settings of a job entity.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0.19
 */
interface AtsModeInterface extends EntityInterface
{
    /**#@+
     * Mode constants.
     *
     * @var string
     */
    const MODE_INTERN = 'intern';
    const MODE_URI    = 'uri';
    const MODE_EMAIL  = 'email';
    const MODE_NONE   = 'none';
    /**#@-*/

    /**
     * Sets the ATS mode.
     *
     * @param string $mode
     *
     * @return self
     */
    public function setMode($mode);

    /**
     * Checks the ATS mode.
     *
     * @param string $mode
     *
     * @return bool
     */
    public function isMode($mode);

    /**
     * Gets the ATS mode.
     *
     * @return string
     */
    public function getMode();

    /**
     * Returns true, if the built-in ATS mode is set.
     *
     * @internal
     *  Convinient method for ::isMode(MODE_INTERN);
     *
     * @return bool
     */
    public function isIntern();

    /**
     * Returns true, if an external link is used for ATS.
     *
     * @internal
     *  Convinient method for ::isMode(MODE_URI);
     *
     *
     * @return bool
     */
    public function isUri();

    /**
     * Returns true, if applications should be send as email.
     *
     * @internal
     *  Convinient method for ::isMode(MODE_EMAIL);
     *
     * @return bool
     */
    public function isEmail();

    /**
     * Returns true, if ATS is not disabled.
     *
     * @return bool
     */
    public function isEnabled();

    /**
     * Returns true, if ATS is completely disabled.
     *
     * @internal
     *  Convinient method for ::isMode(MODE_NONE);
     *
     * @return bool
     */
    public function isDisabled();

    /**
     * Sets the uri to be used for MODE_URI.
     *
     * @param string $uri
     *
     * @return self
     */
    public function setUri($uri);

    /**
     * Gets the uri to be used for MODE_URI.
     *
     * @return string
     */
    public function getUri();

    /**
     * Sets the email to be used for MODE_EMAIL
     *
     * @param string $email
     *
     * @return self
     */
    public function setEmail($email);

    /**
     * Gets the email to be used for MODE_EMAIL.
     *
     * @return string
     */
    public function getEmail();

    /**
     * Enables/Disables the one Click apply feature
     *
     * @param $oneClickApply
     *
     * @return self
     */
    public function setOneClickApply($oneClickApply);

    /**
     * Gets the One-Click-Apply Mode.
     *
     * @return bool
     */
    public function getOneClickApply();

    /**
     * Sets the available Social Networks
     *
     * @param $profiles
     *
     * @return self
     */
    public function setOneClickApplyProfiles(array $profiles);

    /**
     * Gets the available Social Networks
     *
     * @return bool
     */
    public function getOneClickApplyProfiles();
}
