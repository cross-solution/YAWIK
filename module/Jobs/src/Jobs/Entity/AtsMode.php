<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2015 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Jobs\Entity;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Core\Entity\AbstractEntity;

/**
 * Application tracking setting of a job entity.
 *
 * @ODM\EmbeddedDocument
 * @since 0.19
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
class AtsMode extends AbstractEntity implements AtsModeInterface
{
    /**
     * The ATS mode.
     *
     * @var string
     * @ODM\String
     */
    protected $mode;

    /**
     * The uri to be used in MODE_URI.
     *
     * @var string
     * @ODM\String
     */
    protected $uri;

    /**
     * The email to be used in MODE_EMAIL
     *
     * @var string
     * @ODM\String
     */
    protected $email;

    /**
     * Creates a new instance.
     *
     * @param string $mode The ATS mode.
     * @param null   $uriOrEmail Provide the URI for MODE_URI or the email address for MODE_EMAIL.
     *                           Is not used for MODE_INTERN and MODE_NONE.
     *
     * use setMode()
     * use setUri()
     * use setEmail()
     * @throws \InvalidArgumentException if invalid mode is passed.
     */
    public function __construct($mode = self::MODE_INTERN, $uriOrEmail = null)
    {
        $this->setMode($mode);

        if (null !== $uriOrEmail) {
            if (self::MODE_URI == $mode) {
                $this->setUri($uriOrEmail);
            } else if (self::MODE_EMAIL == $mode) {
                $this->setEmail($uriOrEmail);
            }
        }
    }

    /**
     * Sets the ATS mode.
     *
     * @throws \InvalidArgumentException
     */
    public function setMode($mode)
    {
        $validModes = array(
            self::MODE_INTERN,
            self::MODE_URI,
            self::MODE_EMAIL,
            self::MODE_NONE,
        );

        if (!in_array($mode, $validModes)) {
            throw new \InvalidArgumentException('Unknown value for ats mode.');
        }

        $this->mode = $mode;

        return $this;
    }

    public function isMode($mode)
    {
        return $mode == $this->mode;
    }

    public function getMode()
    {
        return $this->mode;
    }

    public function isIntern()
    {
        return $this->isMode(self::MODE_INTERN);
    }

    public function isUri()
    {
        return $this->isMode(self::MODE_URI);
    }

    public function isEmail()
    {
        return $this->isMode(self::MODE_EMAIL);
    }

    public function isEnabled()
    {
        return !$this->isDisabled();
    }

    public function isDisabled()
    {
        return $this->isMode(self::MODE_NONE);
    }

    public function setUri($uri)
    {
        $this->uri = $uri;

        return $this;
    }

    public function getUri()
    {
        return $this->uri;
    }

    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    public function getEmail()
    {
        return $this->email;
    }


}