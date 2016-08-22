<?php
/**
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license MIT
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @since 0.27
 */

namespace Applications\Auth;

use Zend\I18n\Translator\TranslatorInterface as Translator;
use Auth\Entity\UserInterface as User;
use Zend\Mvc\Router\RouteInterface as Router;
use Auth\Dependency\ModuleInterface;
use Auth\Dependency\ModuleItem;

class DependencyModule implements ModuleInterface
{

    /**
     * @see \Auth\Dependency\ModuleInterface::getTitle()
     */
    public function getTitle(Translator $translator)
    {
        return $translator->translate('Applications');
    }

    /**
     * @see \Auth\Dependency\ModuleInterface::getCount()
     */
    public function getCount(User $user)
    {
        return 1;
    }

    /**
     * @see \Auth\Dependency\ModuleInterface::getItems()
     */
    public function getItems(User $user, Router $router)
    {
        return [
            new ModuleItem('Another', $router->assemble(['id' => 'another'], ['name' => 'lang/applications/detail']))
        ];
    }
    
    /**
     * @see \Auth\Dependency\ModuleInterface::removeItems()
     */
    public function removeItems(User $user)
    {}
}
