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

interface NativeLanguageInterface extends EntityInterface
{
    /*
     * name of the language de,en,fr
     */
    public function setLanguage($language);
    public function getLanguage();
}
