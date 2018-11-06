<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2017 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Core\Service;

use Core\Listener\TracyListener;
use Core\Options\ModuleOptions;
use Psr\Container\ContainerInterface;
use Tracy\Debugger;
use Tracy\Logger;
use Tracy\Helpers;
use Tracy\Dumper;
use Zend\EventManager\EventManagerInterface;
use Zend\Mail\Message;
use Zend\Mail\Transport;
use Zend\Mime;
use Zend\Stdlib\ArrayUtils;

/**
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @since 0.28
 * @package Core\Service
 */
class Tracy
{
    /**
     * @var TracyListener
     */
    private $listener;

    /**
     * True if debugging already started
     * @var bool
     */
    private $started = false;

    /**
     * @var array
     */
    private $config = [];

    public function __construct(EventManagerInterface $eventManager, $config)
    {
        $defaults = [
            'log' => getcwd().'/var/log/tracy'
        ];
        $this->config = ArrayUtils::merge($defaults, $config);
        $listener = new TracyListener();
        $listener->attach($eventManager);
        $this->listener = $listener;
    }

    public static function factory(ContainerInterface $container)
    {
        /* @var EventManagerInterface $eventManager */
        $eventManager = $container->get('Application')->getEventManager();
        $config = $container->get('Config')['tracy'];

        // we always use log dir from Core Options
        /* @var ModuleOptions $coreOptions */
        $coreOptions = $container->get('Core/Options');
        $log = $coreOptions->getLogDir().'/tracy';
        if (!is_dir($log)) {
            mkdir($log, 0777, true);
        }
        $config['log'] = $log;

        return new static($eventManager,$config);
    }

    public function startDebug()
    {
        if ($this->started) {
            return;
        }
        $config = $this->config;
        if (!is_dir($dir = $config['log'])) {
            @mkdir($dir, 0777, true);
        }
        $this->register($this->config);
        $this->listener->startListen();
        $this->started = true;
    }

    /**
     * @param array $config
     */
    private function register(array $config)
    {
        // enable logging of all error types globally
        Debugger::enable($config['mode'], $config['log'], $config['email']);
        Debugger::$strictMode = $config['strict'];
        Debugger::$showBar = $config['bar'];

        /** @var Logger $logger */
        $logger = Debugger::getLogger();
        $logger->emailSnooze = $config['email_snooze'];
        $logger->mailer = function ($message, $email) use ($logger) {
            $exceptionFile = null;

            if ($message instanceof \Exception || $message instanceof \Throwable) {
                $exceptionFile = $logger->getExceptionFile($message);
                $tmp = [];
                while ($message) {
                    $tmp[] = (
                        $message instanceof \ErrorException
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
