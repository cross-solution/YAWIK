<?php

namespace Cv\Entity;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Core\Entity\AbstractEntity;

/**
 * initial computer skill class.
 *
 * Class ComputerSkill
 * @package Cv\Entity
 * @ODM\EmbeddedDocument
 */
class ComputerSkill extends AbstractEntity implements ComputerSkillInterface
{

    /**
     * @var string
     * @ODM\Field(type="string")
     */
    protected $name;

    /**
     * @var string
     * @ODM\Field(type="string")
     */
    protected $level;

    /**
     * {@inheritDoc}
     * @param string $name
     */
    public function setName($name)
    {
        $this->name=$name;
    }

    /**
     * {@inheritDoc}
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritDoc}
     * @param string $level
     */
    public function setLevel($level)
    {
        $this->level=$level;
    }

    /**
     * {@inheritDoc}
     * @return mixed
     */
    public function getLevel()
    {
        return $this->level;
    }
}
