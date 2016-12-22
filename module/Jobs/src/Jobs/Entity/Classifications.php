<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Jobs\Entity;

use Core\Entity\EntityInterface;
use Core\Entity\EntityTrait;
use Core\Entity\Tree\EmbeddedLeafs;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * ${CARET}
 *
 * @ODM\EmbeddedDocument
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test 
 */
class Classifications implements EntityInterface
{

    use EntityTrait;
    /**
     *
     * @ODM\EmbedOne(targetDocument="\Core\Entity\Tree\EmbeddedLeafs")
     * @var EmbeddedLeafs
     */
    private $professions;

    /**
     *
     * @ODM\EmbedOne(targetDocument="\Core\Entity\Tree\EmbeddedLeafs")
     * @var EmbeddedLeafs
     */
    private $employmentTypes;

    /**
     * @param \Core\Entity\Tree\EmbeddedLeafs $employmentTypes
     *
     * @return self
     */
    public function setEmploymentTypes($employmentTypes)
    {
        $this->employmentTypes = $employmentTypes;

        return $this;
    }

    /**
     * @return \Core\Entity\Tree\EmbeddedLeafs
     */
    public function getEmploymentTypes()
    {
        if (!$this->employmentTypes) {
            $this->setEmploymentTypes(new EmbeddedLeafs());
        }

        return $this->employmentTypes;
    }

    /**
     * @param \Core\Entity\Tree\EmbeddedLeafs $professions
     *
     * @return self
     */
    public function setProfessions($professions)
    {
        $this->professions = $professions;

        return $this;
    }

    /**
     * @return \Core\Entity\Tree\EmbeddedLeafs
     */
    public function getProfessions()
    {
        if (!$this->professions) {
            $this->setProfessions(new EmbeddedLeafs());
        }

        return $this->professions;
    }


}