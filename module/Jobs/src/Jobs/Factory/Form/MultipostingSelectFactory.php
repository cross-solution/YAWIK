<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2015 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Jobs\Factory\Form;


use Jobs\Form\MultipostingSelect;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Factory for the Multiposting select box
 * 
 * @author Carsten Bleek <bleek@cross-solution.de>
 */
class MultipostingSelectFactory implements FactoryInterface
{
    /**
     * Creates the multiposting select box.
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /* @var $serviceLocator \Zend\ServiceManager\AbstractPluginManager
         * @var $headScript     \Zend\View\Helper\HeadScript
         * @var $channels       \Jobs\Options\ProviderOptions
         * @var $currency       \Zend\I18n\View\Helper\CurrencyFormat */
        $services = $serviceLocator->getServiceLocator();
        $router = $services->get('Router');
        $select  = new MultipostingSelect();
        $helpers = $services->get('ViewHelperManager');
        $headScript = $helpers->get('headScript');
        $basePath  = $helpers->get('basePath');
        $currencyFormat  = $helpers->get('currencyFormat');

        $channels = $serviceLocator->getServiceLocator()->get('Jobs/Options/Provider');

        $headScript->appendFile($basePath('Jobs/js/form.multiposting-select.js'));

        $groups = array();

        foreach ($channels as $name=>$channel) {
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
                            . $currencyFormat($channel->getPrice(),$channel->getCurrency(),2) . '|'
                            . $channel->getPrice();
        }


        $select->setAttributes(array(
            'data-autoinit' => 'false',
            'multiple' => 'multiple'
        ));

        $select->setValueOptions($groups);

        return $select;
    }
}