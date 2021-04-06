<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright https://yawik.org/COPYRIGHT.php
 * @license   MIT
 */

/** InternalReferences.php */
namespace Applications\Entity;

use Auth\Entity\UserInterface;
use Jobs\Entity\JobInterface;
use Core\Entity\AbstractEntity;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * @ODM\EmbeddedDocument
 */
class InternalReferences extends AbstractEntity
{

    /** @ODM\Field(type="hash") */
    protected $jobs = array();

    /**
     * @ODM\Field(type="collection")
     * @var array
     */
    protected $jobManagers = [];

    public function setJob(JobInterface $job)
    {
        if (isset($this->jobs['__id__']) && $this->jobs['__id__'] == $job->getId()) {
            return $this;
        }

        $this->jobs = array(
            '__id__' => $job->getId(),
            'userId' => ($user = $job->getUser()) ? $user->getId() : null,
        );
        $this->setJobsManagers($job->getMetaData('organizations:managers', []));
        return $this;
    }

    public function setJobsId($id)
    {
        $this->jobs['__id__'] = $id;
        return $this;
    }

    public function setJobsUserId($userOrId)
    {
        if ($userOrId instanceof UserInterface) {
            $userOrId = $userOrId->getId();
        }
        $this->jobs['userId'] = $userOrId;
        return $this;
    }

    public function setJobsManagers($managers)
    {
        $this->jobManagers = $managers;
        return $this;
    }
}
