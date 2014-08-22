<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2104 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** OrganizationEntityHydrator.php */ 
namespace Organizations\Entity\Hydrator;

use Core\Entity\Hydrator\EntityHydrator;
use Zend\Stdlib\Hydrator\HydratorInterface;
use Settings\Entity\SettingsContainerInterface;
use Zend\Stdlib\Hydrator\AbstractHydrator;
use Zend\Stdlib\Hydrator\Reflection;

class OrganizationHydrator extends Reflection
{
    protected $repOrganization; 
    protected $repOrganizationName;
    protected $repOrganizationImage;
    protected $data;
    protected $object;
            
    public function __construct($repOrganization, $repOrganizationName, $repOrganizationImage)
    {
        parent::__construct();
        $this->repOrganization = $repOrganization; 
        $this->repOrganizationName = $repOrganizationName;
        $this->repOrganizationImage = $repOrganizationImage;
        //$httpload = new HttploadStrategy($repOrganizationImage);
        //$organizationName = new OrganizationNameStrategy($repOrganizationName);
        //$this->addStrategy('image', $httpload);
        //$this->addStrategy('organizationName', $organizationName);
    }
    
/**
     * Extract values from an object
     *
     * @param  object $object
     * @return array
     */
    public function extract($object)
    {
        $result = array();
        foreach (self::getReflProperties($object) as $property) {
            $propertyName = $property->getName();
            if (!$this->filterComposite->filter($propertyName)) {
                continue;
            }
            $getter = 'get' . ucfirst($propertyName);
            $value = method_exists($object, $getter)
                   ? $object->$getter()
                   : $property->getValue($object);

            $result[$propertyName] = $this->extractValue($propertyName, $value);
        }

        return $result;
    }

    /**
     * Hydrate $object with the provided $data.
     *
     * @param  array $data
     * @param  object $object
     * @return object
     */
    public function hydrate(array $data, $object)
    {
        $this->data = $data;
        $this->object = $object;
        $this->hydrateSkimData();
        $reflProperties = self::getReflProperties($object);
        foreach ($this->data as $key => $value) {
            if (isset($reflProperties[$key])) {
                $value  = $this->hydrateValue($key, $value);
                $setter = 'set' . ucfirst($key);
                if (method_exists($object, $setter)) { 
                    $object->$setter($value);
                } else {
                    // the values of the entity have to be set explicitly
                    $reflProperties[$key]->setValue($object, $this->hydrateValue($key, $value));
                }
            }
        }
        return $object;
    }
    
    
    /**
     * Converts a value for hydration. If no strategy exists the plain value is returned.
     *
     * @param string $name The name of the strategy to use.
     * @param mixed $value The value that should be converted.
     * @param array $data The whole data is optionally provided as context.
     * @return mixed
     */
    public function hydrateValue($name, $value)
    {
        if ($this->hasStrategy($name)) {
            $strategy = $this->getStrategy($name);
            $value = $strategy->hydrate($value, $this->data, $this->object);
        }
        return $value;
    }
    
    protected function hydrateSkimData() {
        if (!empty($this->data['image']) && is_string($this->data['image'])) {
            // image uri is given, decide if image should be downloaded
            $image = $this->object->getImage();
            if (isset($image)) {
                $uri = $image->getImageUri();
                if (!empty($uri) && $uri == $this->data['image']) {
                    unset($this->data['image']);
                }
            }
        }
    }
}
