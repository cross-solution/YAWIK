<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** AbstractProfile.php */
namespace Auth\Entity\SocialProfiles;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Core\Entity\AbstractIdentifiableEntity;
use Core\Entity\Collection\ArrayCollection;
use Cv\Entity\Education;
use Core\Entity\Hydrator\EntityHydrator;

/**
 * Social Profile Entity
 *
 * Provides methods to normalize profile data.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @ODM\MappedSuperclass @ODM\HasLifecycleCallbacks
 */
abstract class AbstractProfile extends AbstractIdentifiableEntity implements ProfileInterface
{
    
    /**
     * Name of the profile.
     * Should be the name of the social network.
     *
     * @var String
     * @ODM\Field(type="string")
     */
    protected $name;
    
    
    /**
     * URL to the profile page.
     *
     * @var String
     * @ODM\Field(type="string")
     */
    protected $link;
    
    /**
     * Raw profile data (API result)
     *
     * @var array
     * @ODM\Field(type="hash")
     */
    protected $data = array();
    
    /**
     * Normalized educations collection.
     *
     * @var Collection
     * @ODM\EmbedMany(targetDocument="\Cv\Entity\Education")
     */
    protected $educations;
    
    /**
     * Normalized employments collection.
     *
     * @var Collection
     * @ODM\EmbedMany(targetDocument="\Cv\Entity\Employment")
     */
    protected $employments;
    
    /**
     * Internal configuration.
     * Available keys are
     * - 'educations'
     *   - 'key': key name in the raw data.
     *   - 'entity': Concrete implementation of an EntityInterface or
     *               class name of the entity to be used as education entity
     *   - 'hydrator': Concrete hydrator instance or class name of a hydrator
     *                 to be used to hydrate entity.
     * - 'employments': view 'educations'
     * - 'properties_map': Array in the format
     *          'property': 'data-key',
     *          used to map simple entity properties to data values.
     *
     * @var array
     */
    protected $config = array(
        'educations' => array(
            'key' => 'education'
        ),
        'employments' => array(
            'key' => 'employment'
        ),
        'properties_map' => array(
            'link' => 'permalink',
        ),
    );
    
    /**
     * {@inheritDoc}
     * @see \Core\Entity\PreUpdateAwareInterface::preUpdate()
     * @ODM\PreUpdate
     * @ODM\PrePersist
     */
    public function preUpdate($isNew = false)
    {
        if (null === $this->educations) {
            $this->getEducations();
        }
        if (null === $this->employments) {
            $this->getEmployments();
        }
        if (null === $this->link) {
            $this->getLink();
        }
    }
    
    /**
     * {@inheritDoc}
     * @see \Auth\Entity\SocialProfiles\ProfileInterface::getName()
     */
    public function getName()
    {
        return $this->name;
    }
    


    /**
     * {@inheritDoc}
     * @see \Auth\Entity\SocialProfiles\ProfileInterface::setName()
     */
    public function setName($name)
    {
        $this->name = (string) $name;
        return $this;
    }
    
    /**
     * {@inheritDoc}
     * @see \Auth\Entity\SocialProfiles\ProfileInterface::getData()
     */
    public function getData($key = null)
    {
        if (null === $key) {
            return $this->data;
        }
        
        $return = $this->data;
        foreach (explode('.', $key) as $subKey) {
            if (isset($return[$subKey])) {
                $return = $return[$subKey];
            } else {
                return null;
            }
        }
        return $return;
    }
    
    /**
     * {@inheritDoc}
     * @see \Auth\Entity\SocialProfiles\ProfileInterface::setData()
     */
    public function setData(array $data)
    {
        $this->data = $data;
        
        // Force recreation of collections and properties.
        $this->educations = null;
        $this->employments = null;
        $this->link = null;
        
        return $this;
    }
    
    /**
     * {@inheritDoc}
     * @see \Auth\Entity\SocialProfiles\ProfileInterface::getLink()
     */
    public function getLink()
    {
        if (!$this->link) {
            $this->link = $this->getData($this->config['properties_map']['link']);
        }
        return $this->link;
    }

    /**
     * {@inheritDoc}
     * @see \Auth\Entity\SocialProfiles\ProfileInterface::getEducations()
     */
    public function getEducations()
    {
        if (!$this->educations) {
            $this->educations = $this->getCollection('educations');
        }
        return $this->educations;
    }

    /**
     * {@inheritDoc}
     * @see \Auth\Entity\SocialProfiles\ProfileInterface::getEmployments()
     */
    public function getEmployments()
    {
        if (!$this->employments) {
            $this->employments = $this->getCollection('employments');
        }
        return $this->employments;
    }

    
    /**
     * Creates a collection of normalized embedded documents.
     *
     * @param string $type
     * @uses getHydrator(), getEntity(), getData()
     * @return \Core\Entity\Collection\ArrayCollection
     */
    protected function getCollection($type)
    {
        $collection = new ArrayCollection();
        $key        = $this->config[$type]['key'];
        $hydrator   = $this->getHydrator($type);
        $filter     = 'filter' . rtrim($type, 's');
        $entity     = $this->getEntity($type);
        $dataArray  = $this->getData($key);
        
        if ($dataArray) {
            foreach ($dataArray as $data) {
                $data    = $this->$filter($data);
                if (!count($data)) {
                    continue;
                }
                $current = $hydrator->hydrate($data, clone $entity);
                $collection->add($current);
            }
        }
        return $collection;
    }
    
    /**
     * Gets a hydrator
     * @param string $type
     * @return \Core\Entity\Hydrator\EntityHydrator
     */
    protected function getHydrator($type)
    {
        $hydrator = isset($this->config[$type]['hydrator'])
                  ? $this->config[$type]['hydrator']
                  : new EntityHydrator();
        
        if (is_string($hydrator)) {
            $this->config[$type]['hydrator'] = $hydrator = new $hydrator();
        }
        return $hydrator;
    }
    
    /**
     * filters one entry of the educations collection for use in the
     * configured entity hydrator.
     * @param array $data
     * @return array
     */
    abstract protected function filterEducation($data);
    
    /**
     * filters one entry of the employments collection for use in the
     * configured entity hydrator.
     *
     * @param array $data
     * @return array
     */
    abstract protected function filterEmployment($data);

    /**
     * Gets an entity for education or employment.
     *
     * @param string $type
     * @return \Core\Entity\EntityInterface
     */
    protected function getEntity($type)
    {
        $entity = isset($this->config[$type]['entity'])
                ? $this->config[$type]['entity']
                : '\Cv\Entity\\'. ucfirst(rtrim($type, 's'));
        
        if (is_string($entity)) {
            $this->config[$type]['entity'] = $entity = new $entity();
        }
        return $entity;
    }
}
