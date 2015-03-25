<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2015 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Organizations\Entity;

use Auth\Entity\UserInterface;
use Core\Entity\AddressInterface;
use Core\Entity\ModificationDateAwareEntityInterface;
use Core\Entity\PermissionsInterface;
use DateTime;
use Doctrine\Common\Collections\Collection;
use Doctrine\ODM\MongoDB\DocumentManager;
use Zend\Stdlib\Hydrator\HydratorAwareInterface;
use Zend\Stdlib\Hydrator\HydratorInterface;

/**
 * Manages reference to an organization.
 *
 * As OrganizationInterface is also implemented (and all methods are proxied to the "real" organization
 * object), this class can be used as an organization.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test
 *
 */
class OrganizationReference implements OrganizationInterface,
                                       OrganizationReferenceInterface
{
    /*
     * Note: We start property names with an underscore to prevent
     * name collisions with the OrganizationInterface.
     */

    /**
     * The user id of the parent document.
     *
     * @var string
     */
    protected $_userId;

    /**
     * The doctrine document manager.
     *
     * @var \Doctrine\ODM\MongoDB\DocumentManager
     */
    protected $_documentManager;

    /**
     * The organization
     *
     * @var Organization
     */
    protected $_organization;

    /**
     * Reference type.
     *
     * @internal
     *      Initial value is null, so we can determine in {@link load()} if it has already run once.
     *
     * @var string
     */
    protected $_type;

    /**
     * Creates an instance.
     *
     * @param string          $userId
     * @param DocumentManager $documentManager
     */
    public function __construct($userId, DocumentManager $documentManager)
    {
        $this->_userId = $userId;
        $this->_documentManager = $documentManager;
    }

    /**
     * Proxies all calls to the referenced organization entity.
     *
     * @param string $method
     * @param array $args
     *
     * @return mixed
     * @throws \BadMethodCallException
     */
    public function __call($method, $args)
    {
        if (method_exists($this->_organization, $method)) {
            return call_user_func_array(array($this->organization, $method), $args);
        }

        throw new \BadMethodCallException(sprintf(
            'Neither "%s" nor proxied class "%s" have a method called "%s',
            get_class($this), get_class($this->_organization), $method
        ));
    }

    public function isOwner()
    {
        $this->load();

        return self::TYPE_OWNER == $this->_type;
    }

    public function isEmployee()
    {
        $this->load();

        return self::TYPE_EMPLOYEE == $this->_type;
    }

    public function hasAssociation()
    {
        $this->load();

        return self::TYPE_NONE != $this->_type;
    }

    public function getOrganization()
    {
        $this->load();

        return $this->_organization;
    }

    /**
     * Loads the organization from the database and sets the reference type.
     */
    protected function load()
    {
        if (null !== $this->_type) { return; }

        // Is the user the owner of the referenced organization?
        $qb = $this->_documentManager->createQueryBuilder('\Organizations\Entity\Organization');
        $qb->field('user')->equals($this->_userId);
        $q  = $qb->getQuery();
        $org = $q->getSingleResult();

        if ($org) {
            $this->_type = self::TYPE_OWNER;
            $this->_organization = $org;

            return;
        }

        // Is the user employed by the referenced organization?
        $qb = $this->_documentManager->createQueryBuilder('\Organizations\Entity\Organization');
        $qb->field('employees.user')->equals($this->_userId);
        $q  = $qb->getQuery();
        $org = $q->getSingleResult(); // we need only the first.

        if ($org) {
            $this->_type = self::TYPE_EMPLOYEE;
            $this->_organization = $org;

            return;
        }

        // It seems the user is not associated with an organization.
        $this->_type = self::TYPE_NONE;
    }


    protected function proxy($method)
    {
        if (!$this->hasAssociation()) {
            return $this;
        }

        $args = array_slice(func_get_args(), 1);

        return call_user_func_array(array($this->_organization, $method), $args);
    }

    /*
     * We need to implement each method of OrganizationInterface because we want to proxy
     * to a concrete instance of Organization and therefor cannot simply extend.
     */

    /**#@+
     * Proxies to the concrete Organization instance.
     *
     * {@inheritDoc}
     */


    public function __get($property)
    {
        return $this->proxy('__get', $property);
    }

    public function __set($property, $value)
    {
        return $this->proxy('__set', $property, $value);
    }

    public function __isset($property)
    {
        return $this->proxy('__isset', $property);
    }

    public function setHydrator(HydratorInterface $hydrator)
    {
        return $this->proxy('setHydrator', $hydrator);
    }

    public function getHydrator()
    {
        return $this->proxy('getHydrator');
    }

    public function setId($id)
    {
        return $this->proxy('setId', $id);
    }

    public function getId()
    {
        return $this->proxy('getId');
    }

    public function setDateCreated(DateTime $date)
    {
        return $this->proxy('setDateCreated', $date);
    }

    public function getDateCreated()
    {
        return $this->proxy('getDateCreated');
    }

    public function setDateModified($date)
    {
        return $this->proxy('setDateModified', $date);
    }

    public function getDateModified()
    {
        return $this->proxy('getDateModified');
    }

    public function setParent(OrganizationInterface $parent)
    {
        return $this->proxy('setParent', $parent);
    }

    public function getParent()
    {
        return $this->proxy('getParent');
    }

    public function isHiringOrganization()
    {
        return $this->proxy('isHiringOrganization');
    }

    public function setOrganizationName(OrganizationName $organizationNames)
    {
        return $this->proxy('setOrganizationName', $organizationNames);
    }

    public function getOrganizationName()
    {
        return $this->proxy('getOrganizationName');
    }

    public function setAddresses(AddressInterface $addresses)
    {
        return $this->proxy('setAddresses', $addresses);
    }

    public function getAddresses()
    {
        return $this->proxy('getAddresses');
    }

    public function getDescription()
    {
        return $this->proxy('getDescription');
    }

    public function setDescription($description)
    {
        return $this->proxy('setDescription', $description);
    }

    public function setEmployees(Collection $employees)
    {
        return $this->proxy('setEmployees', $employees);
    }

    public function getEmployees()
    {
        return $this->proxy('getEmployees');
    }

    public function getUser()
    {
        return $this->proxy('getUser');
    }

    public function setUser(UserInterface $user)
    {
        $this->_type = null; // force reload of references!
        return $this->proxy('setUser', $user);
    }

    public function getJobs()
    {
        return $this->proxy('getJobs');
    }

    public function getPermissions()
    {
        return $this->proxy('getPermissions');
    }

    public function setPermissions(PermissionsInterface $permissions)
    {
        return $this->proxy('setPermissions', $permissions);
    }

    public function getPermissionsResourceId()
    {
        return $this->proxy('getPermissionsResourceId');
    }

    public function getPermissionsUserIds()
    {
        // TODO: Implement getPermissionsUserIds() method.
    }

    public function getSearchableProperties()
    {
        return $this->proxy('getSearchableProperties');
    }

    public function setKeywords(array $keywords)
    {
        return $this->proxy('setKeywords', $keywords);
    }

    public function clearKeywords()
    {
        return $this->proxy('clearKeywords');
    }

    public function getKeywords()
    {
        return $this->proxy('getKeywords');
    }

    /**#@-*/
}