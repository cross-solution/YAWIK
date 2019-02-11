<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2019 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Core\Queue;

use Core\Queue\Exception\AbstractJobException;
use Core\Queue\Exception\FatalJobException;
use SlmQueue\Job\AbstractJob;
use SlmQueue\Job\JobPluginManager;

/**
 * ${CARET}
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test 
 */
class LazyJob extends AbstractJob
{
    protected $container;

    public function __construct(JobPluginManager $container, array $options = null)
    {
        $this->container = $container;
        if (isset($options['name'])) {
            $this->setServiceName($options['name']);
        }
        if (isset($options['options'])) {
            $this->setServiceOptions($options['options']);
        }
        if (isset($options['content'])) {
            $this->setContent($options['content']);
        }
    }

    public function setServiceName($name)
    {
        $this->setMetadata('service_name', $name);
    }

    public function getServiceName()
    {
        return $this->getMetadata('service_name');
    }

    public function setServiceOptions($options)
    {
        $this->setMetadata('service_options', $options);
    }

    public function getServiceOptions()
    {
        return $this->getMetadata('service_options');
    }

    public function execute()
    {
        $service = $this->getServiceName();

        if ($this->container->has($service)) {
            $job = $this->container->get($service, $this->getServiceOptions());

        } elseif (class_exists($service)) {
            $options = $this->getServiceOptions();
            $job = $options ? new $service(...$options) : new $service;

        } else {
            throw new FatalJobException('A job with name "' . $service . '" could not be created.');
        }

        $job->setContent($this->getContent());
        if ($job instanceOf LazyJobWrapperAwareInterface) {
            $job->setWrapper($this);
        }

        return $job->execute();
    }

}
