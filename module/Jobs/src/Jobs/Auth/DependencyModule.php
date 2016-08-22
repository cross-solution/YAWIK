<?php
/**
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license MIT
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @since 0.27
 */

namespace Jobs\Auth;

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
        return $translator->translate('Jobs');
    }

    /**
     * @see \Auth\Dependency\ModuleInterface::getCount()
     */
    public function getCount(User $user)
    {
        return 2;
    }

    /**
     * @see \Auth\Dependency\ModuleInterface::getItems()
     */
    public function getItems(User $user, Router $router)
    {
        return [
            new ModuleItem('First'),
            new ModuleItem('Second', $router->assemble(['action' => 'edit'], ['name' => 'lang/jobs/manage', 'query' => ['id' => 'second']]))
        ];
    }
    
    /**
     * @see \Auth\Dependency\ModuleInterface::removeItems()
     */
    public function removeItems(User $user)
    {}
}
