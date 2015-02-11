<?php

namespace Auth\Form\Hydrator;

use Core\Entity\Hydrator\EntityHydrator;

class UserPasswordHydrator extends EntityHydrator
{
    public function extract ($object)
    {
        $data = parent::extract($object);
        // provide the fieldset access to the entity
        // (since it is changing properties of the user)
        $data['passwordFieldset'] = $object;
        return $data;
    }
}


