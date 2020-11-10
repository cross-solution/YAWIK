<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** UniqueGroupName.php */
namespace Auth\Form\Validator;

use Auth\Repository\User;
use Core\Exception\MissingDependencyException;
use Laminas\Validator\AbstractValidator;

/**
 * Validator for uniqueness check of group names.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
class UniqueLoginName extends AbstractValidator
{
    /**
     * Messages
     * @var string
     */
    const NOT_UNIQUE = 'NotUnique';

    /**
     * The user repository
     *
     * @var User
     */
    protected $users;

    /**
     * The current user
     */
    protected $currentUser;

    /**
     * {@inheritDoc}
     */
    protected $messageTemplates = array(
        self::NOT_UNIQUE => /*@translate*/ 'The login name "%value%" is already in use.',
    );

    /**
     * Sets the user the group should belong to.
     *
     * @param User $repository
     * @return \Auth\Form\Validator\UniqueGroupName
     */
    public function setUserRepository(User $repository)
    {
        $this->users = $repository;
        return $this;
    }

    /**
     * Set The current user
     *
     * @param mixed $currentUser
     */
    public function setCurrentUser($currentUser): void
    {
        $this->currentUser = $currentUser;
    }

    /**
     * Returns true, if the given value is unique among the groups of the user.
     *
     * Also returns true, if the given value equals the {@link $allowName}.
     *
     * @param string $value
     * @return bool
     * @see \Laminas\Validator\ValidatorInterface::isValid()
     */
    public function isValid($value)
    {
        if (!$this->users) {
            throw new MissingDependencyException(User::class, $this);
        }

        if (count($this->users->findByLogin($value))
            && (!$this->currentUser || $this->currentUser->getLogin() != $value)
        ) {
            $this->error(self::NOT_UNIQUE, $value);

            return false;
        }

        return true;
    }
}
