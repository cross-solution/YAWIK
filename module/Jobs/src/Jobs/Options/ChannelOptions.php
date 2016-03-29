<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @author cbleek
 * @license   MIT
 */

namespace Jobs\Options;

use Zend\Stdlib\AbstractOptions;

/**
 * Class ChannelOptions
 *
 * Jobs can be published on channels
 *
 * @package Jobs\Options
 */
class ChannelOptions extends AbstractOptions
{

    /**
     * Unique key of the channel
     *
     * @var int $key
     */
    protected $key;

    /**
     * external key of a channel. Eg. a provider offers the channel "MyJobboard" with the key "123".
     * YAWIK provides a channel "MyJobboard" using the key "myjobborad". Set externalkey to "123", if
     * the job is published to the provider.
     *
     * @var int $externalkey
     */
    protected $externalkey = 0;

    /**
     * An array defining prices.
     *
     * e.g. [ 'purchase' => 100, 'list' => 300 ];
     *
     * @var array
     */
    protected $prices = [];


    /**
     * Currency of the price
     *
     * @var string $currency
     */
    protected $currency;

    /**
     * Tax of the channel
     *
     * @var int $tax
     */
    protected $tax;

    /**
     * Label of the Channel.
     *
     * @var string
     */
    protected $label;

    /**
     * Logo of the Channel.
     *
     * @var string
     */
    protected $logo;

    /**
     * days to publish a job posting
     *
     * @var int $publishDuration
     */
    protected $publishDuration=30;

    /**
     * Category for this channel
     *
     * @var string
     */
    protected $category = 'General';

    /**
     * descriptive title of the channel
     *
     * @var string $title
     */
    protected $headLine;

    /**
     * Long description of the channel. This description may contain one Link.
     *
     * @var string $description
     */
    protected $description;

    /**
     * Link text which references a mor information page about a channel
     *
     * @var string
     */
    protected $linkText;

    /**
     * Link target which references a mor information page about a channel
     *
     * @var string
     */
    protected $linkTarget;

    /**
     * Route to a content page with details about the channel
     *
     * @var string
     */
    protected $route;

    /**
     * Parameter, which can be used for linking the detail page about the channel
     *
     * @var array
     */
    protected $params;

    /**
     * If true, the channel can be selected and deselected after publishing
     *
     * @var bool
     */
    protected $editableAfterPublish = false;

    /**
     * Sets the unique key of a channel
     *
     * @param string $key
     * @return ChannelOptions
     */
    public function setKey($key)
    {
        $this->key=$key;
        return $this;
    }

    /**
     * Gets the unique key of a channel
     *
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Sets the unique key of a channel
     *
     * @param string $key
     * @return ChannelOptions
     */
    public function setExternalkey($key)
    {
        $this->externalkey=$key;
        return $this;
    }

    /**
     * Gets the unique key of a channel
     *
     * @return string
     */
    public function getExternalkey()
    {
        return $this->externalkey;
    }

    /**
     * Gets a specific price of a channel
     *
     * @param string $key Key of the price in the prices array
     *
     * @return float
     */
    public function getPrice($key)
    {
        return $this->prices[$key];
    }

    /**
     * Sets a specific price of a channel
     *
     * @param string $key Key of the price in the prices array
     * @param float $price
     *
     * @return self
     */
    public function setPrice($key, $price)
    {
        $this->prices[$key] = $price;

        return $this;
    }

    /**
     * Gets the prices array
     *
     * @return array
     */
    public function getPrices()
    {
        return $this->prices;
    }

    /**
     * Sets the prices array
     *
     * @param array $prices
     *
     * @return self
     */
    public function setPrices(array $prices)
    {
        $this->prices = $prices;

        return $this;
    }

    /**
     * Gets the currency of a price
     *
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * Sets the currency of a price
     *
     * @param int $currency
     * @return ChannelOptions
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
        return $this;
    }

    /**
     * Gets the tax for a price
     *
     * @return string
     */
    public function getTax()
    {
        return $this->tax;
    }

    /**
     * Sets the tax for a price
     *
     * @param int $tax
     * @return ChannelOptions
     */
    public function setTax($tax)
    {
        $this->tax = $tax;
        return $this;
    }

    /**
     * Gets the label of a channel. Eg. "YAWIK Jobboard"
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Sets the label of a channel
     *
     * @param string $label
     * @return ChannelOptions
     */
    public function setLabel($label)
    {
        $this->label = $label;
        return $this;
    }


    /**
     * Gets the publish duration of a channel in days
     *
     * @return int
     */
    public function getPublishDuration()
    {
        return $this->publishDuration;
    }

    /**
     * Sets the publish duration of a job posting for a channel in days
     *
     * @param string $publishDuration
     * @return ChannelOptions
     */
    public function setPublishDuration($publishDuration)
    {
        $this->publishDuration = $publishDuration;
        return $this;
    }

    /**
     * Sets the category name.
     *
     * @param string $category
     *
     * @return self
     */
    public function setCategory($category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Gets the category name.
     *
     * @return string
     */
    public function getCategory()
    {
        return $this->category;
    }



    /**
     * Gets the headline of the channel
     *
     * @return mixed
     */
    public function getHeadLine()
    {
        return $this->headLine;
    }

    /**
     * Sets the headline of a channel
     *
     * @param $headLine
     * @return $this
     */
    public function setHeadLine($headLine)
    {
        $this->headLine = $headLine;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param $description
     * @return $this
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLinkText()
    {
        return $this->linkText;
    }

    /**
     * @param $linkText
     * @return $this
     */
    public function setLinkText($linkText)
    {
        $this->linkText = $linkText;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * @param $route
     * @return $this
     */
    public function setRoute($route)
    {
        $this->route = $route;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @param $params
     * @return $this
     */
    public function setParams($params)
    {
        $this->params = $params;
        return $this;
    }

    /**
     * Gets the logo of a channel (Jobboard)
     *
     * @return string|null
     */
    public function getLogo()
    {
        return $this->logo;
    }

    /**
     * Set the logo of a channel (Jobboard)
     *
     * @param $logo
     * @return $this
     */
    public function setLogo($logo = null)
    {
        $this->logo = $logo;
        return $this;
    }

    /**
     * Gets the logo of a channel (Jobboard)
     *
     * @return bool
     */
    public function getEditableAfterPublish()
    {
        return $this->editableAfterPublish;
    }

    /**
     * Set the logo of a channel (Jobboard)
     *
     * @param $editableAfterPublish
     * @return $this
     */
    public function setEditableAfterPublish($editableAfterPublish = false)
    {
        $this->editableAfterPublish = $editableAfterPublish;
        return $this;
    }
}
