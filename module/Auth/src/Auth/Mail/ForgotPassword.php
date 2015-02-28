<?php

namespace Auth\Mail;

use Auth\Entity\UserInterface;
use Core\Mail\StringTemplateMessage;
use Zend\Mime\Message;
use Zend\Mime\Part as MimePart;

class ForgotPassword extends StringTemplateMessage
{
    /**
     * @var UserInterface
     */
    protected $user;

    protected $resetLink;

    public function init()
    {
        $email = $this->user->getInfo()->getEmail();
        if (!($name = trim($this->user->getInfo()->getDisplayName()))) {
            $name = $email;
        }

        $variables = array(
            'name' => $name ? $name : $email,
            'resetLink' => $this->resetLink
        );

        $this->setVariables($variables);

        $this->setTo($email, $name !== $email ? $name : null);
        $subject = /*@translate*/ 'Password recovery';

        if ($this->isTranslatorEnabled()) {
            $subject = $this->getTranslator()->translate($subject);
        }
        $this->setSubject($subject);

        $bodyHtml =
            <<<HTML
%sHello ##name##,

You (or someone else) have requested to reset your password.

If you follow the link below you will be able to personally reset your password.
%s##resetLink##%s

This password reset request is valid for the next 24 hours.
%s
HTML;

        if ($this->isTranslatorEnabled()) {
            $bodyHtml = $this->getTranslator()->translate($bodyHtml);
        }

        $bodyText = str_replace('%s', '', $bodyHtml);
        $bodyHtml = sprintf(
            $bodyHtml,
            '<html><body>',
            '<a href="##resetLink##" title="Link to reset password">',
            '</a>',
            '</body></html>'
        );
        $bodyHtml = str_replace("\n", "<br />", $bodyHtml);

        $textPart = new MimePart($bodyText);
        $textPart->type = "text/plain";

        $htmlPart = new MimePart($bodyHtml);
        $htmlPart->type = "text/html";

        $body = new Message();
        $body->setParts(array($textPart, $htmlPart));

        $this->setBody($body);

        return $this;
    }

    protected function setUser(UserInterface $user)
    {
        $this->user = $user;
    }

    protected function setResetLink($resetLink)
    {
        $this->resetLink = $resetLink;
    }
}