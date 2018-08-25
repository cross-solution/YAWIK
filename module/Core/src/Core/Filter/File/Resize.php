<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2018 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Core\Filter\File;

use Imagine\Image\Box;
use Zend\Filter\AbstractFilter;

/**
 * ${CARET}
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
class Resize extends AbstractFilter
{
    private $imagine;

    protected $options = [
        'width' => 0,
        'height' => 0,
        'min-width' => 0,
        'min-height' => 0,
        'max-width' => 0,
        'max-height' => 0,
    ];

    private $alreadyFiltered = [];

    public function __construct(array $options = [])
    {
        $this->setOptions($options);
    }

    /**
     * @return \Imagine\Gd\Imagine
     */
    public function getImagine()
    {
        return $this->imagine;
    }

    /**
     * @param mixed $imagine
     *
     * @return self
     */
    public function setImagine($imagine)
    {
        $this->imagine = $imagine;

        return $this;
    }



    public function filter($value)
    {
        if (!is_array($value) || UPLOAD_ERR_OK !== $value['error'] || 0 !== strpos($value['type'], 'image')) {
            return $value;
        }

        $name = $value['tmp_name'];

        if (isset($this->alreadyFiltered[$name])) {
            return $this->alreadyFiltered[$name];
        }

        $image = $this->getImagine()->open($name);
        $size  = $image->getSize();


        if ($this->options['width'] || $this->options['height']) {
            $size = new Box($this->options['width'] ?: $size->getWidth(), $this->options['height'] ?: $size->getHeight());
        } else {
            if ($this->options['max-width'] && $size->getWidth() > $this->options['max-width']) {
                $size = $size->widen($this->options['max-width']);
            }

            if ($this->options['max-height'] && $size->getHeight() > $this->options['max-height']) {
                $size = $size->heighten($this->options['max-height']);
            }

            if ($this->options['min-width'] && $size->getWidth() < $this->options['min-width']) {
                $size = $size->widen($this->options['min-width']);
            }

            if ($this->options['min-height'] && $size->getHeight() < $this->options['min-height']) {
                $size = $size->heighten($this->options['min-height']);
            }
        }

        $image->resize($size);
        $image->save($name, ['format' => substr($value['type'], 6)]);
        $value['size'] = filesize($name);
        $this->alreadyFiltered[$name] = $value;

        return $value;
    }
}
