<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    weitz@cross-solution.de
 */

namespace Auth\Entity;

use Core\Entity\AbstractEntity;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * @ODM\EmbeddedDocument
 */
class AuthSession extends AbstractEntity
{
    /**
     * Name of the session
     *
     * @ODM\Field(type="string")
     */
    protected $name;

    /**
     * Hash of the session
     *
     * @var array
     * @ODM\Field(type="hash")
     */
    protected $session = array();

    /**
     * Last modification date of the session
     *
     * @var \Datetime
     * @ODM\Field(type="date")
     */
    protected $modificationDate;

    /**
     * @param $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param $session
     * @return $this
     */
    public function setSession($session)
    {
        if (is_string($session)) {
            // the session can already be serialized
            $session = array('sessionKeyString' => $session);
        }
        $this->session = $session;
        $this->setModificationDate();
        return $this;
    }

    /**
     * @return array
     */
    public function getSession()
    {
        $session = $this->session;
        if (array_key_exists('sessionKeyString', $session) && count($session) == 1) {
            $session = $session['sessionKeyString'];
        }
        return $session;
    }

    /**
     * @return \Datetime
     */
    public function getModificationDate()
    {
        return $this->modificationDate;
    }

    /**
     * @param \Datetime|string $modificationDate
     *
     * @return self
     */
    public function setModificationDate($modificationDate = null)
    {
        if (!isset($modificationDate)) {
            $modificationDate = new \DateTime();
        }
        if (is_string($modificationDate)) {
            $modificationDate = new \DateTime($modificationDate);
        }
        $this->modificationDate = $modificationDate;
        return $this;
    }
}
