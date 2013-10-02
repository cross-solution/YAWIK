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
use Core\Paginator\Adapter\MongoCursor as MongoCursorAdapter;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Core\Entity\RelationEntity;


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

    public function getPaginatorAdapter(array $params)
    {
    
        $query = $this->mappers->getServiceLocator()->get('FilterManager')
                      ->get('applications-params-to-properties')
                      ->filter($params);
         
        if (isset($query['sort'])) {
            $sort = $query['sort'];
            unset($query['sort']);
        } else {
            $sort = array();
        }
        $cursor = $this->getCursor($query, array('cv'), true); //, array('cv'), true);
        $cursor->sort($sort);
        return new MongoCursorAdapter($cursor, $this->builders->get('application'));
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
        if ($job = $entity->getJob()) {
            $data['refs']['jobs']['userId'] = $job->getUserId();
        }
        $auth = $this->mappers->getServiceLocator()->get('AuthenticationService');
        if ($auth->hasIdentity()) {
            $data['refs']['users']['id'] = $auth->getIdentity();
        }
        if (isset($data['cv']) && empty($data['cv']['_id'])) {
            $data['cv']['_id'] = new \MongoId();
        }
        $id      = $this->saveData($data);
        if ($id) {
            $entity->setId($id);
        }
    }
    
    

      
}