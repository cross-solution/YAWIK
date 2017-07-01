<?php
/**
 * YAWIK
 *
 * @filesource
 * @license    MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */

/** */
namespace Organizations\Controller\Plugin;

use Auth\Repository\User as UserRepository;
use Auth\Service\UserUniqueTokenGenerator;
use Core\Controller\Plugin\Mailer;
use Core\Exception\MissingDependencyException;
use Zend\I18n\Translator\Translator;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\Validator\EmailAddress;

/**
 * Handles invitation of users / employees.
 *
 * - Creates a new user draft, if necessary.
 * - Sends the invitation mail.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since  0.19
 */
class InvitationHandler extends AbstractPlugin
{

    /**
     * Translator
     *
     * @var Translator
     */
    protected $translator;

    /**
     * Email address validator
     *
     * @var EmailAddress
     */
    protected $emailValidator;

    /**
     * User repository
     *
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * User token generator
     *
     * @var UserUniqueTokenGenerator
     */
    protected $userTokenGenerator;

    /**
     * Mailer plugin
     *
     * @var Mailer
     */
    protected $mailerPlugin;

    /**
     * Gets the translator
     *
     * @return \Zend\I18n\Translator\Translator
     * @throws MissingDependencyException
     */
    public function getTranslator()
    {
        if (!$this->translator) {
            throw new MissingDependencyException('\Zend\I18n\Translator\Translator', $this);
        }

        return $this->translator;
    }

    /**
     * Sets the translator
     *
     * @param \Zend\I18n\Translator\Translator $translator
     *
     * @return self
     */
    public function setTranslator($translator)
    {
        $this->translator = $translator;

        return $this;
    }

    /**
     * Gets the email validator.
     *
     * @return \Zend\Validator\EmailAddress
     * @throws MissingDependencyException
     */
    public function getEmailValidator()
    {
        if (!$this->emailValidator) {
            throw new MissingDependencyException('\Zend\Validate\EmailAddress', $this);
        }

        return $this->emailValidator;
    }

    /**
     * Sets the email validator.
     *
     * @param \Zend\Validator\EmailAddress $emailValidator
     *
     * @return self
     */
    public function setEmailValidator($emailValidator)
    {
        $this->emailValidator = $emailValidator;

        return $this;
    }

    /**
     * Gets the user repository.
     *
     * @return \Auth\Repository\User
     * @throws MissingDependencyException
     */
    public function getUserRepository()
    {
        if (!$this->userRepository) {
            throw new MissingDependencyException('\Auth\Repository\User', $this);
        }

        return $this->userRepository;
    }

    /**
     * Sets the user repository.
     *
     * @param \Auth\Repository\User $userRepository
     *
     * @return self
     */
    public function setUserRepository($userRepository)
    {
        $this->userRepository = $userRepository;

        return $this;
    }

    /**
     * Gets the user token generator.
     *
     * @return \Auth\Service\UserUniqueTokenGenerator
     * @throws MissingDependencyException
     */
    public function getUserTokenGenerator()
    {
        if (!$this->userTokenGenerator) {
            throw new MissingDependencyException('\Auth\Service\UserUniqueTokenGenerator', $this);
        }

        return $this->userTokenGenerator;
    }

    /**
     * Sets the user token generator.
     *
     * @param \Auth\Service\UserUniqueTokenGenerator $userTokenGenerator
     *
     * @return self
     */
    public function setUserTokenGenerator($userTokenGenerator)
    {
        $this->userTokenGenerator = $userTokenGenerator;

        return $this;
    }

    /**
     * Gets the mailer plugin.
     *
     * @return \Core\Controller\Plugin\Mailer
     * @throws MissingDependencyException
     */
    public function getMailerPlugin()
    {
        if (!$this->mailerPlugin) {
            throw new MissingDependencyException('\Core\Controller\Plugin\Mailer', $this);
        }

        return $this->mailerPlugin;
    }

    /**
     * Sets the mailer plugin.
     *
     * @param \Core\Controller\Plugin\Mailer $mailerPlugin
     *
     * @return self
     */
    public function setMailerPlugin(Mailer $mailerPlugin)
    {
        $this->mailerPlugin = $mailerPlugin;

        return $this;
    }

    /**
     * Processes the invitation of an user by an organization owner.
     *
     * Validators the email address, loads or creates the user and finallly sends
     * the mail.
     *
     * The return value is meant to be used as an array of variables passed to a
     * ViewModelInterface instance.
     *
     * @param string $email
     *
     * @return array
     */
    public function process($email)
    {
        $translator = $this->getTranslator();

        if (!$this->validateEmail($email)) {
            return array(
                'ok'      => false,
                'message' => $translator->translate('Email address is invalid.')
            );
        }

        $userAndToken = $this->loadOrCreateUser($email);

        try {
            $mailer = $this->getMailerPlugin();
            $mailer('Organizations/InviteEmployee', $userAndToken, true);

        } catch (\Exception $e) {
            return array(
                'ok'      => false,
                'message' => $translator->translate(trim('Sending invitation mail failed. '.$e->getMessage()))
            );
        }

        $user = $userAndToken['user'];

        /* @var $user \Auth\Entity\User
         * @var $info \Auth\Entity\InfoInterface
         */

        $info = $user->getInfo();

        return array(
            'ok'     => true,
            'result' => array(
                'userId'    => $user->getId(),
                'userName'  => $info->getDisplayName(),
                'userEmail' => $email
            )
        );
    }

    /**
     * Validates an email address.
     *
     * @param string $email
     *
     * @return bool
     */
    protected function validateEmail($email)
    {
        if (!$email) {
            return false;
        }

        $validator = $this->getEmailValidator();

        return $validator->isValid($email);
    }

    /**
     * Loads or creates an user.
     *
     * Tries to load an user from the database, and creates a new
     * user draft, if no user was found.
     * A token will be generated to authenticate this user in further interactions
     * (with other parts of this application).
     *
     * @param string $email
     *
     * @return array An array with the keys 'user' and 'token', where 'user' is an UserInterface instance and
     *               'token' is the generated token.
     */
    protected function loadOrCreateUser($email)
    {
        $repository = $this->getUserRepository();
        $generator  = $this->getUserTokenGenerator();

        /* @var $user \Auth\Entity\User */
        $user = $repository->findByEmail(
            $email, /*do not check isDraft flag */
            null
        );

        if (!$user) {
            $user = $repository->create();
            $user->setEmail($email)
                 ->setLogin($email)
                 ->setRole(\Auth\Entity\User::ROLE_RECRUITER)
                 ->setIsDraft(true);
            $info = $user->getInfo();
            /* @var $info \Auth\Entity\InfoInterface */
            $info->setEmail($email);
        }

        $token = $generator->generate(
            $user, /* daysToLive */
            7
        ); // will store user!

        return array('user' => $user, 'token' => $token);
    }
}
