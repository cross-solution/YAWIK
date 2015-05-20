<?php
/**
 * YAWIK
 * 
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    weitz@cross-solution.de
 */

namespace Jobs\Options;

use Zend\Stdlib\AbstractOptions;
use Jobs\Options\ChannelOptions;

/**
 * Class ModuleOptions
 *
 * Default options of the Jobs Module
 *
 * @package Jobs\Options
 */
class ProviderOptions extends AbstractOptions implements \IteratorAggregate {

    protected $channels = array();

    public function __construct()
    {
        $this->channels = array();
    }

    public function getIterator()
    {
        return new ArrayIterator($this->channels);
    }

    public function addChannel(ChannelOptions $channel)
    {
        $key = $channel->getKey();
        $this->channels[$key] = $channel;
        return $this;
    }
}