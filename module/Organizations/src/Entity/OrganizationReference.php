<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */

/** */
namespace Organizations\Entity;

use Auth\Entity\UserInterface;
use Core\Entity\EntityInterface;
use Core\Entity\MetaDataProviderTrait;
use Core\Entity\PermissionsInterface;
use Doctrine\Common\Collections\Collection;
use Organizations\Repository\Organization as OrganizationRepository;
use Zend\Hydrator\HydratorInterface;

/**
 * Manages reference to an organization.
 *
 * As OrganizationInterface is also implemented (and all methods are proxied to the "real" organization
 * object), this class can be used as an organization.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo update test
 */
class OrganizationReference implements
    OrganizationInterface,
    OrganizationReferenceInterface
{
    use MetaDataProviderTrait;

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
     * The organization repository.
     *
     * @var \Organizations\Repository\Organization
     */
    protected $_repository;

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
     * @param string                 $userId
     * @param OrganizationRepository $repository
     */
    public function __construct($userId, OrganizationRepository $repository)
    {
        $this->_userId = $userId;
        $this->_repository = $repository;
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
        if (null !== $this->_type) {
            return;
        }

        // Is the user the owner of the referenced organization?
        $org = $this->_repository->findByUser($this->_userId);

        if ($org) {
            $this->_type = self::TYPE_OWNER;
            $this->_organization = $org;

            return;
        }

        // Is the user employed by the referenced organization?
        $org = $this->_repository->findByEmployee($this->_userId);

        if ($org) {
            $this->_type = self::TYPE_EMPLOYEE;
            $this->_organization = $org;

            return;
        }

        // It seems the user is not associated with an organization.
        $this->_type = self::TYPE_NONE;
    }


    /**
     * Executes a proxy call to the associated organization entity.
     *
     * Does nothing, if no organization is available.
     *
     * Call it like:
     * <pre>
     *   $this->proxy($method[, $arg1[, $arg2[, ...]]]);
     * </pre>
     *
     * @param $method
     *
     * @return self|mixed
     */
    protected function proxy($method)
    {
        if (!$this->hasAssociation()) {
            return $this;
        }

        $args = array_slice(func_get_args(), 1);

        $return = call_user_func_array(array($this->_organization, $method), $args);

        return ($return === $this->_organization) ? $this : $return;
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

    public function notEmpty($property, array $args=[])
    {
        return $this->proxy('notEmpty', $args);
    }

    public function hasProperty($property, $mode = self::PROPERTY_STRICT)
    {
        return $this->proxy('hasProperty', $mode);
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

    public function setDateCreated($date)
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

    public function getParent($returnSelf = false)
    {
        return $this->proxy('getParent', $returnSelf);
    }

    public function setContact(EntityInterface $contact = null)
    {
        return $this->proxy('setContact', $contact);
    }

    public function getContact()
    {
        return $this->proxy('getContact');
    }

    public function isHiringOrganization()
    {
        return $this->proxy('isHiringOrganization');
    }

    public function getHiringOrganizations()
    {
        return $this->proxy('getHiringOrganizations');
    }

    public function setImage(OrganizationImage $image)
    {
        return $this->proxy('setImage', $image);
    }

    public function getImage()
    {
        return $this->proxy('getImage');
    }

    public function getImages()
    {
        return $this->proxy('getImages');
    }

    public function setImages(\Core\Entity\ImageSet $images)
    {
        return $this->proxy('setImages', $images);
    }

    public function setOrganizationName(OrganizationName $organizationNames)
    {
        return $this->proxy('setOrganizationName', $organizationNames);
    }

    public function getOrganizationName()
    {
        return $this->proxy('getOrganizationName');
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

    public function getEmployeesByRole($role)
    {
        return $this->proxy('getEmployeesByRole', $role);
    }

    public function getEmployee($userOrId)
    {
        return $this->proxy('getEmployee', $userOrId);
    }

    public function setExternalId($externalId)
    {
        return $this->proxy('setExternalId', $externalId);
    }

    public function getExternalId()
    {
        return $this->proxy('getExternalId');
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

    public function getPermissionsUserIds($type = null)
    {
        return $this->proxy('getPermissionsUSerIds', $type);
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

    public function getTemplate()
    {
        return $this->proxy('getTemplate');
    }

    public function setTemplate(TemplateInterface $template)
    {
        return $this->proxy('setTemplate', $template);
    }

    public function getWorkflowSettings()
    {
        return $this->proxy('getWorkflowSettings');
    }

    public function setWorkflowSettings($workflowSettings)
    {
        return $this->proxy('setWorkflowSettings', $workflowSettings);
    }

    /**#@-*/
}
