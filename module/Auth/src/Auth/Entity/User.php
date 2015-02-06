<?php
/**
 * YAWIK
 *
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license       MIT
 */

namespace Auth\Entity;

use Core\Entity\AbstractIdentifiableEntity;
use Core\Entity\Collection\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Settings\Repository\SettingsEntityResolver;

/**
 * Defines an user model
 *
 * @ODM\Document(collection="users", repositoryClass="Auth\Repository\User")
 */
class User extends AbstractIdentifiableEntity implements UserInterface
{

    /**
     * defines the role of a recruiter
     */
    const ROLE_RECRUITER = 'recruiter';
    /*
     * defines the role of an authenticated user
     */
    const ROLE_USER = 'user';

    /**
     * Users login name
     *
     * @var string
     * @ODM\String @ODM\Index
     */
    protected $login;

    /**
     * Role of an user. Currently "user" or "recruiter"
     *
     * @ODM\String*/
    protected $role;

    /**
     * Users contact data.
     *
     * @ODM\EmbedOne(targetDocument="Info")
     */
    protected $info;

    /**
     * Users login password
     *
     * @ODM\String
     */
    protected $credential;

    /**
     * Users primary email address
     *
     * @ODM\String
     */
    protected $email;

    /**
     * pre-shared key, which allows an external application to authenticate
     *
     * @var String
     * @ODM\String
     */
    protected $secret;

    /**
     * Can contain various HybridAuth profiles.
     *
     * @var array
     * @ODM\Hash
     */
    protected $profile = array();

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
     * @ODM\ReferenceMany(targetDocument="Group", mappedBy="owner", simple=true, cascade="all")
     */
    protected $groups;

    /**
     * User tokens.
     *
     * @var Collection
     * @ODM\EmbedMany(targetDocument="Token")
     */
    protected $tokens;

    /**
     * @see http://docs.doctrine-project.org/projects/doctrine-mongodb-odm/en/latest/reference/best-practices.html
     * It is recommended best practice to initialize any business collections in documents in the constructor.
     * {mg: What about lazy loading? Initialize the Collection in the getter, if none is set? Reduce overload.}
     */
    public function __construct()
    {
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
        return $this->email;
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
    public function getProfile()
    {
        return $this->profile;
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

}