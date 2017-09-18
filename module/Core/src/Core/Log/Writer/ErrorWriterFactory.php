<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Core\Log\Writer;

use Core\Log\Filter\ErrorType;
use Core\Log\Formatter\ErrorAndExceptionHandler;
use Interop\Container\ContainerInterface;
use Zend\Log\Writer\Stream;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * ${CARET}
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test 
 */
class ErrorWriterFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $writer = new Stream($options);

        if (!isset($options['log_errors']) || !$options['log_errors']) {
            $writer->addFilter(new ErrorType(ErrorType::TYPE_EXCEPTION));
        }

        if (!isset($options['log_exceptions']) || !$options['log_exceptions']) {
            $writer->addFilter(new ErrorType(ErrorType::TYPE_ERROR));
        }

        $formatter = new ErrorAndExceptionHandler(
            array(
                'dateTimeFormat' => 'Y-m-d H:i:s',
            )
        );

        $writer->setFormatter($formatter);

        return $writer;
    }
}
