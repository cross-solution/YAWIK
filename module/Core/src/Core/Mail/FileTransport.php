<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */

namespace Core\Mail;

use Zend\Mail\Transport\File as BaseFileTransport;
use Zend\Mail\Transport\Exception\RuntimeException;

/**
 * A class to handle mail transport during tests
 *
 * @package Core\Mail
 * @author Anthonius Munthi <me@itstoni.com>
 * @since 0.30.1
 */
class FileTransport extends BaseFileTransport
{
    /**
     * Saves e-mail message to a file
     *
     * @param \Zend\Mail\Message $message
     * @throws RuntimeException on not writable target directory or
     * on file_put_contents() failure
     */
    public function send(\Zend\Mail\Message $message)
    {
        $options  = $this->options;
        $filename = call_user_func($options->getCallback(), $this);
        $file     = $options->getPath() . DIRECTORY_SEPARATOR . $filename;


        $contents = $message->toString();
        $umask = umask();
        umask(022);
        if (false === file_put_contents($file, $contents, LOCK_EX)) {
            throw new RuntimeException(sprintf(
                'Unable to write mail to file (directory "%s")',
                $options->getPath()
            ));
        }
        umask($umask);
        $this->lastFile = $file;
    }
}
