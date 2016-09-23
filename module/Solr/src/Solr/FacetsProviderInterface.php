<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @since 0.27
 */

namespace Solr;

interface FacetsProviderInterface
{
    /**
     * @return Facets
     */
    public function getFacets();
}