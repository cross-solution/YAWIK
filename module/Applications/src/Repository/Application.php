<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright https://yawik.org/COPYRIGHT.php
 * @license   MIT
 * @author Carsten Bleek <bleek@cross-solution.de>
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @author Mathias Weitz <weitz@xenon>
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 */
namespace Applications\Repository;

use Auth\AuthenticationService;
use Auth\Entity\SocialProfiles\ProfileInterface;
use Core\Repository\AbstractRepository;
use Applications\Entity\Application as ApplicationEntity;
use Applications\Entity\CommentInterface;
use Doctrine\ODM\MongoDB as ODM;
use Interop\Container\ContainerInterface;
use Laminas\Stdlib\ArrayUtils;
use Auth\Entity\UserInterface;
use MongoDB\BSON\ObjectId;

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
    public function findBy(array $criteria, array $sort = null, $limit = null, $skip = null): array
    {
        if (!array_key_exists('isDraft', $criteria)) {
            $criteria['isDraft'] = false;
        } elseif (null === $criteria['isDraft']) {
            unset($criteria['isDraft']);
        }
        return parent::findBy($criteria, $sort, $limit, $skip);
    }

    /**
     * {@inheritDoc}
     */
    public function findOneBy(array $criteria): ?object
    {
        if (!array_key_exists('isDraft', $criteria)) {
            $criteria['isDraft'] = false;
        } elseif (null === $criteria['isDraft']) {
            unset($criteria['isDraft']);
        }
        return parent::findOneBy($criteria);
    }

    /**
     * {@inheritDoc}
     */
    public function createQueryBuilder($findDrafts = false): ODM\Query\Builder
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
     * @return ODM\Query\Builder
     */
    protected function getPaginationQueryBuilder($params)
    {
        $filter = $this->getService('FilterManager')->get('PaginationQuery/Applications');
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
     * @param \Jobs\Entity\Job $job
     *
     * @return mixed
     */
    public function loadApplicationsForJob($job)
    {
        return $this->createQueryBuilder()
                    ->field("job")->equals(new ObjectId($job->getId()))
                    ->getQuery()
                    ->execute();
    }

    /**
     * Get unread applications
     *
     * @param \Jobs\Entity\JobInterface $job
     * @throws ODM\MongoDBException
     */
    public function loadUnreadApplicationsForJob($job)
    {
        $auth=$this->getService('AuthenticationService');
        $qb=$this->createQueryBuilder()
                  ->field("readBy")->notIn(array($auth->getUser()->getId()))
                  ->field("job")->equals(new ObjectId($job->getId()));
        return $qb->getQuery()->execute();
    }

    /**
     * Get comments of an applications
     *
     * @param string|CommentInterface $commentOrId
     * @internal param \Application\Entity\Comment $comment | Id
     * @return \Applications\Entity\Comment|null
     */
    public function findComment($commentOrId)
    {
        if ($commentOrId instanceof CommentInterface) {
            $commentOrId = $commentOrId->getId();
        }

        $application = $this->findOneBy(array('comments.id' => $commentOrId));
        /* @var \Applications\Entity\Comment $comment */
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
     * @return ProfileInterface|null
     */
    public function findProfile($profileId)
    {
        $application = $this->findOneBy(array('isDraft' => null, 'profiles._id' => new ObjectId($profileId)));
        foreach ($application->getProfiles() as $profile) {
            if ($profile->getId() == $profileId) {
                return $profile;
            }
        }
        return null;
    }

    /**
     * @return mixed
     * @throws \Doctrine\ODM\MongoDB\MongoDBException
     */
    public function getStates()
    {
        $qb = $this->createQueryBuilder();
        $qb->hydrate(false)->distinct('status.name');
        return $qb->getQuery()->execute();
    }

    /**
     * @param UserInterface $user
     * @param string $applyId
     * @return ApplicationEntity|null
     */
    public function findDraft($user, $applyId)
    {
        if ($user instanceof UserInterface) {
            $user = $user->getId();
        }

        $documents = $this->findBy(
            [
                'isDraft' => true,
                '$or'     => [
                    ['user' => $user],
                    ['permissions.change' => $user]
                ]
            ]
        );

        /* @var $document ApplicationEntity */
        foreach ($documents as $document) {
            if ($applyId == $document->getJob()->getApplyId()) {
                return $document;
            }
        }

        return null;
    }

    /**
     * Get applications for given user ID
     *
     * @param string $userId
     * @param int $limit
     * @return mixed
     * @since 0.27
     */
    public function getUserApplications($userId, $limit = null): ODM\Query\Builder
    {
        $qb = $this->createQueryBuilder(null)
            ->field('user')->equals($userId)
            ->sort(['date' => -1]);

        if (isset($limit)) {
            $qb->limit($limit);
        }

        return $qb;
    }
}
