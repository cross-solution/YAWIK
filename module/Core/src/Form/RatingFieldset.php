<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** RatingFieldset.php */
namespace Core\Form;

use Zend\Form\Fieldset;
use Core\Entity\RatingInterface;
use Core\Entity\Hydrator\EntityHydrator;

class RatingFieldset extends Fieldset
{
    protected $isBuild = false;
    
    public function allowObjectBinding($object)
    {
        return $object instanceof RatingInterface;
    }
    
    public function getHydrator()
    {
        if (!$this->hydrator) {
            $this->setHydrator(new EntityHydrator());
        }
        return $this->hydrator;
    }
    
    public function setObject($object)
    {
        parent::setObject($object);
        $this->build();
        return $this;
    }
    
    public function build()
    {
        if ($this->isBuild) {
            return;
        }
        
        $rating = $this->getObject();
        $refl   = new \ReflectionClass($rating);
        $properties = $refl->getProperties();
        
        foreach ($properties as $property) {
            $name  = $property->getName();
            if ('_' == $name{0}) {
                continue;
            }
            $value = $rating->{'get' . $name}();
            $input = array(
                'type' => 'Core/Rating',
                'name' => $name,
                'options' => array(
                    'label' => ucfirst(preg_replace('~([A-Z])~', ' $1', $name)),
                    'value' => $value
                ),
            
            );
            $this->add($input);
        }
    }
}
