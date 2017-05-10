<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** ErrorLoggerFactory.php */
namespace Core\Log;

use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Log\Logger;
use Zend\Log\Writer\Stream;
use Core\Log\Filter\ErrorType;
use Core\Log\Formatter\ErrorAndExceptionHandler;

class ErrorLoggerFactory implements FactoryInterface
{
    /**
     * Create an object
     *
     * @param  ContainerInterface $container
     * @param  string             $requestedName
     * @param  null|array         $options
     *
     * @return object
     * @throws ServiceNotFoundException if unable to resolve the service.
     * @throws ServiceNotCreatedException if an exception is raised when
     *     creating a service.
     * @throws ContainerException if any other error occurs
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('Config');
        $config = isset($config['log']['ErrorLogger']['config'])
            ? $config['log']['ErrorLogger']['config']
            : array();

        if (!isset($config['stream'])) {
            throw new \RuntimeException('A stream must be configured for ErrorLogger.');
        }

        $formatter = new ErrorAndExceptionHandler(
            array(
                'dateTimeFormat' => 'Y-m-d H:i:s',
            )
        );
        $writer = new Stream($config['stream']);
        $writer->setFormatter($formatter);
        $logger = new Logger();

        if (!isset($config['log_errors']) || !$config['log_errors']) {
            $writer->addFilter(new ErrorType(ErrorType::TYPE_EXCEPTION));

        } else {
            Logger::registerErrorHandler($logger);
        }

        if (!isset($config['log_exceptions']) || !$config['log_exceptions']) {
            $writer->addFilter(new ErrorType(ErrorType::TYPE_ERROR));
        } else {
            Logger::registerExceptionHandler($logger);
        }
        $logger->addWriter($writer);
        return $logger;

    }


    /* (non-PHPdoc)
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator, Logger::class);
    }
}
