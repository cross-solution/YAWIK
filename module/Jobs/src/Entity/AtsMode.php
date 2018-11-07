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
     * @ODM\Field(type="string")
     */
    protected $mode;

    /**
     * The uri to be used in MODE_URI.
     *
     * @var string
     * @ODM\Field(type="string")
     */
    protected $uri;

    /**
     * The email to be used in MODE_EMAIL
     *
     * @var string
     * @ODM\Field(type="string")
     */
    protected $email;

    /**
     * One click apply flag
     *
     * @var bool
     * @ODM\Field(type="bool")
     */
    protected $oneClickApply = false;
    
    /**
     * One click apply profiles
     *
     * @var array
     * @ODM\Field(type="hash")
     */
    protected $oneClickApplyProfiles = [];

    /**
     * Creates a new instance.
     *
     * @param string $mode The ATS mode.
     * @param null   $uriOrEmail Provide the URI for MODE_URI or the email address for MODE_EMAIL.
     *                           Is not used for MODE_INTERN and MODE_NONE.
     *
     * @uses setMode()
     * @uses setUri()
     * @uses setEmail()
     * @throws \InvalidArgumentException if invalid mode is passed.
     */
    public function __construct($mode = self::MODE_INTERN, $uriOrEmail = null)
    {
        $this->setMode($mode);

        if (null !== $uriOrEmail) {
            if (self::MODE_URI == $mode) {
                $this->setUri($uriOrEmail);
            } elseif (self::MODE_EMAIL == $mode) {
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

    /**
     * @param string $mode
     *
     * @return bool
     */
    public function isMode($mode)
    {
        return $mode == $this->mode;
    }

    /**
     * @return string
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * @return bool
     */
    public function isIntern()
    {
        return $this->isMode(self::MODE_INTERN);
    }

    /**
     * @return bool
     */
    public function isUri()
    {
        return $this->isMode(self::MODE_URI);
    }

    /**
     * @return bool
     */
    public function isEmail()
    {
        return $this->isMode(self::MODE_EMAIL);
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return !$this->isDisabled();
    }

    /**
     * @return bool
     */
    public function isDisabled()
    {
        return $this->isMode(self::MODE_NONE);
    }

    /**
     * @param string $uri
     *
     * @return $this
     */
    public function setUri($uri)
    {
        $this->uri = $uri;

        return $this;
    }

    /**
     * @return string
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * @param string $email
     *
     * @return $this
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

	/**
	 * @return bool
	 */
	public function getOneClickApply()
	{
		return $this->oneClickApply;
	}


    /**
     * @param bool $oneClickApply
     *
     * @return $this
     */
	public function setOneClickApply($oneClickApply)
	{
		$this->oneClickApply = (bool)$oneClickApply;
		
		return $this;
	}

	/**
	 * @return array
	 */
	public function getOneClickApplyProfiles()
	{
		return $this->oneClickApplyProfiles;
	}

	/**
	 * @param array $oneClickApplyProfiles
	 * @return AtsMode
	 */
	public function setOneClickApplyProfiles(array $oneClickApplyProfiles)
	{
		$this->oneClickApplyProfiles = $oneClickApplyProfiles;
		
		return $this;
	}
}
