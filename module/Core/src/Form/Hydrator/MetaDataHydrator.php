<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Core\Form\Hydrator;

use Core\Entity\MetaDataProviderInterface;
use Zend\Hydrator\HydratorInterface;

/**
 * Hydrator for entity meta data.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0.29
 */
class MetaDataHydrator implements HydratorInterface
{
    /**
     * Extract meta data from an entity.
     *
     * @param  object $object
     *
     * @return array
     */
    public function extract($object)
    {
        if (!$object instanceof MetaDataProviderInterface) {
            return [];
        }

        $data = $object->getMetaData();

        return $data;
    }

    /**
     * Hydrate $object with the provided meta data array.
     *
     * @param  array  $data
     * @param  object $object
     *
     * @return object
     */
    public function hydrate(array $data, $object)
    {
        if (!$object instanceof MetaDataProviderInterface) {
            return $object;
        }

        foreach ($data as $key => $value) {
            $object->setMetaData($key, $value);
        }

        return $object;
    }
}
