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

use Core\Entity\AbstractEntity;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Holds various fields a o job opening template
 *
 * @ODM\EmbeddedDocument
 * @ODM\Indexes({
 *   @ODM\Index(keys={"requirements"="text",
 *                    "description"="text",
 *                    "qualifications"="text",
 *                    "benefits"="text",
 *                    "title"="text"},
 *              name="fulltext",
 *              options={"language_override":"lang_index"})
 * })
 * @since 0.29 Adds html field.
 * @since 0.33 Add option 'language_override' to index definition.
 */
class TemplateValues extends AbstractEntity implements TemplateValuesInterface
{

    /**
     * Qualification field of the job template
     *
     * @var String
     * @ODM\Field(type="string")
     */
    protected $qualifications='';

    /**
     * Requirements field of the job template
     *
     * @var String
     * @ODM\Field(type="string")
     */
    protected $requirements='';

    /**
     * Benefits field of the job template
     *
     * @var String
     * @ODM\Field(type="string")
     */
    protected $benefits='';

    /**
     * Job title field of the job template
     *
     * @var String
     * @ODM\Field(type="string")
     */
    protected $title='';

    /**
     * Company description field of the job template
     *
     * @var String
     * @ODM\Field(type="string")
     */
    protected $description='';

    /**
     * language of the job template values. Must be a valid ISO 639-1 code
     *
     * @var String
     * @ODM\Field(type="string")
     */
    protected $language='en';

    /**
     * Pure HTML
     *
     * @ODM\Field(type="string")
     * @var string
     * @since 0.29
     */
    protected $html='';

    /**
     * Introduction text for the job template
     *
     * @ODM\Field(type="string")
     * @var string
     */
    protected $introduction = '';

    /**
     * Boilerplate (outro) text for the job template
     *
     * @ODM\Field(type="string")
     * @var string
     */
    protected $boilerplate = '';

    /**
     * free values (currently not in use)
     *
     * @ODM\Field(type="hash")
     */
    protected $_freeValues;

    /**
     * Sets the Qualification field of the job template
     *
     * @param $qualifications
     * @return $this
     */
    public function setQualifications($qualifications)
    {
        $this->qualifications= (string) $qualifications;
        return $this;
    }

    /**
     * Gets the qualification of a job template
     *
     * @return String
     */
    public function getQualifications()
    {
        return $this->qualifications;
    }

    /**
     * Sets the requirements of a job template
     *
     * @param String $requirements
     * @return $this
     */
    public function setRequirements($requirements)
    {
        $this->requirements=(string) $requirements;
        return $this;
    }

    /**
     * Gets the requirements of a job template
     *
     * @return String
     */
    public function getRequirements()
    {
        return $this->requirements;
    }

    /**
     * Sets the benefits of a job template
     *
     * @param String $benefits
     * @return $this
     */
    public function setBenefits($benefits)
    {
        $this->benefits=(string) $benefits;
        return $this;
    }

    /**
     * Gets the Benefits of a job template
     *
     * @return String
     */
    public function getBenefits()
    {
        return $this->benefits;
    }

    /**
     * Sets the job title of the job template
     *
     * @param $title
     * @return $this
     */
    public function setTitle($title)
    {
        $this->title=(string) $title;
        return $this;
    }

    /**
     * Gets the job title of the job template
     *
     * @return String
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Sets the company description of the job template
     *
     * @param $description
     * @return $this
     */
    public function setDescription($description)
    {
        $this->description=(string) $description;
        return $this;
    }

    /**
     * Gets the company description of the job template
     *
     * @return String
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Sets the language of the job template values
     *
     * @param $language
     * @return $this
     */
    public function setLanguage($language)
    {
        $this->language=(string) $language;
        return $this;
    }

    /**
     * Gets the language of the job template values
     *
     * @return String
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @param string $html
     *
     * @return self
     * @since 0.29
     */
    public function setHtml($html)
    {
        $this->html = $html;

        return $this;
    }

    /**
     * @return string
     * @since 0.29
     */
    public function getHtml()
    {
        return $this->html;
    }

    /**
     * @return string
     */
    public function getIntroduction(): string
    {
        return $this->introduction;
    }

    /**
     * @param string $introduction
     *
     * @return self
     */
    public function setIntroduction($introduction)
    {
        $this->introduction = $introduction;

        return $this;
    }

    /**
     * @return string
     */
    public function getBoilerplate(): string
    {
        return $this->boilerplate;
    }

    /**
     * @param string $boilerplate
     *
     * @return self
     */
    public function setBoilerplate($boilerplate)
    {
        $this->boilerplate = $boilerplate;

        return $this;
    }



    /**
     * @param null $key
     * @param null $default
     * @param bool $set
     * @return null
     */
    public function get($key = null, $default = null, $set = false)
    {
        if (isset($this->_freeValues[$key])) {
            return $this->_freeValues[$key];
        }
        if ($set) {
            $this->set($key, $default);
        }
        return $default;
    }

    /**
     * @param $key
     * @param $value
     * @return $this
     */
    public function set($key, $value)
    {
        //$this->checkWriteAccess();
        $this->_freeValues[$key] = $value;
        return $this;
    }

    public function __get($property)
    {
        $getter = "get" . ucfirst($property);
        if (method_exists($this, $getter)) {
            return $this->$getter();
        }

        if (property_exists($this, $property)) {
            return $this->$property;
        }

        return $this->get($property);
    }

    public function __set($property, $value)
    {
        //$this->checkWriteAccess();
        $setter = 'set' . ucfirst($property);
        if (method_exists($this, $setter)) {
            $this->$setter($value);
            return;
        }

        if (property_exists($this, $property)) {
            $this->$property = $value;
            return;
        }

        $this->set($property, $value);
    }

    public function __isset($property)
    {
        $value = $this->__get($property);

        if (is_array($value) && !count($value)) {
            return false;
        }
        if (is_bool($value) || is_object($value)) {
            return true;
        }
        return (bool) $value;
    }
}
