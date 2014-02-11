<?php
/**
 * Cross Applicant Management
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   AGPLv3
 */

/** ErrorLoggerFactory.php */ 
namespace Core\Log;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Log\Logger;
use Zend\Log\Writer\Stream;
use Core\Log\Filter\ErrorType;
use Core\Log\Formatter\ExceptionHandler;

class ErrorLoggerFactory implements FactoryInterface
{
    
    
    /* (non-PHPdoc)
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     */
    public function createService(ServiceLocatorInterface $serviceLocator) {
        
        $config = $serviceLocator->get('Config');
        $config = isset($config['log']['ErrorLogger']['config']) 
                ? $config['log']['ErrorLogger']['config']
                : array();
        
        if (!isset($config['stream'])) {
            throw new \RuntimeException('A stream must be configured for ErrorLogger.');
        }
        
        $logger = new Logger();
        
        if (isset($config['log_errors']) && $config['log_errors']) {
            $errorWriter = new Stream($config['stream']);
            $errorWriter->setFormatter('ErrorHandler', array(
                'dateTimeFormat' => 'Y-m-d H:i:s'
            ));
            $errorWriter->addFilter(new ErrorType(ErrorType::TYPE_ERROR));
            $logger->addWriter($errorWriter);
            Logger::registerErrorHandler($logger);
        }
        
        if (isset($config['log_exceptions']) && $config['log_exceptions']) {
            $exceptionWriter = new Stream($config['stream']);
            $formatter = new ExceptionHandler();
            $formatter->setDateTimeFormat('Y-m-d H:i:s');
            $exceptionWriter->setFormatter($formatter);
            $exceptionWriter->addFilter(new ErrorType(ErrorType::TYPE_EXCEPTION));
            $logger->addWriter($exceptionWriter);
            Logger::registerExceptionHandler($logger);
        }
        
        return $logger;

    }

}

