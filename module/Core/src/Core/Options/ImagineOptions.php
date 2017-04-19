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
 * ${CARET}
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test 
 */
class ImagineOptions extends AbstractOptions
{

    const LIB_GD = 'Gd';

    const LIB_IMAGICK = 'Imagick';

    const LIB_GMAGICK = 'Gmagick';

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