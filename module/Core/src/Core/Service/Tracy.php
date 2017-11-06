<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2017 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Core\Service;

use Tracy\Debugger;
use Tracy\Logger;
use Tracy\Helpers;
use Tracy\Dumper;
use Zend\Mail\Message;
use Zend\Mail\Transport;
use Zend\Mime;
/**
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @since 0.28
 * @package Core\Service
 */
class Tracy
{
    /**
     * @param array $config
     */
    public function register(array $config)
    {
        // enable logging of all error types globally
        Debugger::enable($config['mode'], $config['log'], $config['email']);
        Debugger::$strictMode = $config['strict'];
        Debugger::$showBar = $config['bar'];
        
        /** @var Logger $logger */
        $logger = Debugger::getLogger();
        $logger->emailSnooze = $config['email_snooze'];
        $logger->mailer = function ($message, $email) use ($logger)
        {
            $exceptionFile = null;
            
            if ($message instanceof \Exception || $message instanceof \Throwable) {
                $exceptionFile = $logger->getExceptionFile($message);
                $tmp = [];
                while ($message) {
                    $tmp[] = ($message instanceof \ErrorException
                        ? Helpers::errorTypeToString($message->getSeverity()) . ': ' . $message->getMessage()
                        : Helpers::getClass($message) . ': ' . $message->getMessage() . ($message->getCode() ? ' #' . $message->getCode() : '')
                    ) . ' in ' . $message->getFile() . ':' . $message->getLine();
                    $message = $message->getPrevious();
                }
                $message = implode("\ncaused by ", $tmp);
                
                if ($exceptionFile) {
                    $message .= "\n\nException file: ".basename($exceptionFile);
                }
    
            } elseif (!is_string($message)) {
                $message = Dumper::toText($message);
            }

            $message = trim($message);
            $host = preg_replace('#[^\w.-]+#', '', isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : php_uname('n'));
            $mimeMessage = new Mime\Message();
            
            $text = new Mime\Part($message . "\n\nsource: " . Helpers::getSource());
            $text->type = Mime\Mime::TYPE_TEXT;
            $text->charset = 'utf-8';
            $mimeMessage->addPart($text);
            
            if ($exceptionFile) {
                $attachment = new Mime\Part(fopen($exceptionFile, 'r'));
                $attachment->type = Mime\Mime::TYPE_HTML;
                $attachment->filename = basename($exceptionFile);
                $attachment->disposition = Mime\Mime::DISPOSITION_ATTACHMENT;
                $mimeMessage->addPart($attachment);
            }
            
            $mailMessage = (new Message())
                ->addFrom("noreply@$host")
                ->addTo(array_map('trim', explode(',', $email)))
                ->setSubject("PHP: An error occurred on the server $host")
                ->setBody($mimeMessage);
                
            $mailMessage->getHeaders()
                ->addHeaderLine('X-Mailer', 'Tracy');
            
            (new Transport\Sendmail())->send($mailMessage);
        };
    }
}
