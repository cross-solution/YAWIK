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

/**
 * Entities implementing this interface are able to provide meta data.
 *
 * This meta data should primarly be used for database interactions.
 *  *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0.27
 */
interface MetaDataProviderInterface
{

    /**
     * Get meta data.
     *
     * Returns the whole meta data array, if no <i>$key</i> is provided.
     * Returns <i>null</i>, if there is no meta data for the provided <i>$key</i>.
     *
     * @param null|string $key
     *
     * @return array|mixed|null
     */
    public function getMetaData($key = null);

    /**
     * Set meta data.
     *
     * @param string $key
     * @param mixed $value
     *
     * @return self
     */
    public function setMetaData($key, $value);

    /**
     * Check if a meta data with a specific key is available.
     *
     * @param string $key
     *
     * @return bool
     */
    public function hasMetaData($key);
}
