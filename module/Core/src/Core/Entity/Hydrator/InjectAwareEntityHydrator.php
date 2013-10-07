<?php
/**
 * Cross Applicant Management
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

/** InjectAwareEntityHydrator.php */ 
namespace Core\Entity\Hydrator;

class InjectAwareEntityHydrator extends EntityHydrator
{
    protected $injectProperties;
    
    public function __construct(array $injectProperties)
    {
        parent::__construct();
        $this->injectProperties = $injectProperties;
        
    }
    
    public function extract($object)
    {
        $data = parent::extract($object);
        
        foreach ($this->injectProperties as $property) {
            $method = "get$property";
            if (method_exists($object, $method)) {
                $data[$property] = $this->extractValue($property, $object->$method());
            }
        }
        return $data;
        
    }
    
    public function hydrate(array $data, $object)
    {
        $object = parent::hydrate($data, $object);
        
        foreach ($this->injectProperties as $property) {
            $method = "inject$property";
            if (isset($data[$property]) && method_exists($object, $method)) {
                $object->$method($this->hydrateValue($property, $data[$property]));
            }
        }
    }
}

