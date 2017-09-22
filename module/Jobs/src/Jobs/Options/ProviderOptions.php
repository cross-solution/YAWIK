<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
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
class ProviderOptions extends AbstractOptions implements \IteratorAggregate
{

    /**
     * List of channels a user can publish job postings
     *
     * @var array
     */
    protected $channels = array();

    public function __construct()
    {
        $this->channels = array();
    }

    /**
     * @return ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->channels);
    }

    /**
     * Adds a channel (aka job portal)
     *
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
     * Get a channel by "key"
     *
     * @param string $key
     * @return ChannelOptions
     */
    public function getChannel($key)
    {
        if (array_key_exists($key, $this->channels)) {
            return $this->channels[$key];
        }
        return null;
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
