<?php
/**
 * Cross Applicant Management
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

/** Auth mapper mongodb */
namespace Applications\Repository\Mapper;

use Core\Repository\Mapper\AbstractBuilderAwareMapper as CoreMapper;
use Core\Entity\EntityInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;


/**
 *
 */
class ApplicationMapper extends CoreMapper implements ServiceLocatorAwareInterface
{
    
    protected $mappers;
    
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->mappers = $serviceLocator;
        return $this;
    }
    
    public function getServiceLocator()
    {
        return $this->mappers;
    }

    /**
     * {@inheritdoc}
     *
     * @param string|\MongoId $id Mongodb id
     */
    public function find($id, array $fields = array(), $exclude = false)
    {
        $data = $this->getData($id, $fields, $exclude);
        $builder = $this->builders->get('application');
        $entity = $builder->build($data);
        return $entity;
    }
    
    
    public function findContact($id)
    {
        $query = array('_id' => $this->getMongoId($id));
        $fields = array(
            'jobId', 'status', 'dateCreated', 'dateModified',
            'cv'
        );
        $data = $this->getData($query, $fields, true);
        if (!isset($data['contact'])) {
            $data['contact'] = array();
        }
        $entity = $this->buildEntity($data, 'application-contact');
        return $entity;
    }
    /**
     * {@inheritdoc}
     *
     * @param CriteriaInterface|null $criteria
     * @return Collection
     */
    public function fetch(array $query = array(), array $fields = array(), $exclude = false)
    {
        $cursor     = $this->getCursor($query, $fields, $exclude);
        $builder    = $this->builders->get('application');
        $collection = $builder->buildCollection($cursor);
        return $collection;
    }
    
    public function fetchByJobid($jobId)
    {
        $query = array('jobId' => (string) $jobId);
        $cursor = $this->getCollection()->find($query);
        return $cursor;    
    }
    
    public function fetchCv($applicationId)
    {
        $query  = array('_id' => $this->getMongoId($applicationId));
        $fields = array(
            'jobId', 'status', 'dateCreated', 'dateModified', 
            'cv.educations', 'cv.employments'
        );
        $data   = $this->getData($query, $fields, /*exclude*/ true);
        if (!isset($data['cv'])) { $data['cv'] = array(); }
        $entity = $this->buildEntity($data['cv'], 'application-cv');
        return $entity; 
    }

    public function save(EntityInterface $entity)
    {
        $builder = $this->builders->get('application');
        $data    = $builder->unbuild($entity);
        $id      = $this->saveData($data);
        if ($id) {
            $entity->setId($id);
        }
    }
    
    

      
}