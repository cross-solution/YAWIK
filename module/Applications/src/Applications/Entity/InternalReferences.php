<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
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
    
    public function setJob(JobInterface $job)
    {
        if (isset($this->jobs['__id__']) && $this->jobs['__id__'] == $job->getId()) {
            return $this;
        }
        
        $this->jobs = array(
            '__id__' => $job->getId(),
            'userId' => ($user = $job->getUser()) ? $user->getId() : null,
        );
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
}
