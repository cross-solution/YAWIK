<?php
/**
 * Cross Applicant Management
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   AGPLv3
 */

/** AbstractProfile.php */ 
namespace Auth\Entity\SocialProfiles;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Core\Entity\AbstractIdentifiableEntity;
use Core\Entity\Collection\ArrayCollection;
use Cv\Entity\Education;
use Core\Entity\Hydrator\EntityHydrator;
use Core\Entity\PreUpdateAwareInterface;

/**
 * 
 * @ODM\MappedSuperclass
 */
abstract class AbstractProfile extends AbstractIdentifiableEntity 
                      implements ProfileInterface,
                                 PreUpdateAwareInterface
{
    
    /**
     * 
     * @var String
     * @ODM\String
     */
    protected $name;
    
    
    /**
     * 
     * @var unknown
     * @ODM\String
     */
    protected $link;
    /**
     * 
     * 
     * @ODM\Hash
     */
    protected $data = array();
    
    /**
     * 
     * @var unknown
     * @ODM\EmbedMany(targetDocument="\Cv\Entity\Education")
     */
    protected $educations;
    
    /**
     * 
     * @var unknown
     * @ODM\EmbedMany(targetDocument="\Cv\Entity\Employment")
     */
    protected $employments;
    
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
    
    /* (non-PHPdoc)
     * @see \Auth\Entity\SocialProfiles\ProfileInterface::getName()
    */
    public function getName ()
    {
        return $this->name;
    }
    


    /* (non-PHPdoc)
     * @see \Auth\Entity\SocialProfiles\ProfileInterface::setName()
     */
    public function setName ($name)
    {
       $this->name = (string) $name;
       return $this;
    
    }
    
    /* (non-PHPdoc)
     * @see \Auth\Entity\SocialProfiles\ProfileInterface::getData()
     */
    public function getData ($key = null)
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
    
    /* (non-PHPdoc)
     * @see \Auth\Entity\SocialProfiles\ProfileInterface::setData()
     */
    public function setData (array $data)
    {
        $this->data = $data;
        
        // Force recreation of collections and properties.
        $this->educations = null;
        $this->employments = null;
        $this->link = null;
        
        return $this;
    }
    
    public function getLink()
    {
        if (!$this->link) {
            $this->link = $this->getData($this->config['properties_map']['link']);
        }
        return $this->link;
    }

    public function getEducations()
    {
        if (!$this->educations) {
            $this->educations = $this->getCollection('educations');
        }
        return $this->educations;
    }

	/* (non-PHPdoc)
     * @see \Auth\Entity\SocialProfiles\ProfileInterface::getEmployments()
     */
    public function getEmployments ()
    {
        if (!$this->employments) {
            $this->employments = $this->getCollection('employments');
        }
        return $this->employments;
        
    }

    
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
                if (!count($data)) { continue; }
                $current = $hydrator->hydrate($data, clone $entity);
                $collection->add($current);
            }
        }
        return $collection;
    }
    
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
    
    abstract protected function filterEducation($data);
    abstract protected function filterEmployment($data);

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

