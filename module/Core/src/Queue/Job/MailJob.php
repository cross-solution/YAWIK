<?php

/**
 * YAWIK
 *
 * @see       https://github.com/cross-solution/YAWIK for the canonical source repository
 * @copyright https://github.com/cross-solution/YAWIK/blob/master/COPYRIGHT
 * @license   https://github.com/cross-solution/YAWIK/blob/master/LICENSE
 */

declare(strict_types=1);

namespace Core\Queue\Job;

use Laminas\Mail\Message;

/**
 * TODO: description
 *
 * @author Mathias Gelhausen
 * TODO: write tests
 */
class MailJob extends MongoJob implements MailSenderInterface
{

    private $mail;

    public static function create($payload = null)
    {
        if ($payload instanceof Message) {
            return static::fromMessage($payload);
        }

        return parent::create($payload);
    }

    public static function fromMessage(Message $mail)
    {
        $payload = $mail->toString();

        return parent::create($payload);
    }

    public static function fromMailSpec(string $mailServiceName, $options = null)
    {
        return parent::create([$mailServiceName, $options]);
    }

    public function execute()
    {
        $mail = $this->getContent();

        if (is_string($mail)) {
            $mail = Message::fromString($mail);
        }

        $this->mail = $mail;
    }

    public function getMail()
    {
        return $this->mail;
    }
}
