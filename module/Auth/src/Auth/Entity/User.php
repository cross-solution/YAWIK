<?php
/**
 * YAWIK
 *
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

namespace Auth\Entity;

use Core\Entity\AbstractIdentifiableEntity;
use Core\Entity\EntityInterface;
use Core\Entity\RelationEntity;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Core\Entity\Collection\ArrayCollection;
use Settings\Repository\SettingsEntityResolver;

/**
 * The user model
 * 
 * @ODM\Document(collection="users", repositoryClass="Auth\Repository\User")
 */
class User extends AbstractIdentifiableEntity implements UserInterface
{   
   
    /** @var string 
     * @ODM\String */
    protected $login;
    
    /** @ODM\String */
    protected $role;
    
    /** @ODM\EmbedOne(targetDocument="Info") */
    protected $info;
    
    /** @ODM\String */
    protected $credential;
    
    /** @ODM\String */
    protected $email;
    
     /** @var external password for AMS-Interface exclusively
     * @ODM\String*/
    protected $secret;
    
    /** @var array 
     * @ODM\Hash*/
    protected $profile = array();
    
    /** @var array 
     * @ODM\EmbedMany(discriminatorField="_entity") */
    protected $settings;
    
    /**
     * This is not a persistent property!
     * @var SettingsEntityResolver
     */
    protected $settingsEntityResolver;
    /**
     * @see http://docs.doctrine-project.org/projects/doctrine-mongodb-odm/en/latest/reference/best-practices.html
     * It is recommended best practice to initialize any business collections in documents in the constructor.
     * {mg: What about lazy loading? Initialize the Collection in the getter, if none is set? Reduce overload.}
     */
    public function __construct(){
        //$this->info = new Info(); // moved to getter {mg}
    }
    
    /**
     * {@inheritdoc}
     * @return \Auth\Model\User
     */
    public function setLogin($login)
    {
        $this->login = trim((String) $login);
        return $this;
    }
    
    /** {@inheritdoc} */
    public function getLogin()
    {
        return $this->login;
    }
    
    public function setRole($role)
    {
        $this->role = $role;
        return $this;
    }
    
    public function getRole()
    {
        if (!$this->role) {
            $this->setRole('user');
        }
        return $this->role;
    }
    
    public function getRoleId()
    {
        return $this->getRole();
    }
    
    public function setInfo(InfoInterface $info)
    {
        $this->info = $info;
        return $this;
    }
    
    public function getInfo()
    {
        if (null == $this->info) {
            $this->setInfo(new Info());
        }
        return $this->info;
    }
    
    public function getCredential()
    {
        return $this->credential;
    }
    
    public function setPassword($password)
    {
        $filter     = new Filter\CredentialFilter();
        $credential = $filter->filter($password); 
        return $this->setCredential($credential);
    }
    
    public function setCredential($credential) 
    {
        $this->credential = $credential;
        return $this;    
    }
    
    public function getSecret()
    {
        if (isset($this->secret)) {
            return $this->secret;
        }
        return $this->credential;
    }
    
    public function setSecret($secret)
    {
        $this->secret = $secret;
        return $this;
    }
    
    public function getEmail()
    {
        return $this->email;
    }
    
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }
    
    /**
     * {@inheritdoc}
     * @return \Auth\Model\User
     */
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
    
    
    public function setSettingsEntityResolver($resolver)
    {
        $this->settingsEntityResolver = $resolver;
    }
    /** 
     * 
     * 
     */
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
   
}