<?php
/**
 * YAWIK
 *
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Auth\Entity;

use Core\Entity\AbstractIdentifiableEntity;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Laminas\Session\Container as Session;

/**
 * An Anonymous user.
 *
 * Anonymous user may not be persisted in the database and is used solely
 * for identifying an user through subsequential requests by a session variable.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 *
 * @ODM\Document(collection="users", repositoryClass="Auth\Repository\User")
 * @ODM\HasLifecycleCallbacks
 */
class AnonymousUser extends User
{

    /**
     * Unique identification key (session-based)
     * @var string
     */
    protected $token;

    public function __construct(?string $token = null)
    {
        $this->token = $token;
    }

    /**
     * Prevents this entity from persistence.
     *
     * This is a Doctrine Hook which throws an exception when called.
     *
     * @throws \RuntimeException
     * @ODM\PrePersist
     * @ODM\PreUpdate
     */
    public function preventPersistence()
    {
        throw new \RuntimeException('Anonymous users may not be persisted.');
    }

    /**
     * Gets the anonymous identification key.
     *
     * @return string
     */
    public function getToken()
    {
        if (!$this->token) {
            $session = new Session('Auth');
            if (!$session->token) {
                $session->token = uniqid();
            }
            $this->token = $session->token;
        }
        return $this->token;
    }

    /**
     * {@inheritDoc}
     *
     * Normalizes the token for use in Permission Entity
     *
     * @see \Core\Entity\AbstractIdentifiableEntity::getId()
     */
    public function getId()
    {
        if (!$this->id) {
            $this->setId('token:' . $this->getToken());
        }
        return $this->id;
    }

    /**
     * {@inheritDoc}
     *
     * Always returns "guest"
     *
     * @see \Auth\Entity\User::getRole()
     */
    public function getRole()
    {
        return "guest";
    }

    /**
     * {@inheritDoc}
     *
     * Disabled. Simply returns this instance.
     *
     * @see \Auth\Entity\User::setPassword()
     */
    public function setPassword($password)
    {
        return $this;
    }

    /**
     * {@inheritDoc}
     *
     * Disabled. Simply returns this instance.
     *
     * @see \Auth\Entity\User::setCredential()
     */
    public function setCredential($credential)
    {
        return $this;
    }

    /**
     * {@inheritDoc}
     *
     * Disabled. Simply returns this instance.
     *
     * @see \Auth\Entity\User::setSecret()
     */
    public function setSecret($secret)
    {
        return $this;
    }
}
