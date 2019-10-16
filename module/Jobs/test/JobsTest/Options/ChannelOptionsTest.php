<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @author cbleek
 * @license   AGPLv3
 */

namespace JobsTest\Options;

use PHPUnit\Framework\TestCase;

use Jobs\Options\ChannelOptions as Options;

class ChannelOptionsTest extends TestCase
{
    /**
     * @var Options $options
     */
    protected $options;

    protected function setUp(): void
    {
        $options = new Options;
        $this->options = $options;
    }

    /**
     * @covers \Jobs\Options\ChannelOptions::getLabel
     * @covers \Jobs\Options\ChannelOptions::setLabel
     */
    public function testSetGetLabel()
    {
        $label="Jobsintown";

        $this->options->setLabel($label);
        $this->assertEquals($label, $this->options->getLabel());
    }

    /**
     * @covers \Jobs\Options\ChannelOptions::getKey
     * @covers \Jobs\Options\ChannelOptions::setKey
     */
    public function testSetGetKey()
    {
        $key="jobsintown";

        $this->options->setKey($key);
        $this->assertEquals($key, $this->options->getKey());
    }

    /**
     * @covers \Jobs\Options\ChannelOptions::getPrice
     * @covers \Jobs\Options\ChannelOptions::setPrice
     */
    public function testSetGetPrice()
    {
        $key='test';
        $price=199;
        $this->options->setPrice($key, $price);
        $this->assertEquals($price, $this->options->getPrice($key));
    }

    /**
     * @covers \Jobs\Options\ChannelOptions::getPrices
     * @covers \Jobs\Options\ChannelOptions::setPrices
     */
    public function testSetGetPrices()
    {
        $prices = [
            'test' => 1234,
            'one'  => 4321,
            'more' => 1234.56
        ];

        $this->assertSame($this->options, $this->options->setPrices($prices), 'Fluent interface broken');
        $this->assertEquals($prices, $this->options->getPrices());
    }

    /**
     * @covers \Jobs\Options\ChannelOptions::getTax
     * @covers \Jobs\Options\ChannelOptions::setTax
     */
    public function testSetGetTax()
    {
        $tax='21';

        $this->options->setTax($tax);
        $this->assertEquals($tax, $this->options->getTax());
    }

    /**
     * @covers \Jobs\Options\ChannelOptions::getCurrency
     * @covers \Jobs\Options\ChannelOptions::setCurrency
     */
    public function testSetGetCurrency()
    {
        $currency="EUR";

        $this->options->setCurrency($currency);
        $this->assertEquals($currency, $this->options->getCurrency());
    }

    /**
     * @covers \Jobs\Options\ChannelOptions::getPublishDuration
     * @covers \Jobs\Options\ChannelOptions::setPublishDuration
     */
    public function testSetGetPublishDuration()
    {
        $days=60;

        $this->options->setPublishDuration($days);
        $this->assertEquals($days, $this->options->getPublishDuration());
    }

    /**
     * @covers \Jobs\Options\ChannelOptions::getExternalKey
     * @covers \Jobs\Options\ChannelOptions::setExternalKey
     */
    public function testSetGetExternalKey()
    {
        $key="1234";

        $this->options->setExternalkey($key);
        $this->assertEquals($key, $this->options->getExternalkey());
    }

    /**
     * @covers \Jobs\Options\ChannelOptions::getCategory
     * @covers \Jobs\Options\ChannelOptions::setCategory
     */
    public function testSetGetCategory()
    {
        $category="Technical Jobs";

        $this->options->setCategory($category);
        $this->assertEquals($category, $this->options->getCategory());
    }

    /**
     * @covers \Jobs\Options\ChannelOptions::getHeadline
     * @covers \Jobs\Options\ChannelOptions::setHeadline
     */
    public function testSetGetHeadline()
    {
        $headline="Post Jobs on YAWIK";

        $this->options->setHeadLine($headline);
        $this->assertEquals($headline, $this->options->getHeadLine());
    }

    /**
     * @covers \Jobs\Options\ChannelOptions::getDescription
     * @covers \Jobs\Options\ChannelOptions::setDescription
     */
    public function testSetGetDescription()
    {
        $description="Post Jobs on YAWIK";

        $this->options->setDescription($description);
        $this->assertEquals($description, $this->options->getDescription());
    }

    /**
     * @covers \Jobs\Options\ChannelOptions::getLinkText
     * @covers \Jobs\Options\ChannelOptions::setLinkText
     */
    public function testSetGetLinkText()
    {
        $linkText="YAWIK";

        $this->options->setLinkText($linkText);
        $this->assertEquals($linkText, $this->options->getLinkText());
    }

    /**
     * @covers \Jobs\Options\ChannelOptions::getRoute
     * @covers \Jobs\Options\ChannelOptions::setRoute
     */
    public function testSetGetRoute()
    {
        $route="YAWIK";

        $this->options->setRoute($route);
        $this->assertEquals($route, $this->options->getRoute());
    }

    /**
     * @covers \Jobs\Options\ChannelOptions::getParams
     * @covers \Jobs\Options\ChannelOptions::setParams
     */
    public function testSetGetParams()
    {
        $p=array(
                    'view' => 'jobs-publish-on-jobsintown'
        );

        $this->options->setParams($p);
        $this->assertEquals($p, $this->options->getParams());
    }

    /**
     * @covers \Jobs\Options\ChannelOptions::getLogo
     * @covers \Jobs\Options\ChannelOptions::setLogo
     */
    public function testSetGetLogo()
    {
        $input="test.logo.gif";
        $this->options->setLogo($input);
        $this->assertEquals($input, $this->options->getLogo());
    }
}
