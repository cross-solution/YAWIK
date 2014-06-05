<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2104 Cross Solution (http://cross-solution.de)
 * @license   AGPLv3
 */

/** Forward.php 
 *
 * generates an email containing an applications
 */ 
namespace Applications\Mail;

use Core\Mail\TranslatorAwareMessage;
use Zend\Mime;

class Forward extends TranslatorAwareMessage
{
    protected $application;
    protected $isInitialized = false;
    
    public function setApplication($application)
    {
        $this->application = $application;
        if ($this->isInitialized) {
            $this->generateBody();
        }
        return $this;
    }
    
    public function init()
    {
        $this->isInitialized = true;
        if (!$this->application) {
            return;
        }
        $this->setEncoding('UTF-8');
        $subject = /* @translate */ 'Fwd: Application to "%s" dated %s';
        if ($this->isTranslatorEnabled()) {
            $subject = $this->getTranslator()->translate($subject);
        } 
        $this->setSubject(sprintf(
            $subject,
            $this->application->job->title,
            strftime('%x', $this->application->dateCreated->getTimestamp())
        ));
        $this->generateBody();
    }
    
    protected function generateBody()
    {
        $message = new Mime\Message();
        
        
        $text = $this->generateText();
        $textPart = new Mime\Part($text);
        $textPart->type = 'text/plain';
        $textPart->charset = 'UTF-8';
        $textPart->disposition = Mime\Mime::DISPOSITION_INLINE;
        $message->addPart($textPart);

        if (isset($this->application->contact->image) && $this->application->contact->image->id) {
            $image = $this->application->contact->image;
            $part = new Mime\Part($image->getResource());
            $part->type = $image->type;
            $part->encoding = Mime\Mime::ENCODING_BASE64;
            $part->filename = $image->name;
            $part->disposition = Mime\Mime::DISPOSITION_ATTACHMENT;
            $message->addPart($part);
        }
        
        foreach ($this->application->attachments as $attachment) {
            $part = new Mime\Part($attachment->getResource());
            $part->type = $attachment->type;
            $part->encoding = Mime\Mime::ENCODING_BASE64;
            $part->filename = $attachment->name;
            $part->disposition = Mime\Mime::DISPOSITION_ATTACHMENT;
            $message->addPart($part);
        }
        
        $this->setBody($message);
    }
    
    protected function generateText() 
    {
        $translator = $this->isTranslatorEnabled() ? $this->getTranslator() : null;
        $t = function($text) use ($translator) { return $translator ? $translator->translate($text) : $text; };
        $keyValueRow = function($key, $value) use ($t) {
            $key = str_pad($t($key) . ':', 26);
            return $key . $value . PHP_EOL;
        };
        $delim = function($title=null) use ($t) {
            return "\n\n" . ($title ? $t($title) . PHP_EOL : "") . str_repeat('-', 76) . PHP_EOL;
        };

        $text = $keyValueRow('date of receipt', strftime('%x', $this->application->dateCreated->getTimestamp()))
              . $keyValueRow('last modification date', strftime('%x', $this->application->dateCreated->getTimestamp()))
              . $delim('personal information')
              . $this->application->contact->getAddress(/*$extended*/ true) . PHP_EOL
              . $delim('Summary')
              . wordwrap($this->application->summary, 76) . PHP_EOL
              . $delim('work experience');
        
        foreach ($this->application->cv->employments as $employment) {
            $descRaw = wordwrap($employment->description, 50);
            $range= $employment->startDate . ' - ' . $employment->endDate;
            $lines = explode(PHP_EOL, $descRaw);
            $line = array_shift($lines); 
            $desc = $range . "  " . $line;
            $space = str_repeat(" ", 25); 
            foreach ($lines as $line) {
                $desc .= "$space$line" . PHP_EOL;
            }
            $text .= $desc . PHP_EOL;
        }
              
        $text .= $delim('education and training');
        
        foreach ($this->application->cv->educations as $education) {
            $descRaw = wordwrap($education->description, 50);
            $range= $education->startDate . ' - ' . $education->endDate;
            $lines = explode(PHP_EOL, $descRaw);
            $line = array_shift($lines); 
            $desc = $range . ": " . $line . PHP_EOL;
            $space = str_repeat(" ", 25); 
            foreach ($lines as $line) {
                $desc .= "$space$line" . PHP_EOL;
            }
            $text .= $desc . PHP_EOL;
        }
       
        //@todo: assemble all Application info
        
        $text .= $delim('Attachments');
        
        if ($this->application->contact->image && $this->application->contact->image->id) {
            $text .= ' * ' . $this->application->contact->image->name . PHP_EOL;
        }
        
        foreach ($this->application->attachments as $attachment) {
            $text .= ' * ' . $attachment->name . PHP_EOL;
        }
              
        return trim($text);
    }
}

