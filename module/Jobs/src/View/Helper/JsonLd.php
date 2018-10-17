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
use Jobs\Entity\JobInterface;
use Jobs\Entity\JsonLdProviderInterface;
use Zend\View\Helper\AbstractHelper;

/**
 * Print the JSON-LD representation of a job.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test
 */
class JsonLd extends AbstractHelper
{
    /**
     *
     *
     * @var JobInterface
     */
    private $job;

    /**
     * Print the JSON-LD representation of a job.
     *
     * Wraps it in <script type="application/ld+json"> tag
     *
     * @param JsonLdProvider|null $job
     *
     * @return string
     */
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
     * Set the default job to use, if invoked without arguments.
     *
     * @param JobInterface $job
     *
     * @return self
     */
    public function setJob(JobInterface $job)
    {
        $this->job = $job;

        return $this;
    }
}
