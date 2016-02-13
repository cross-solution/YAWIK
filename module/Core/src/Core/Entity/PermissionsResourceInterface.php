<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** PermissionsInterface.php */
namespace Core\Entity;

interface PermissionsResourceInterface
{
    public function getPermissionsResourceId();
    public function getPermissionsUserIds();
}
