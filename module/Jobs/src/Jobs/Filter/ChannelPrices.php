<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2015 Cross Solution <http://cross-solution.de>
 */
  
/** */
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
     * Returns the result of filtering $value
     *
     * @param  mixed $value
     *
     * @throws Exception\RuntimeException If filtering $value is impossible
     * @return mixed
     */
    public function filter($value)
    {
        $sum = 0;
        foreach ($value as $channelKey) {
            $channel = $this->providers->getChannel($channelKey);

            $sum += $channel->getPrice('base');
        }
        return $sum;
    }


}