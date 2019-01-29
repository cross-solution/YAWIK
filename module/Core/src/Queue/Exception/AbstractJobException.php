<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2019 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Core\Queue\Exception;

/**
 * ${CARET}
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test 
 */
abstract class AbstractJobException extends \RuntimeException
{
    protected $options = [];

    public function __construct($message, array $options = [])
    {
        parent::__construct($message);

        $this->setOptions($options);
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return array_merge(
            [
                'message' => $this->getMessage(),
                'trace'   => $this->getTraceAsString()
            ],
            $this->options
        );

    }

    /**
     * @param array $options
     *
     * @return self
     */
    public function setOptions($options)
    {
        $this->options = $options;

        return $this;
    }


}
