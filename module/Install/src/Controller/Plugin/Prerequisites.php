<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2015 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Install\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;

/**
 * ${CARET}
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test 
 */
class Prerequisites extends AbstractPlugin
{

    const DIR_EXISTS       = 4; // 0100
    const DIR_IS_WRITABLE  = 8; // 1000
    const DIR_IS_CREATABLE = 2; // 0010
    const DIR_IS_MISSING   = 1; // 0001

    protected $directories = array(
        'config/autoload' => 'exists',
        'cache' => 'writable|creatable',
        'log' => 'writable|creatable',
    );

    public function __invoke($directories = null)
    {
        return $this->check($directories);
    }

    public function check($directories = null)
    {
        null == $directories && $directories = $this->directories;
        $return = array(); $valid = true;

        foreach ($directories as $path => $validationSpec) {
            $result = $this->checkDirectory($path, $validationSpec);
            $return['directories'][$path] = $result;
            $valid = $valid && $result['valid'];
        }
        $return['valid'] = $valid;

        return $return;
    }

    public function checkDirectory($dir, $validationSpec)
    {
        $exists = file_exists($dir);
        $writable = $exists && is_writable($dir);
        $missing = !$exists;
        $creatable = $missing && is_writable(dirname($dir));

        $return = array(
            'exists' => $exists,
            'writable' => $writable,
            'missing' => $missing,
            'creatable' => $creatable
        );

        $return['valid'] = $this->validateDirectory($return, $validationSpec);

        return $return;
    }

    public function validateDirectory($result, $spec)
    {
        $spec = explode('|', $spec);
        $valid = false;
        foreach ($spec as $s) {
            if (isset($result[$s]) && $result[$s]) {
                $valid = true;
                break;
            }
        }

        return $valid;
    }
}