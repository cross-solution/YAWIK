<?php
/**
 * YAWIK
 * 
 * @filesource
 * @copyright (c) 2013-2014 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    weitz@cross-solution.de
 */

namespace Jobs\Entity;

use Core\Entity\AbstractIdentifiableHydratorAwareEntity;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Defines the contact address of an Organization
 *
 * @ODM\EmbeddedDocument
 */
class TemplateValues extends AbstractIdentifiableHydratorAwareEntity
{

    /**
     * @var String
     * @ODM\String
     */
    protected $qualifications;

    /**
     * @var String
     * @ODM\String
     */
    protected $requirements;

    /**
     * @var String
     * @ODM\String
     */
    protected $benefits;

    /**
     * @var String
     * @ODM\String
     */
    protected $title;

    /**
     * @ODM\Hash
     */
    protected $_freeValues;

    public function setQualifications(\string $qualifications)
    {
        $this->qualifications=$qualifications;
        return $this;
    }

    public function getQualification()
    {
        return $this->qualifications;
    }


    public function setRequirements(\string $requirements)
    {
        $this->requirements=$requirements;
        return $this;
    }

    public function getRequirements()
    {
        return $this->requirements;
    }

    public function setBenefits(\string $benefits)
    {
        $this->benefits=$benefits;
        return $this;
    }

    public function getBenefits()
    {
        return $this->benefits;
    }

    public function setTitle(\string $title)
    {
        $this->title=$title;
        return $this;
    }

    public function getTitle()
    {
        return $this->title;
    }

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

    public function set($key, $value)
    {
        //$this->checkWriteAccess();
        $this->_freeValues[$key] = $value;
        return $this;
    }

    /*
    public function __call($method, $params)
    {
        if (preg_match('~^((?:g|s)et)(.*)$~', $method, $match)) {
            $property = lcfirst($match[2]);
            if (property_exists($this, $property)) {
                if ('set' == $match[1]) {
                    $this->$property = $params[0];
                    return $this;
                } else {
                    return $this->$property;
                }
            }
            $value = isset($params[0]) ? $params[0] : null;
            return $this->{$match[1]}($property, $value);
        }

        throw new \BadMethodCallException(sprintf(
            'Unknown method %s called on %s',
            $method, get_class($this)
        ));
    }
    */

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