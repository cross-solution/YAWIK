<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Applications\Form\Element;

use Core\Form\HeadscriptProviderInterface;
use Jobs\Entity\JobInterface;
use Zend\Form\Element\Select;

/**
 * Select element for job titles
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0.29.2
 */
class JobSelect extends Select implements HeadscriptProviderInterface
{
    private $scripts = [ 'Applications/js/form.job-select.js' ];

    /**
     * Sets the array of script names.
     *
     * @param string[] $scripts
     *
     * @return self
     */
    public function setHeadscripts(array $scripts)
    {
        $this->scripts = $scripts;

        return $this;
    }

    /**
     * Gets the array of script names.
     *
     * @return string[]
     */
    public function getHeadscripts()
    {
        return $this->scripts;
    }

    /**
     * Set the pre selected job.
     *
     * @param JobInterface $job
     *
     * @return self
     */
    public function setSelectedJob(JobInterface $job)
    {
        $this->setValueOptions([
            '0' => '',
            $job->getId() => $job->getTitle(),
        ]);

        $this->setValue($job->getId());

        return $this;
    }

    public function init()
    {
        $this->setAttribute('data-element', 'job-select');

        if (!count($this->getValueOptions())) {
            $this->setValueOptions(['0' => '']);
        }
    }
}