<?php

namespace Cv\Repository;

use Applications\Entity\Attachment;
use Core\Entity\PermissionsInterface;
use Core\Repository\DraftableEntityAwareInterface;
use Core\Repository\DraftableEntityAwareTrait;
use Core\Repository\AbstractRepository;
use Auth\Entity\UserInterface;
use Applications\Entity\Application;
use Cv\Entity\Cv as CvEntity;

/**
 * class for accessing CVs
 *
 * @method CvEntity create(array $data = null, $persist = false)
 */
class Cv extends AbstractRepository implements DraftableEntityAwareInterface
{
    use DraftableEntityAwareTrait;

    /**
     * Look for an drafted Document of a given user
     *
     * @param object|UserInterface $user
     * @return CvEntity|null
     */
    public function findDraft($user)
    {
        if ($user instanceof UserInterface) {
            $user = $user->getId();
        }

        return $this->findOneDraftBy(['user' => $user]);
    }
}
