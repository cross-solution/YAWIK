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
use Zend\View\Renderer\PhpRenderer as View;

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
     * @param View $view
     * @param int $limit
     * @return ListItem[]
     */
    public function getItems(User $user, View $view, $limit);

    /**
     * @return \Traversable
     */
    public function getEntities(User $user);
}
