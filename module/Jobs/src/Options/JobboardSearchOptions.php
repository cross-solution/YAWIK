<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
/**
 * to configure the options, please copy the config/JobboardSearchOptions.config.local.php.dist into your
 * config/autoload directory.
 */
  
/** */
namespace Jobs\Options;

use Core\Options\FieldsetCustomizationOptions;

/**
 * ${CARET}
 *
 * @author Carsten Bleek <bleek@cross-solution.de>
 */
class JobboardSearchOptions extends FieldsetCustomizationOptions
{
    /**
     * Fields can be disabled.
     *
     * @var array
     */
    protected $fields=[
       'q' => [
            'enabled' => true
        ],
        'l' => [
            'enabled' => true
        ],
        'd' => [
            'enabled' => true
        ],
        'c' => [
            'enabled' => true
        ],
        't' => [
            'enabled' => true,
        ]
    ];

    /**
     * Sets the number of items per page on the Jobboard search result
     *
     * @var int
     */
    protected $perPage = 10;

    /**
     * @return int
     */
    public function getPerPage()
    {
        return $this->perPage;
    }

    /**
     * @param $perPage
     *
     * @return $this
     */
    public function setPerPage($perPage)
    {
        $this->perPage=$perPage;
        return $this;
    }
}
