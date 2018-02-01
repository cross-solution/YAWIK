<?php

/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */

namespace Yawik\Behat;

use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Core\Mail\FileTransport;
use Webmozart\Assert\Assert;

class MailContext implements Context
{
    use CommonContextTrait;

    /**
     * @var array
     */
    private $messages = array();

    /**
     * @var array
     */
    private $toMails = [];

    /**
     * @var array
     */
    private $fromMails = [];

    /**
     * @var array
     */
    private $subjects = [];

    /**
     * Cleans all files before start test
     * @BeforeScenario @mail
     * @param BeforeScenarioScope $scope
     */
    public function beforeScenario(BeforeScenarioScope $scope)
    {
        $core = $scope->getEnvironment()->getContext(CoreContext::class);
        /* @var FileTransport $transport */
        $transport = $core->getServiceManager()->get('Core/MailService')->getTransport();
        $path = $transport->getOptions()->getPath() . '/*.eml';
        foreach (glob($path) as $filename) {
            unlink($filename);
        }

        $this->subjects = $this->fromMails = $this->toMails = array();
    }

    /**
     * @Then an email should be sent to :address
     * @param string $email
     */
    public function mailShouldBeSentTo($email)
    {
        $this->iHaveEmailSent();
        Assert::oneOf(
            $email,
            $this->toMails
        );
    }

    /**
     * @Then an email should be sent from :address
     * @param string $email
     */
    public function mailShouldBeSentFrom($email)
    {
        $this->iHaveEmailSent();
        Assert::oneOf(
            $email,
            $this->fromMails
        );
    }

    /**
     * @Then email subject should contain :subject
     * @param string $subject
     */
    public function mailSubjectShouldBe($subject)
    {
        $this->iHaveEmailSent();
        Assert::oneOf(
            $subject,
            $this->subjects
        );
    }

    /**
     * @Then sent email should be contain :text
     */
    public function sentEmailShouldBeContain($text)
    {
        $this->iHaveEmailSent();
        $regex = '/.*('.preg_quote($text).').*/im';
        $matches = [];
        $multiMessages = false;
        if(count($this->messages) > 1){
            $multiMessages = true;
        }
        $content = "";
        foreach($this->messages as $key=>$definition){
            $content = $definition['contents'];
            if(preg_match($regex,$content,$match)){
                $matches[] = $match;
            }
        }
        $failMessage = sprintf('Can not find text "%s" in any email sent',$text);
        if(!$multiMessages){
            $failMessage = sprintf(
                'Can not find text "%s" in sent email. Here\'s the email content: %s',
                $text,
                PHP_EOL.PHP_EOL.$content
            );
        }
        Assert::true(count($matches)>0,$failMessage);
    }

    /**
     * @Then I have email sent
     */
    public function iHaveEmailSent()
    {
        /* @var FileTransport $transport */
        $transport = $this->getService('Core/MailService')->getTransport();

        $path = $transport->getOptions()->getPath().'/*.eml';

        foreach(glob($path) as $filename){
            $id = md5($filename);
            if(!isset($this->messages[$id])){
                $contents = file_get_contents($filename);
                $this->messages[$id]  = $this->parseEmail($contents);
            }
        }

        Assert::true(
            count($this->messages)>0,
            'No email have been sent'
        );
    }

    private function parseEmail($contents)
    {
        $addresses = $this->parseEmailAddress($contents);
        $subject =$this->parseSubject($contents);

        $contents = strip_tags($contents);
        $contents = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $contents);

        return array_merge($addresses,$subject,['contents' => $contents]);
    }

    private function parseEmailAddress($contents)
    {
        // pattern to get email address
        $radd =  '(\b[A-Z0-9._%+-]+@(?:[A-Z0-9-]+\.)+[A-Z]{2,6}\b)';

        // get email from address
        $regex = sprintf('/^From\:.*%s/im',$radd);
        $hasMatch = preg_match($regex,$contents,$matches);
        $fromAddress = $hasMatch ? $matches[1]:null;

        // get email to address
        $regex = sprintf('/^To\:\s+%s/im',$radd);
        $hasMatch = preg_match($regex,$contents,$matches);
        $toAddress1 = $hasMatch ? $matches[1]:null;

        // get email to address
        $regex = sprintf('/^To\:.*%s/im',$radd);
        $hasMatch = preg_match($regex,$contents,$matches);
        $toAddress2 = $hasMatch ? $matches[1]:null;

        $this->fromMails[] = $fromAddress;
        $this->toMails[] = $toAddress1;
        $this->toMails[] = $toAddress2;

        return [
            'from' => $fromAddress,
            'to' => [$toAddress1,$toAddress2],
        ];
    }

    private function parseSubject($contents)
    {
        $pattern = '/Subject\:(.*)/i';
        preg_match($pattern,$contents,$matches);
        $subject = isset($matches[1]) ? $matches[1]:null;
        $this->subjects[] = $subject;
        return [
            'subject' => trim($subject)
        ];
    }
}
