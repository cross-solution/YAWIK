<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** Message.php */
namespace Core\Mail;

use Auth\Entity\UserInterface;
use Traversable;
use Zend\Mail\Address;
use Zend\Mail\AddressList;
use Zend\Mail\Exception;
use Zend\Mail\Message as ZfMessage;

class Message extends ZfMessage
{
    public function __construct(array $options = array())
    {
        $this->setOptions($options);
    }
    
    public function setOptions($options)
    {
        if (!is_array($options) && !$options instanceof \Traversable) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Expected $options to be an array or \Traversable, but received %s',
                    (is_object($options) ? 'instance of ' . get_class($options) : 'skalar')
                )
            );
        }
        
        foreach ($options as $key => $value) {
            $method = "set$key";
            if (method_exists($this, $method)) {
                $this->$method($value);
            }
        }
        return $this;
    }

    protected function updateAddressList(AddressList $addressList, $emailOrAddressOrList, $name, $callingMethod)
    {
        if (null === $emailOrAddressOrList) {
            return;
        }

        if ($emailOrAddressOrList instanceof UserInterface) {
            parent::updateAddressList(
                         $addressList,
                         $emailOrAddressOrList->getInfo()->getEmail(),
                         $emailOrAddressOrList->getInfo()->getDisplayName(false),
                         $callingMethod
            );
            return;
        }

        if (is_array($emailOrAddressOrList)) {
            $list = new AddressList();
            foreach ($emailOrAddressOrList as $email => $displayName) {
                if ($displayName instanceof UserInterface) {
                    $info = $displayName->getInfo();
                    $list->add($info->getEmail(), $info->getDisplayName(false));
                    continue;
                }

                if (is_int($email)) {
                    $email = $displayName;
                    $displayName = null;
                }

                $list->add($email, $displayName);
            }
            $emailOrAddressOrList = $list;
        }

        parent::updateAddressList($addressList, $emailOrAddressOrList, $name, $callingMethod);
    }
}
