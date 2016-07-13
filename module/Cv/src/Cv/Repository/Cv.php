<?php

namespace Cv\Repository;

use Core\Repository\DraftableEntityAwareInterface;
use Core\Repository\DraftableEntityAwareTrait;
use Core\Repository\AbstractRepository;
use Auth\Entity\UserInterface;

/**
 * class for accessing CVs
 */
class Cv extends AbstractRepository implements DraftableEntityAwareInterface
{
    use DraftableEntityAwareTrait;

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

        return $this->findOneDraftBy(['user' => $user]);
    }
}
