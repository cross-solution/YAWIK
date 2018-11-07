<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license       MIT
 */

namespace Jobs\Factory\Controller;

use Interop\Container\ContainerInterface;
use Jobs\Controller\TemplateController;
use Jobs\Repository;
use Zend\ServiceManager\Factory\FactoryInterface;

class TemplateControllerFactory implements FactoryInterface
{

    /**
     * Create an object
     *
     * @param  ContainerInterface $container
     * @param  string             $requestedName
     * @param  null|array         $options
     *
     * @return object
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /**
         * @var $jobRepository Repository\Job
         */
        $jobRepository = $container->get('repositories')->get('Jobs/Job');
        $options = $container->get('Jobs/Options');
        $viewModelTemplateFilter = $container->get('Jobs/ViewModelTemplateFilter');
        $translator = $container->get('translator');
        $viewHelper = $container->get('ViewHelperManager');
        $formManager = $container->get('FormElementManager');
        return new TemplateController(
            $jobRepository,
            $viewModelTemplateFilter,
            $translator,
            $options,
            $viewHelper,
            $formManager
        );
    }
}
