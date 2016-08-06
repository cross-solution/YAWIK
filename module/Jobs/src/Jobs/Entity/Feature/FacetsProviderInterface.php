<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Jobs\Entity\Feature;

/**
 * Interface FacetsProviderInterface
 * @author      Anthonius Munthi <me@itstoni.com>
 * @since       0.26
 * @package     Jobs\Entity\Feature
 */
interface FacetsProviderInterface
{
    /**
     * Get facets results
     *
     * @return array
     */
    public function getFacets();
}