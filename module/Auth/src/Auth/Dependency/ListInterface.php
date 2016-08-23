<?php
/**
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license MIT
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @since 0.27
 */

namespace Auth\Dependency;

use Zend\I18n\Translator\TranslatorInterface as Translator;
use Auth\Entity\UserInterface as User;
use Zend\Mvc\Router\RouteInterface as Router;

interface ListInterface
{

    /**
     * @param Translator $translator
     * @return string
     */
    public function getTitle(Translator $translator);

    /**
     * @param User $user
     * @return int
     */
    public function getCount(User $user);

    /**
     * @param User $user
     * @param Router $router
     * @return ListItem[]
     */
    public function getItems(User $user, Router $router);
}
