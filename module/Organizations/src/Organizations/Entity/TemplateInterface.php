<?php
/**
 * YAWIK
 *
 * @filesource
 * @license    MIT
 * @copyright  2013 - 2015 Cross Solution <http://cross-solution.de>
 */

/** */
namespace Organizations\Entity;

/**
 * Interface for an organizations job template.
 *
 * @author Carsten Bleek <bleek@cross-solution.de>
 * @since  0.23
 */
interface TemplateInterface
{
    /**
     * Sets the label of the requirements form field
     *
     * @param string $labelRequirement
     *
     * @return self
     */
    public function setLabelRequirements($labelRequirement);

    /**
     * Gets the label of the requirements form field
     *
     * @return string
     */
    public function getLabelRequirements();

    /**
     * Sets the label of the qualifications form field
     *
     * @param string $labelQualification
     *
     * @return self
     */
    public function setLabelQualification($labelQualification);

    /**
     * Gets the label of the qualificationss form field
     *
     * @return string
     */
    public function getLabelQualification();

    /**
     * Sets the label of the benefits form field
     *
     * @param string $labelBenefits
     *
     * @return self
     */
    public function setLabelBenefits($labelBenefits);

    /**
     * Gets the label of the benefits form field
     *
     * @return string
     */
    public function getLabelBenefits();



}
