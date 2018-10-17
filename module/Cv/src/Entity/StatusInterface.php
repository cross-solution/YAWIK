<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */
namespace Cv\Entity;

use Core\Entity\EntityInterface;

/**
 * CV StatusInterface
 */
interface StatusInterface extends EntityInterface
{
    const NONPUBLIC =  /*@translate*/ 'private';

    const PUBLIC_TO_ALL =  /*@translate*/ 'public to all';

    public function __construct($status = self::NONPUBLIC);

    /**
     * Gets the name of an state.
     */
    public function getName();

    /**
     * Gets the order of an state.
     */
    public function getOrder();

    /**
     * Converts entity into a string
     */
    public function __toString();

    /**
     * Gets the array of all possible states
     */
    public function getStates();
}
