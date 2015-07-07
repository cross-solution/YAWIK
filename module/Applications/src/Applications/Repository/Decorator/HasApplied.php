<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2015 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Applications\Repository\Decorator;

use Applications\Entity\ApplicationInterface;
use Auth\Entity\InfoInterface;
use Auth\Entity\UserInterface;
use Core\Decorator\Decorator;
use Jobs\Entity\JobInterface;

/**
 * Decorates ApplicationsRepository to allow querying the database if a user has already applied to a job.
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0.20
 * @todo write test 
 */
class HasApplied extends Decorator
{
    protected $objectType = '\Applications\Repository\Application';

    /**
     * Finds applications by job, contact information (email) and user
     *
     * @param JobInterface  $job
     * @param InfoInterface $contact
     * @param UserInterface $user
     *
     * @return null|array
     */
    public function findByJobAndContactAndUser(JobInterface $job, InfoInterface $contact, UserInterface $user)
    {
        $or = array(
            array('user' => $user->getId()),
            array('permissions.change' => $user->getId())
        );

        if ($email = $contact->getEmail()) {
            $or[] = array('contact.email' => $email);
        }

        return $this->object->findBy(array(
                                'isDraft' => null,
                                '$and' => array(
                                    array('job' => $job->getId()),
                                    array('$or' => $or),
                                ),
                            ));
    }

    /**
     * Returns true, if the
     *
     * @param ApplicationInterface $application
     *
     * @return bool
     */
    public function hasApplied(ApplicationInterface $application)
    {
        $result = $this->findByJobAndContactAndUser($application->getJob(), $application->getContact(), $application->getUser());
        $count  = count($result);

        if (2 <= $count) { return true; }

        if (1 == $count) {
            return $application->getId() != $result[0]->getId();
        }

        return false;
    }


}