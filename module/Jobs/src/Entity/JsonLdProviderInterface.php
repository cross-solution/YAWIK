<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright https://yawik.org/COPYRIGHT.php
 */

/** */
namespace Jobs\Entity;

/**
 * Provide a toJsonLd method.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since :version
 */
interface JsonLdProviderInterface
{

    /**
     * Creates a JSON-LD representation of this object.
     *
     * JSON-LD is specified in https://developers.google.com/search/docs/data-types/job-postings
     * Results can be tested in https://search.google.com/structured-data/testing-tool
     *
     * @return string
     */
    public function toJsonLd();
}
