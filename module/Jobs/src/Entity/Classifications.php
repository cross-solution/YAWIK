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

use Core\Entity\ClonePropertiesTrait;
use Core\Entity\EntityInterface;
use Core\Entity\EntityTrait;
use Core\Entity\Tree\EmbeddedLeafs;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Stores the classifications (categories) of a job.
 *
 * @ODM\EmbeddedDocument
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0,29
 */
class Classifications implements EntityInterface
{
    use EntityTrait, ClonePropertiesTrait;

    private $cloneProperties = [
        'professions', 'employmentTypes', 'industries'
    ];

    /**
     * The professions.
     *
     * @ODM\EmbedOne(targetDocument="\Core\Entity\Tree\EmbeddedLeafs")
     * @var EmbeddedLeafs
     */
    private $professions;

    /**
     * The industries.
     *
     * @ODM\EmbedOne(targetDocument="\Core\Entity\Tree\EmbeddedLeafs")
     * @var EmbeddedLeafs
     */
    private $industries;

    /**
     * The employment types.
     *
     * @ODM\EmbedOne(targetDocument="\Core\Entity\Tree\EmbeddedLeafs")
     * @var EmbeddedLeafs
     */
    private $employmentTypes;

    /**
     * Set the employment types.
     *
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
     * Get the employment types.
     *
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
     * Set the professions.
     *
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
     * Get the professions.
     *
     * @return \Core\Entity\Tree\EmbeddedLeafs
     */
    public function getProfessions()
    {
        if (!$this->professions) {
            $this->setProfessions(new EmbeddedLeafs());
        }

        return $this->professions;
    }

    /**
     * Set the industries.
     *
     * @param \Core\Entity\Tree\EmbeddedLeafs $industries
     *
     * @return self
     */
    public function setIndustries($industries)
    {
        $this->industries = $industries;

        return $this;
    }

    /**
     * Get the professions.
     *
     * @return \Core\Entity\Tree\EmbeddedLeafs
     */
    public function getIndustries()
    {
        if (!$this->industries) {
            $this->setIndustries(new EmbeddedLeafs());
        }

        return $this->industries;
    }
}
