<?php
/**
 * YAWIK
 *
 * @filesource
 * @license    MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */

/** */
namespace Install\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;

/**
 * Checks permissions on directories or if the directories can be created or not.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since  0.20
 */
class Prerequisites extends AbstractPlugin
{

    /**
     * Default directories paths to check.
     *
     * @internal
     *  Value is the specification, when the checks are valid.
     *
     * @var array
     */
    protected $directories = array(
        'config/autoload' => 'exists',
        'var/cache'           => 'writable|creatable',
        'var/log'             => 'writable|creatable',
    );

    /**
     * Called when invoked directly.
     *
     * Proxies to {@link check()}
     *
     * @param null|array $directories
     *
     * @return array
     */
    public function __invoke($directories = null)
    {
        return $this->check($directories);
    }


    /**
     * Checks directories.
     *
     * Calls {@link checkDirectory()} for each directory in the array.
     * Checks, if all directories has passed as valid according to the specs.
     *
     * @param null|array $directories
     *
     * @return array
     */
    public function check($directories = null)
    {
        null == $directories && $directories = $this->directories;
        $return = array();
        $valid  = true;

        foreach ($directories as $path => $validationSpec) {
            $result                       = $this->checkDirectory($path, $validationSpec);
            $return['directories'][$path] = $result;
            $valid                        = $valid && $result['valid'];
        }
        $return['valid'] = $valid;

        return $return;
    }

    /**
     * Checks a directory.
     *
     * @param string $dir
     * @param string $validationSpec
     *
     * @return array
     */
    public function checkDirectory($dir, $validationSpec)
    {
        $exists    = file_exists($dir);
        $writable  = $exists && is_writable($dir);
        $missing   = !$exists;
        $creatable = $missing && is_writable(dirname($dir));

        $return = array(
            'exists'    => $exists,
            'writable'  => $writable,
            'missing'   => $missing,
            'creatable' => $creatable
        );

        $return['valid'] = $this->validateDirectory($return, $validationSpec);

        return $return;
    }

    /**
     * Validates a directory according to the spec
     *
     * @param array  $result
     * @param string $spec
     *
     * @return bool
     */
    public function validateDirectory($result, $spec)
    {
        $spec  = explode('|', $spec);
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
