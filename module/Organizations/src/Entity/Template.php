<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Organizations\Entity;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Core\Entity\AbstractEntity;

/**
 * Organization Template
 *
 * defines default values of the job template of an organization
 *
 * @ODM\EmbeddedDocument
 * @author Carsten Bleek <bleek@cross-solution.de>
 * @since 0.23
 */
class Template extends AbstractEntity implements TemplateInterface
{

    /**
     * Label of the requirements field in the job template
     *
     * @var $string;
     * @ODM\Field(type="string")
     */
    protected $labelRequirements = 'Requirements';

    /**
     * Label of the qualifications field in the job template
     *
     * @ODM\Field(type="string")
     * @var $string;
     */
    protected $labelQualifications = 'Qualifications';

    /**
     * Label of the benefits field in the job template
     *
     * @ODM\Field(type="string")
     * @var $string;
     */
    protected $labelBenefits = 'Benefits';

    /**
     * Sets the label of the requirements form field
     *
     * @param string $labelRequirements
     *
     * @return self
     */
    public function setLabelRequirements($labelRequirements)
    {
        $this->labelRequirements = $labelRequirements;
        return $this;
    }

    /**
     * Gets the label of the requirements form field
     *
     * @return string
     */
    public function getLabelRequirements()
    {
        return $this->labelRequirements;
    }

    /**
     * Sets the label of the qualifications form field
     *
     * @param string $labelQualifications
     *
     * @return self
     */
    public function setLabelQualifications($labelQualifications)
    {
        $this->labelQualifications=$labelQualifications;
        return $this;
    }

    /**
     * Gets the label of the qualifications form field
     *
     * @return string
     */
    public function getLabelQualifications()
    {
        return $this->labelQualifications;
    }

    /**
     * Sets the label of the benefits form field
     *
     * @param string $labelBenefits
     *
     * @return self
     */
    public function setLabelBenefits($labelBenefits)
    {
        $this->labelBenefits=$labelBenefits;
        return $this;
    }

    /**
     * Gets the label of the benefits form field
     *
     * @return string
     */
    public function getLabelBenefits()
    {
        return $this->labelBenefits;
    }
}
