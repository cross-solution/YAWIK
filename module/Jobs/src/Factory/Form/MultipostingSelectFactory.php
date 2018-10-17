<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Jobs\Factory\Form;

use Interop\Container\ContainerInterface;
use Jobs\Form\MultipostingSelect;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Factory for the Multiposting select box
 *
 * @author Carsten Bleek <bleek@cross-solution.de>
 */
class MultipostingSelectFactory implements FactoryInterface
{

    /**
     * Create an object
     *
     * @param  ContainerInterface $container
     * @param  string             $requestedName
     * @param  null|array         $options
     *
     * @return object
     * @throws ServiceNotFoundException if unable to resolve the service.
     * @throws ServiceNotCreatedException if an exception is raised when
     *     creating a service.
     * @throws ContainerException if any other error occurs
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {

        /* @var $headScript     \Zend\View\Helper\HeadScript
         * @var $channels       \Jobs\Options\ProviderOptions
         * @var $currency       \Zend\I18n\View\Helper\CurrencyFormat */

        $router = $container->get('Router');
        $select  = new MultipostingSelect();
        $helpers = $container->get('ViewHelperManager');
        $currencyFormat  = $helpers->get('currencyFormat');

        $channels = $container->get('Jobs/Options/Provider');

        // $headScript = $helpers->get('headScript');
        // $basePath  = $helpers->get('basePath');
        // $headScript->appendFile($basePath('Jobs/js/form.multiposting-select.js'));

        $groups = array();

        foreach ($channels as $name => $channel) {
            /* @var $channel \Jobs\Options\ChannelOptions */

            $category = $channel->getCategory();

            if (!isset($groups[$category])) {
                $groups[$category] = array('label' => $category);
            }

            $link = $router->assemble($channel->getParams(), array('name' => $channel->getRoute()));
            $groups[$category]['options'][$channel->getKey()] =
                $channel->getLabel() . '|'
                . $channel->getHeadLine() . '|'
                . $channel->getDescription() . '|'
                . $channel->getLinkText() . '|'
                . $link . '|' . $channel->getPublishDuration() . '|'
                . $channel->getLogo();
        }


        $select->setAttributes(
            array(
                'data-autoinit' => 'false',
                'multiple' => 'multiple'
            )
        );

        $select->setValueOptions($groups);

        return $select;
    }
}
