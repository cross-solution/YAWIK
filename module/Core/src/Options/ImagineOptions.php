<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Core\Options;

use Zend\Stdlib\AbstractOptions;

/**
 * Options for the Imagine library.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0.29
 */
class ImagineOptions extends AbstractOptions
{

    /**#@+
     * Image library.
     *
     * @var string
     */
    const LIB_GD = 'Gd';
    const LIB_IMAGICK = 'Imagick';
    const LIB_GMAGICK = 'Gmagick';

    /**#@-*/

    /**
     * The image library to be used.
     *
     * @var string
     */
    protected $imageLib = self::LIB_GD;

    /**
     * @param string $imageLib
     *
     * @return self
     */
    public function setImageLib($imageLib)
    {
        $this->imageLib = $imageLib;

        return $this;
    }

    /**
     * @return string
     */
    public function getImageLib()
    {
        return $this->imageLib;
    }
}
