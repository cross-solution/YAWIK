<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */

/**
 * The price calculations is probably something you wanna do completely different. You can do so by writing
 * your own filter.
 * This can be done by defining your Factory in the Jobs\config\modules.config.php
 *
 * 'filters' => [
 *   'factories'=> [
 *      'Jobs/ChannelPrices'  => 'Your\Factory\Filter\YourPriceCalculationFactory',
 *      ...
 *     ]
 *  ]
 *
 * You should create a Factory to be able to inject the Options into your Calculation class. Set the name of the
 * $filter class in your Factory to you FQN of your Calculation class and implement your calculation.
 */
namespace Jobs\Filter;

use Jobs\Options\ChannelOptions;
use Jobs\Options\ProviderOptions;
use Zend\Filter\Exception;
use Zend\Filter\FilterInterface;

/**
 * ${CARET}
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test
 */
class ChannelPrices implements FilterInterface
{
    protected $providers;

    public function __construct(ProviderOptions $providers)
    {
        $this->providers = $providers;
    }
    /**
     * This filter allows you to loop over the selected Channels. Each channel can have three
     * prices 'min', 'base', 'list'. The default calculation simply adds a discount of 13,5% if
     * more than one channel is selected.
     *
     * In addition, you'll get a special discount of 100 whatever, if your job will be posted on
     * jobs.yawik.org :-)
     *
     * Returns the result of filtering $value
     *
     * @param  array $value
     *
     * @throws Exception\RuntimeException If filtering $value is impossible
     * @return mixed
     */
    public function filter($value = [])
    {
        $sum = 0;
        $amount = 0;
        $absoluteDiscount = 0;
        if (empty($value)) {
            return 0;
        }

        foreach ($value as $channelKey) {
            /* @var $channel ChannelOptions */
            $channel = $this->providers->getChannel($channelKey);
            if ('yawik' == $channelKey) {
                $absoluteDiscount = 100;
            }
            if ($channel instanceof ChannelOptions && $channel->getPrice('base')>0) {
                $sum += $channel->getPrice('base');
                $amount++;
            }
        }
        $discount=1-($amount-1)*13.5/100;
        if ($discount>0) {
            $sum= round($sum * $discount, 2);
        }
        return $sum-$absoluteDiscount;
    }
}
