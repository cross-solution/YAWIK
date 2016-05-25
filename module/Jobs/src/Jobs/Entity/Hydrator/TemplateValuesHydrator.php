<?php
/**
 * YAWIK
 *
 * @filesource
 * @license    MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */

/** */
namespace Jobs\Entity\Hydrator;

use Core\Entity\Hydrator\EntityHydrator;
use Jobs\Entity\TemplateValues;

/**
 * Hydrator handles {@link \Jobs\Entity\TemplateValues}.
 *
 * Additionally hydrates and extracts the FreeValues keys it is configured to care for.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since  0.23
 */
class TemplateValuesHydrator extends EntityHydrator
{
    /**
     * The FreeValues keys to care about.
     *
     * @var array
     */
    protected $freeValuesKeys = [];

    /**
     * Creates an instance.
     *
     * @param array|null $freeValuesKeys
     */
    public function __construct($freeValuesKeys = null)
    {
        parent::__construct();

        if ($freeValuesKeys) {
            $this->setFreeValuesKeys($freeValuesKeys);
        }
    }

    /**
     * Gets the FreeValues keys this instance cares about.
     *
     * @return array
     */
    public function getFreeValuesKeys()
    {
        return $this->freeValuesKeys;
    }

    /**
     * Sets the FreeValues keys to care about.
     *
     * @param array $freeValuesKeys
     *
     * @return self
     */
    public function setFreeValuesKeys(array $freeValuesKeys)
    {
        $this->freeValuesKeys = $freeValuesKeys;

        return $this;
    }

    /**
     * Extract values (including the configured FreeValues) from an TemplateValues entity.
     *
     * @param TemplateValues $object
     *
     * @return array
     */
    public function extract($object)
    {
        $data = parent::extract($object);

        foreach ($this->getFreeValuesKeys() as $key) {
            $data[$key] = $object->get($key);
        }

        return $data;
    }

    /**
     * Hydrate a TemplateValues entity with the provided $data including the configured FreeValues.
     *
     * @param  array          $data
     * @param  TemplateValues $object
     *
     * @return TemplateValues
     */
    public function hydrate(array $data, $object)
    {
        parent::hydrate($data, $object);

        foreach ($this->getFreeValuesKeys() as $key) {
            if (isset($data[$key])) {
                $object->set($key, $data[$key]);
            }
        }

        return $object;
    }
}
