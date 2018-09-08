<?php
/**
 * YAWIK
 *
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license       MIT
 */

namespace Auth\Entity;

use Core\Entity\AbstractIdentifiableEntity;
use Core\Entity\AttachableEntityInterface;
use Core\Entity\AttachableEntityTrait;
use Core\Entity\Collection\ArrayCollection;
use Core\Entity\DraftableEntityInterface;
use Doctrine\Common\Collections\Collection;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Organizations\Entity\OrganizationReferenceInterface;
use Settings\Repository\SettingsEntityResolver;

/**
 * Defines an user model
 *
 * @ODM\Document(collection="users", repositoryClass="Auth\Repository\User")
 *  * @ODM\Indexes({
 *      @ODM\Index(keys={
 *                  "login"="text",
 *                  "role"="text",
 *                    "info.email"="text",
 *                    "info.firstName"="text",
 *                    "info.lastName"="text"
 *                 }, name="fulltext")
 * })
 */
class User extends AbstractIdentifiableEntity implements UserInterface, DraftableEntityInterface, AttachableEntityInterface
{
    use AttachableEntityTrait;

    /**
     * Users login name
     *
     * @var string
     * @ODM\Field(type="string")
     * @ODM\Index(unique=true, sparse=true, order="asc")
     */
    protected $login;

    /**
     * Role of an user. Currently "user" or "recruiter"
     *
     * @ODM\Field(type="string")
     */
    protected $role;

    /**
     * Users contact data.
     *
     * @ODM\EmbedOne(targetDocument="Info")
     */
    protected $info;

    /**
     * Authentification Sessions like oAuth
     * After Authentification with OAuth sessions can be stored like a password/key pair
     *
     * @ODM\EmbedMany(targetDocument="AuthSession")
     */
    protected $authSessions;

    /**
     * Users login password
     *
     * @ODM\Field(type="string")
     */
    protected $credential;

    /**
     * Users primary email address
     *
     * @ODM\Field(type="string")
     */
    protected $email;

    /**
     * pre-shared key, which allows an external application to authenticate
     *
     * @var String
     * @ODM\Field(type="string")
     */
    protected $secret;

    /**
     * Can contain various HybridAuth profiles.
     * Deprecated: replaced by User::$profiles
     *
     * @var array
     * @deprecated
     * @ODM\Field(type="hash")
     */
    protected $profile = array();

    /**
     * Can contain various HybridAuth profiles.
     *
     * @var array
     * @ODM\Field(type="hash")
     */
    protected $profiles = [];

    /** @var array
     * @ODM\EmbedMany(discriminatorField="_entity")
     */
    protected $settings;

    /**
     * This is not a persistent property!
     *
     * @var SettingsEntityResolver
     */
    protected $settingsEntityResolver;

    /**
     * User groups.
     *
     * @var Collection
     * @ODM\ReferenceMany(targetDocument="Group", mappedBy="owner", storeAs="id", cascade="all")
     */
    protected $groups;

    /**
     * User tokens. Is generated when recovering Passwords as a short term key.
     *
     * @var Collection
     * @ODM\EmbedMany(targetDocument="Token")
     */
    protected $tokens;

    /**
     * The organization reference for the user.
     *
     * This field is not stored in the database, but injected on postLoad via
     * {@link \Organizations\Repository\Event\InjectOrganizationReferenceListener}
     *
     * @var OrganizationReferenceInterface
     *
     * @since 0.18
     */
    protected $organization;

    /**
     * Is this entity a draft or not?
     *
     * @var bool
     * @ODM\Field(type="boolean")
     */
    protected $isDraft = false;
    
    /**
     * Status of user
     *
     * @var Status
     * @ODM\EmbedOne(targetDocument="Status")
     * @ODM\Index
     */
    protected $status;

    /**
     * @see http://docs.doctrine-project.org/projects/doctrine-mongodb-odm/en/latest/reference/best-practices.html
     * It is recommended best practice to initialize any business collections in documents in the constructor.
     * {mg: What about lazy loading? Initialize the Collection in the getter, if none is set? Reduce overload.}
     */
    public function __construct()
    {
        $this->status = new Status();
    }

    /**
     * @return bool
     */
    public function isDraft()
    {
        return $this->isDraft;
    }

    /**
     * @param bool $flag
     * @return $this
     */
    public function setIsDraft($flag)
    {
        $this->isDraft = (bool) $flag;

        return $this;
    }


    /** {@inheritdoc} */
    public function setLogin($login)
    {
        $this->login = trim((String)$login);
        return $this;
    }

    /** {@inheritdoc} */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * {@inheritdoc}
     */
    public function setRole($role)
    {
        $this->role = $role;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getRole()
    {
        if (!$this->role) {
            $this->setRole('user');
        }
        return $this->role;
    }

    /**
     * {@inheritdoc}
     */
    public function getRoleId()
    {
        return $this->getRole();
    }

    /**
     * {@inheritdoc}
     */
    public function setInfo(InfoInterface $info)
    {
        $this->info = $info;
        return $this;
    }

    /** {@inheritdoc} */
    public function getInfo()
    {
        if (null == $this->info) {
            $this->setInfo(new Info());
        }
        return $this->info;
    }

    /**
     * @param $key
     * @param $sessionParameter
     * @return $this
     */
    public function updateAuthSession($key, $sessionParameter)
    {
        $notExists = true;

        foreach ($this->authSessions as $authSession) {
            /* @var $authSession AuthSession */
            if ($key == $authSession->getName()) {
                $authSession->setSession($sessionParameter);
                $notExists = false;
            }
        }
        if ($notExists) {
            $authSession = new AuthSession();
            $authSession->setName($key);
            $authSession->setSession($sessionParameter);
            $this->authSessions[] = $authSession;
        }
        return $this;
    }

    /**
     * @param $key
     * @return null
     */
    public function getAuthSession($key)
    {
        $result = null;

        foreach ($this->authSessions as $authSession) {
            /* @var $authSession AuthSession */
            if ($key == $authSession->getName()) {
                $result = $authSession->getSession();
            }
        }
        return $result;
    }

    /**
     * removes a stored Session
     * @param string|null $key providerName, if null, remove all sessions
     * @return $this
     */
    public function removeSessionData($key = null)
    {
        $authSessionRefresh = array();
        foreach ($this->authSessions as $authSession) {
            /* @var $authSession AuthSession */
            if (isset($key) && $key != $authSession->getName()) {
                $authSessionRefresh[] = $authSession;
            }
        }
        $this->authSessions = $authSessionRefresh;
        return $this;
    }

    /** {@inheritdoc} */
    public function getCredential()
    {
        return $this->credential;
    }

    /** {@inheritdoc} */
    public function setPassword($password)
    {
        $filter = new Filter\CredentialFilter();
        $credential = $filter->filter($password);
        return $this->setCredential($credential);
    }

    /** {@inheritdoc} */
    public function setCredential($credential)
    {
        $this->credential = $credential;
        return $this;
    }

    /** {@inheritdoc} */
    public function getSecret()
    {
        if (isset($this->secret)) {
            return $this->secret;
        }
        return $this->credential;
    }

    /** {@inheritdoc} */
    public function setSecret($secret)
    {
        $this->secret = $secret;
        return $this;
    }

    /** {@inheritdoc} */
    public function getEmail()
    {
        return $this->email ?: $this->getInfo()->getEmail();
    }

    /** {@inheritdoc} */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    /** {@inheritdoc} */
    public function setProfile(array $profile)
    {
        $this->profile = $profile;
        return $this;
    }

    /** {@inheritdoc} */
    public function getProfile($provider = null)
    {
        if (!isset($provider))
        {
            return $this->profiles;
        }
        
        return isset($this->profiles[$provider]) ? $this->profiles[$provider] : [];
    }

    /**
     * @param string $provider
     * @param array $data
     * @return \Auth\Entity\User
     */
    public function addProfile($provider, array $data)
    {
        $this->profiles[$provider] = $data;
        return $this;
    }
    
    /**
     * @param string $provider
     * @return \Auth\Entity\User
     */
    public function removeProfile($provider)
    {
        unset($this->profiles[$provider]);
        return $this;
    }

    /** {@inheritdoc} */
    public function setSettingsEntityResolver($resolver)
    {
        $this->settingsEntityResolver = $resolver;
    }

    /** {@inheritdoc} */
    public function getSettings($module)
    {
        if (!isset($module)) {
            throw new \InvalidArgumentException('$module must not be null.');
        }

        if (!$this->settings) {
            $this->settings = new ArrayCollection();
        }

        foreach ($this->settings as $settings) {
            if ($settings->moduleName == $module) {
                return $settings;
            }
        }

        $settings = $this->settingsEntityResolver->getNewSettingsEntity($module);
        $this->settings->add($settings);
        return $settings;
    }

    /** {@inheritdoc} */
    public function getGroups()
    {
        if (!$this->groups) {
            $this->groups = new ArrayCollection();
        }
        return $this->groups;
    }

    /** {@inheritdoc} */
    public function getGroup($name, $create = false)
    {
        $groups = $this->getGroups();
        foreach ($groups as $group) {
            /* @var $group GroupInterface */
            if ($group->getName() == $name) {
                return $group;
            }
        }
        if ($create) {
            $group = new Group($name, $this);
            $groups->add($group);
            return $group;
        }
        return null;
    }

    /**
     * @return Collection
     */
    public function getTokens()
    {
        if (!$this->tokens) {
            $this->tokens = new ArrayCollection();
        }

        return $this->tokens;
    }

    /**
     * @param Collection $tokens
     */
    public function setTokens($tokens)
    {
        $this->tokens = $tokens;
    }

    /**
     * @param OrganizationReferenceInterface $organization
     * @return $this
     */
    public function setOrganization(OrganizationReferenceInterface $organization)
    {
        $this->organization = $organization;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasOrganization()
    {
        /* @var $this->organization \Organizations\Entity\OrganizationReference */
        return $this->organization &&
               $this->organization->hasAssociation();
    }

    /**
     * @return OrganizationReferenceInterface
     */
    public function getOrganization()
    {
        return $this->organization;
    }
    
    /**
     * @return Status
     */
    public function getStatus()
    {
        if (!isset($this->status)) {
            $this->status = new Status();
        }
        
        return $this->status;
    }
    
    /**
     * @param Status $status
     */
    public function setStatus($status)
    {
        if (!$status instanceof Status) {
            $status = new Status($status);
        }
        
        $this->status = $status;
    }
    
    /**
     * @return boolean
     */
    public function isActive()
    {
        return $this->getStatus()->getName() === \Jobs\Entity\StatusInterface::ACTIVE;
    }
}
