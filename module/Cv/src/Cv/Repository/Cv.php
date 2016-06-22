<?php

namespace Cv\Repository;

use Zend\ServiceManager\ServiceLocatorInterface;
use Core\Repository\AbstractRepository;
use Auth\Entity\UserInterface;

/**
 * class for accessing CVs
 */
class Cv extends AbstractRepository
{
    /**
     * Look for an drafted Document of a given user
     *
     * @param $user
     * @return \Cv\Entity\Cv|null
     */
    public function findDraft($user)
    {
        if ($user instanceof UserInterface) {
            $user = $user->getId();
        }

        $document = $this->findOneBy(
            array(
                'isDraft' => true,
                'user' => $user
            )
        );

        if (!empty($document)) {
            return $document;
        }

        return null;
    }
}
