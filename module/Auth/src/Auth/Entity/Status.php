<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Auth\Entity;

use Core\Entity\AbstractEntity;
use Jobs\Entity\StatusInterface;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Zend\I18n\Translator\TranslatorInterface as Translator;

/**
 * User status entity
 *
 * @ODM\EmbeddedDocument
 */
class Status extends AbstractEntity implements StatusInterface
{

    /**
     * status values
     */
    protected static $orderMap = [
        self::ACTIVE => 50,
        self::INACTIVE => 60
    ];

    /**
     * name of the job status
     *
     * @var string
     * @ODM\Field(type="string")
     */
    protected $name;

    /**
     * integer for ordering states.
     *
     * @var int
     * @ODM\Field(type="int")
     */
    protected $order;

    /**
     * @param string $status
     * @throws \DomainException
     */
    public function __construct($status = self::ACTIVE)
    {
        if (!isset(static::$orderMap[$status])) {
            throw new \DomainException('Unknown status: ' . $status);
        }
        
        $constant = 'self::' . strtoupper($status);
        $this->name = constant($constant);
        $this->order = $this->getOrder();
    }

    /**
     * @see \Jobs\Entity\StatusInterface::getName()
     * @return String
     */
    public function getName()
    {
        return isset($this->name) ? $this->name : '';
    }

    /**
     * @see \Jobs\Entity\StatusInterface::getOrder()
     * @return Int
     */
    public function getOrder()
    {
        return self::$orderMap[$this->getName()];
    }

    public function __toString()
    {
        return $this->getName();
    }

    public function getStates()
    {
        $states = self::$orderMap;
        asort($states, SORT_NUMERIC);
        return array_keys($states);
    }
    
    /**
     * @param Translator $translator
     * @return array
     */
    public function getOptions(Translator $translator)
    {
        $options = [];
        
        foreach ($this->getStates() as $state)
        {
            $options[$state] = $translator->translate($state);
        }
        
        return $options;
    }
}
