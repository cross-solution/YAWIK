<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Auth\Entity;

use Core\Entity\AbstractEntity;
use Core\Entity\EntityInterface;
use Core\Entity\FileInterface;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * personal information of a user.
 *
 * @ODM\EmbeddedDocument
 */
class Info extends AbstractEntity implements InfoInterface
{
   
    
    /**
     * Day of birth of the user
     *
     * @var string
     * @ODM\String */
    protected $birthDay;
    
    /**
     * Month of birth of the user
     *
     * @var string
     * @ODM\String */
    protected $birthMonth;

    /**
     * Year of birth of the user
     *
     * @var string
     * @ODM\String */
    protected $birthYear;
    
    /**
     * primary email of the user.
     *
     * @var string
     * @ODM\String */
    protected $email;

    /**
     * Flag, if primary email is verified
     *
     * @var boolean
     * @ODM\Boolean
     */
    protected $emailVerified;
    
    /**
     * Firstname of the user
     *
     * @var string
     * @ODM\String */
    protected $firstName;
    
    /**
     * Gender of the user
     *
     * @var string
     * @ODM\String */
    protected $gender;
    
    /**
     * house number of the users address
     *
     * @var string
     * @ODM\String */
    protected $houseNumber;
    
    /**
     * Lastname of the user
     *
     * @var string
     * @ODM\String */
    protected $lastName;
    
    /**
     * phone number of the user
     *
     * @var string
     * @ODM\String */
    protected $phone;
    
    /**
     * postal code of the users address
     *
     * @var string
     * @ODM\String */
    protected $postalCode;

    /**
     * city of the users address
     *
     * @var string
     * @ODM\String */
    protected $city;
    
    /**
     * the photo of an users profile
     *
     * @var FileInterface
     * @ODM\ReferenceOne(targetDocument="UserImage", simple=true, nullable=true, cascade={"all"})
     */
    protected $image;
    
    /**
     * street of the users address
     *
     * @var string
     * @ODM\String */
    protected $street;
    
    /**
     * {@inheritdoc}
     *
     * @return \Auth\Entity\User
     */
    public function setBirthDay($birthDay)
    {
        $this->birthDay=$birthDay;
        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getBirthDay()
    {
        return $this->birthDay;
    }
    
    /**
     * {@inheritdoc}
     *
     * @return \Auth\Entity\User
     */
    public function setBirthMonth($birthMonth)
    {
        $this->birthMonth=$birthMonth;
        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getBirthMonth()
    {
        return $this->birthMonth;
    }
    
    /**
     * {@inheritdoc}
     *
     * @return \Auth\Entity\User
     */
    public function setBirthYear($birthYear)
    {
        $this->birthYear=$birthYear;
        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getBirthYear()
    {
        return $this->birthYear;
    }
    
    /**
     * {@inheritdoc}
     *
     * @return \Auth\Entity\Info
     */
    public function setEmail($email)
    {
        $this->email = trim((String)$email);
        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * {@inheritdoc}
     *
     * @return bool
     */
    public function isEmailVerified()
    {
        return $this->emailVerified;
    }

    /**
     * {@inheritdoc}
     *
     * @param bool $emailVerified
     * @return $this
     */
    public function setEmailVerified($emailVerified)
    {
        $this->emailVerified = $emailVerified;
        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @return \Auth\Entity\User
     */
    public function setFirstName($firstName)
    {
        $this->firstName = trim((String)$firstName);
        return $this;
    }


    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getGender()
    {
        return $this->gender;
    }
    
    /**
     * {@inheritdoc}
     *
     * @return \Auth\Entity\User
     */
    public function setGender($gender)
    {
        $this->gender = trim((String)$gender);
        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }
    
    /**
     * {@inheritdoc}
     *
     * @return \Auth\Entity\User
     */
    public function setHouseNumber($houseNumber)
    {
        $this->houseNumber=$houseNumber;
        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getHouseNumber()
    {
        return $this->houseNumber;
    }
    
    /**
     * {@inheritdoc}
     *
     * @return \Auth\Entity\User
     */
    public function setLastName($name)
    {
        $this->lastName = trim((String) $name);
        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    public function getDisplayName($emailIfEmpty = true)
    {
        if (!$this->lastName) {
            return $emailIfEmpty ? $this->email : '';
        }
        return ($this->firstName ? $this->firstName . ' ' : '') . $this->lastName;
    }
    
    /**
     * {@inheritdoc}
     *
     * @return \Auth\Entity\User
     */
    public function setPhone($phone)
    {
        $this->phone = (String) $phone;
        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }
    
    /**
     * {@inheritdoc}
     *
     * @return \Auth\Entity\User
     */
    public function setPostalCode($postalCode)
    {
        $this->postalCode = (String) $postalCode;
        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getPostalCode()
    {
        return $this->postalCode;
    }
    
    /**
     * {@inheritdoc}
     *
     * @return \Auth\Entity\User
     */
    public function setCity($city)
    {
        $this->city = (String) $city;
        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * {@inheritdoc}
     *
     * @param UserImage $image
     * @return $this
     */
    public function setImage(EntityInterface $image = null)
    {
        $this->image = $image;
        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @return UserImage
     */
    public function getImage()
    {
        return $this->image;
    }
    /**
     * {@inheritdoc}
     *
     * @return \Auth\Entity\User
     */
    public function setStreet($street)
    {
        $this->street=$street;
        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getStreet()
    {
        return $this->street;
    }
}
