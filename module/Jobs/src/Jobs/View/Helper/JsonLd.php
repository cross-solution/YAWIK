<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Jobs\View\Helper;

use Jobs\Entity\Decorator\JsonLdProvider;
use Zend\View\Helper\AbstractHelper;

/**
 * ${CARET}
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test 
 */
class JsonLd extends AbstractHelper
{
    private $job;

    public function __invoke($job = null)
    {
        $job = $job ?: $this->job;
        if (null === $job) {
            return '';
        }

        $jsonLdProvider = new JsonLdProvider($job);

        return '<script type="application/ld+json">'
               . $jsonLdProvider->toJsonLd()
               . '</script>';

    }

    /**
     * @param mixed $job
     *
     * @return self
     */
    public function setJob($job)
    {
        $this->job = $job;

        return $this;
    }


    
}