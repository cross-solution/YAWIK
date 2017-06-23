<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Jobs\Entity\Decorator;

use Jobs\Entity\JobInterface;
use Jobs\Entity\JsonLdProviderInterface;

/**
 * ${CARET}
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test 
 */
class JsonLdProvider implements JsonLdProviderInterface
{

    private $job;

    public function __construct(JobInterface $job)
    {
        $this->job = $job;
    }

    public function toJsonLd()
    {
        return '{}';
    }
}