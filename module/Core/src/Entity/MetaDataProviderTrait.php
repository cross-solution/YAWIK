<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */

/** */
namespace Core\Entity;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Implementation of \Core\Entity\MetaDataProviderInterface
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0.27
 */
trait MetaDataProviderTrait
{
    /**
     * The meta data array.
     *
     * @ODM\Field(type="hash")
     * @var array
     */
    private $metaData = [];

    public function setMetaData($key, $value = null)
    {
        if (is_array($key)) {
            $this->metaData  = $key;
        } else {
            $this->metaData[$key] = $value;
        }

        return $this;
    }

    /**
     * Unset the metadata with a specific key
     *
     * @param string|int $key
     *
     * @since 0.33.15
     */
    public function unsetMetaData($key): void
    {
        unset($this->metaData[$key]);
    }

    /**
     * Get meta data.
     *
     * Returns the whole meta data array, if no <i>$key</i> is provided.
     * Returns <i>$default</i>, if there is no meta data for the provided <i>$key</i>.
     *
     * @param null|string $key
     * @param null|mixed $default
     *
     * @return array|mixed|null
     */
    public function getMetaData($key = null, $default = null)
    {
        if (null === $key) {
            return $this->metaData;
        }

        return $this->hasMetaData($key) ? $this->metaData[$key] : $default;
    }

    public function hasMetaData($key)
    {
        return array_key_exists($key, $this->metaData);
    }
}
