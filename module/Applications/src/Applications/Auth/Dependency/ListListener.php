<?php
/**
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license MIT
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @since 0.27
 */

namespace Applications\Auth\Dependency;

use Zend\I18n\Translator\TranslatorInterface as Translator;
use Auth\Entity\UserInterface as User;
use Zend\Mvc\Router\RouteInterface as Router;
use Auth\Dependency\ListInterface;
use Auth\Dependency\ListItem;

class ListListener implements ListInterface
{

    public function __invoke()
    {
        return $this;
    }
    
    /**
     * @see \Auth\Dependency\ListInterface::getTitle()
     */
    public function getTitle(Translator $translator)
    {
        return $translator->translate('Applications');
    }

    /**
     * @see \Auth\Dependency\ListInterface::getCount()
     */
    public function getCount(User $user)
    {
        return 1;
    }

    /**
     * @see \Auth\Dependency\ListInterface::getItems()
     */
    public function getItems(User $user, Router $router)
    {
        return [
            new ListItem('Another', $router->assemble(['id' => 'another'], ['name' => 'lang/applications/detail']))
        ];
    }
}
