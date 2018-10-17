<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    weitz@cross-solution.de
 */

namespace Jobs\Entity;

/**
 * Holds various fields a o job opening template
 *
 */
interface TemplateValuesInterface
{

    /**
     * Sets the Qualification field of the job template
     *
     * @param $qualifications
     * @return $this
     */
    public function setQualifications($qualifications);

    /**
     * Gets the qualification of a job template
     *
     * @return String
     */
    public function getQualifications();

    /**
     * Sets the requirements of a job template
     *
     * @param String $requirements
     * @return $this
     */
    public function setRequirements($requirements);

    /**
     * Gets the requirements of a job template
     *
     * @return String
     */
    public function getRequirements();

    /**
     * Sets the benefits of a job template
     *
     * @param String $benefits
     * @return $this
     */
    public function setBenefits($benefits);

    /**
     * Gets the Benefits of a job template
     *
     * @return String
     */
    public function getBenefits();

    /**
     * Sets the job title of the job template
     *
     * @param $title
     * @return $this
     */
    public function setTitle($title);

    /**
     * Gets the title of the job template
     *
     * @return String
     */
    public function getTitle();

    /**
     * Gets the company description of the job template
     *
     * @return String
     */
    public function getDescription();

    /**
     * Sets the company description of the job template
     *
     * @param $description
     * @return $this
     */
    public function setDescription($description);

    /**
     * Sets the language of the job template values
     *
     * @param $language
     * @return $this
     */
    public function setLanguage($language);

    /**
     * Gets the language of the job template values
     *
     * @return String
     */
    public function getLanguage();


    /**
     * Gets the job title of the job template
     *
     * @return String
     */


    /**
     * @param null $key
     * @param null $default
     * @param bool $set
     * @return null
     */
    public function get($key = null, $default = null, $set = false);

    /**
     * @param $key
     * @param $value
     * @return $this
     */
    public function set($key, $value);

    public function __get($property);
    public function __set($property, $value);
    public function __isset($property);
}
