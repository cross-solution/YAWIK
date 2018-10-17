<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** OrganizationEntityHydrator.php */
namespace Organizations\Entity\Hydrator;

use Zend\Hydrator\Reflection;

/**
 * Class OrganizationHydrator
 * @package Organizations\Entity\Hydrator
 */
class OrganizationHydrator extends Reflection
{
    /**
     * @var $repOrganization \Organizations\Repository\Organization
     */
    protected $repOrganization;

    /**
     * @var $repOrganizationName \Organizations\Repository\OrganizationName
     */
    protected $repOrganizationName;

    /**
     * @var $repOrganizationImage \Organizations\Repository\OrganizationImage
     */
    protected $repOrganizationImage;

    protected $data;

    protected $object;
            
    public function __construct($repOrganization, $repOrganizationName, $repOrganizationImage)
    {
        parent::__construct();
        $this->repOrganization = $repOrganization;
        $this->repOrganizationName = $repOrganizationName;
        $this->repOrganizationImage = $repOrganizationImage;
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
        // download Image
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
    public function hydrateValue($name, $value, $data = null)
    {
        if ($this->hasStrategy($name)) {
            $strategy = $this->getStrategy($name);
            $value = $strategy->hydrate($value, $this->data, $this->object);
        }
        return $value;
    }

    protected function hydrateSkimData()
    {
        if (!empty($this->data['image']) && is_string($this->data['image'])) {
            // image uri is given, decide if image should be downloaded
            $image = $this->object->getImage();
            if (isset($image)) {
                $uri = $image->getUri();
                if (!empty($uri) && $uri == $this->data['image']) {
                    unset($this->data['image']);
                }
            }
        }
    }
}
