<?php

namespace Cv\Entity;

use Core\Entity\EntityInterface;

interface ComputerSkillInterface extends EntityInterface
{
    /**
     * sets the name of the computer skill
     *
     * @param string $name
     */
    public function setName($name);

    /**
     * returns the name of the computer skill
     *
     * @return mixed
     */
    public function getName();

    /**
     * sets the level of the computer skill
     *
     * @param string $level
     */
    public function setLevel($level);

    /**
     * returns the level of the computer skill
     *
     * @return mixed
     */
    public function getLevel();
}
