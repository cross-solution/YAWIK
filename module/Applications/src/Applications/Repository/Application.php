<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2014 Cross Solution (http://cross-solution.de)
 * @license   AGPLv3
 */
namespace Applications\Repository;

use Core\Repository\AbstractRepository;
use Core\Entity\EntityInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Stdlib\Parameters;
use Core\Paginator\Adapter\EntityList;
use Applications\Entity\ApplicationInterface;
use Doctrine\ODM\MongoDB\Events;
use Applications\Entity\Application as ApplicationEntity;
use Applications\Entity\CommentInterface;
use Zend\Stdlib\ArrayUtils;
use Auth\Entity\UserInterface;

/**
 * class for accessing applications
 * 
 * @todo find better mechanism for loading drafts or applications or both states.
 * 
 * @package Applications
 */
class Application extends AbstractRepository
{   
    /**
     * {@inheritDoc}
     */
    public function findBy(array $criteria, array $sort = null, $limit = null, $skip = null)
    {
        if (!array_key_exists('isDraft', $criteria)) {
            $criteria['isDraft'] = false;
        } else if (null === $criteria['isDraft']) {
            unset($criteria['isDraft']);
        }
        return parent::findBy($criteria, $sort, $limit, $skip);
    }
    
    /**
     * {@inheritDoc}
     */
    public function findOneBy(array $criteria)
    {
        if (!array_key_exists('isDraft', $criteria)) {
            $criteria['isDraft'] = false;
        } else if (null === $criteria['isDraft']) {
            unset($criteria['isDraft']);
        }
        return parent::findOneBy($criteria);
    }
    
    /**
     * {@inheritDoc}
     */
    public function createQueryBuilder($findDrafts = false)
    {
        $qb = parent::createQueryBuilder();
        if (null !== $findDrafts) {
            $qb->field('isDraft')->equals($findDrafts);
        }
        return $qb;
    }
    
    /**
     * Gets a pointer to an application
     * 
     * @param array $params
     */
    public function getPaginatorCursor($params)
    {
        return $this->getPaginationQueryBuilder($params)
                    ->getQuery()
                    ->execute();
    }
    
    /**
     * Gets a query builder to search for applications
     * 
     * @param array $params
     * @return unknown
     */
    protected function getPaginationQueryBuilder($params)
    {
        $filter = $this->getService('filterManager')->get('Applications/PaginationQuery');
        $qb = $filter->filter($params, $this->createQueryBuilder());
        
        return $qb;
    }
    
    /**
     * Gets a result list of applications
     * 
     * @param array $params
     * @return \Applications\Repository\PaginationList
     */
    public function getPaginationList($params)
    {
        $qb = $this->getPaginationQueryBuilder($params);
        $cursor = $qb->hydrate(false)
                     ->select('_id')
                     ->getQuery()
                     ->execute();
        
        $list = new PaginationList(array_keys(ArrayUtils::iteratorToArray($cursor)));
        return $list;
    }

    /**
     * Get unread applications
     *
     * @param \Jobs\Entity\JobInterface $job
     * @return array|bool|\Doctrine\MongoDB\ArrayIterator|\Doctrine\MongoDB\Cursor|\Doctrine\MongoDB\EagerCursor|mixed|null
     */
    public function getUnreadApplications($job) 
    {
        $auth=$this->getService('AuthenticationService');
        $qb=$this->createQueryBuilder()
                  ->field("readBy")->notIn(array($auth->getUser()->id))
                  ->field("job")->equals( new \MongoId($job->id));
        return $qb->getQuery()->execute();          
    }

    /**
     * Get comments of an applications
     *
     * @param $commentOrId
     * @internal param \Application\Entity\Comment $comment | Id
     * @return null $comment|NULL
     */
    public function findComment($commentOrId)
    {
        if ($commentOrId instanceOf CommentInterface) {
            $commentOrId = $commentOrId->getId();
        }
        
        $application = $this->findOneBy(array('comments.id' => $commentOrId));
        foreach ($application->getComments() as $comment) {
            if ($comment->getId() == $commentOrId) {
                return $comment;
            }
        }
        return null;
            
    }
    
    /**
     * Gets social profiles of an application
     * 
     * @param String $profileId
     * @return $profile|NULL
     */
    public function findProfile($profileId)
    {
        $application = $this->findOneBy(array('isDraft' => null, 'profiles._id' => new \MongoId($profileId)));
        foreach ($application->getProfiles() as $profile) {
            if ($profile->getId() == $profileId) {
                return $profile;
            }
        }
        return null;
    }
    
    public function getStates()
    {
        $qb = $this->createQueryBuilder();
        $qb->hydrate(false)->distinct('status.name');
        $result = $qb->getQuery()->execute();
        return $result;
    }
    
    public function findDraft($user, $applyId)
    {
        if ($user instanceOf UserInterface) {
            $user = $user->getId();
        }
        
//         $qb = $this->createQueryBuilder();
//         $qb->addOr(
//                 $qb->expr()
//                    ->field('user')->equals($user)
//             )
//            ->addOr(
//                 $qb->expr()
//                    ->field('permissions.change')
//                    ->equals($user)
//             );
        
        $documents = $this->findBy(array(
            'isDraft' => true,
            '$or' => array(
                array('user' => $user),
                array('permissions.change' => $user)
            )
        ));
        foreach ($documents as $document) {
            if ($applyId == $document->getJob()->getApplyId()) {
                return $document;
            }
        }
        
        return null;
    }
}