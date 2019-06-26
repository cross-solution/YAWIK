<?php

/*
 * This file is part of the Yawik project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Core\Bridge\HtmlPurifier;

use Zend\Filter\AbstractFilter;
use Zend\Filter\Exception;
use HTMLPurifier;

class HTMLPurifierFilter extends AbstractFilter
{
    /**
     * @var HTMLPurifier
     */
    protected $htmlPurifier;

    /**
     * @var array
     */
    protected $config = array();

    /**
     * Returns the result of filtering $value
     *
     * @param string $value
     * @throws Exception\RuntimeException If filtering $value is impossible
     * @return string
     */
    public function filter($value)
    {
        return $this->getHtmlPurifier()->purify($value);
    }
    /**
     * @return HTMLPurifier
     */
    public function getHtmlPurifier()
    {
        if (!$this->htmlPurifier) {
            if (!isset($this->config['Cache.SerializerPath'])) {
                $this->config['Cache.SerializerPath'] = sys_get_temp_dir();
            }
            $this->htmlPurifier = new HTMLPurifier($this->config);
        }
        return $this->htmlPurifier;
    }
    /**
     * @param HTMLPurifier $purifier
     */
    public function setHtmlPurifier(HTMLPurifier $purifier)
    {
        $this->htmlPurifier = $purifier;
    }
    /**
     * @param array $config
     */
    public function setConfig(array $config)
    {
        $this->config = $config;
    }
    /**
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }
}