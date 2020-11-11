<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2020 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** UniqueGroupName.php */
namespace Auth\Form\Validator;

use Auth\Entity\UserInterface;
use Auth\Repository\User;
use Core\Exception\MissingDependencyException;
use Laminas\Validator\AbstractValidator;

/**
 * Validator for uniqueness check of login names.
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
     *
     * @var UserInterface
     */
    protected $currentUser;

    /**
     * {@inheritDoc}
     */
    protected $messageTemplates = array(
        self::NOT_UNIQUE => /*@translate*/ 'The login name "%value%" is already in use.',
    );

    /**
     * Sets the user repository.
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
     * @param UserInterface $currentUser
     */
    public function setCurrentUser(UserInterface $currentUser): void
    {
        $this->currentUser = $currentUser;
    }

    /**
     * Returns true, if the given value is an unique login name.
     *
     * The login name of the currentUser is not checked for uniqueness.
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

        if (($user = $this->users->findByLogin($value))
            && (!$this->currentUser || $this->currentUser !== $user)
        ) {
            $this->error(self::NOT_UNIQUE, $value);

            return false;
        }

        return true;
    }
}
