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
use ArrayIterator;

/**
 * Class ModuleOptions
 *
 * Default options of the Jobs Module
 *
 * @package Jobs\Options
 */
class ProviderOptions extends AbstractOptions implements \IteratorAggregate {

    /**
     * List of channels a user can publish job postings
     *
     * @var array
     */
    protected $channels = array();

    public function __construct()
    {
        $this->channels = array();
        //$this->long_label = '';
    }

    /**
     * @return ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->channels);
    }

    /**
     * @param ChannelOptions $channel
     * @return $this
     */
    public function addChannel(ChannelOptions $channel)
    {
        $key = $channel->getKey();
        $this->channels[$key] = $channel;
        return $this;
    }

    /**
     * @param string $key
     * @return $this
     */
    public function getChannel($key)
    {
        if (array_key_exists($key, $this->channels)) {
            return $this->channels[$key];
        }
        return array();
    }

    /**
     * Gets the list of possible channels a job opening can posted
     *
     * @return array
     */
    public function getChannels()
    {
        return $this->channels;
    }

}