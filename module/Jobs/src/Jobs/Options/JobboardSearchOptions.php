<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
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

    protected $perPage = 10;


    /**
     * @return int
     */
    public function getPerPage(){
        return $this->perPage;
    }

    /**
     * @param $perPage
     */
    public function setPerPage($perPage) {
        $this->perPage=$perPage;
    }

}