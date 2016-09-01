<?php
/**
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license MIT
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @since 0.27
 */

namespace Organizations\Auth\Dependency;

use Zend\I18n\Translator\TranslatorInterface as Translator;
use Auth\Entity\UserInterface as User;
use Zend\View\Renderer\PhpRenderer as View;
use Auth\Dependency\ListInterface;
use Auth\Dependency\ListItem;
use Organizations\Repository\Organization as Repository;

class ListListener implements ListInterface
{
    
    /**
     * @var Repository
     */
    protected $repository;
    
    /**
     * @param Repository $repository
     */
    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke()
    {
        return $this;
    }
    
    /**
     * @see \Auth\Dependency\ListInterface::getTitle()
     */
    public function getTitle(Translator $translator)
    {
        return $translator->translate('Organizations');
    }

    /**
     * @see \Auth\Dependency\ListInterface::getCount()
     */
    public function getCount(User $user)
    {
        return $this->repository->getUserOrganizations($user->getId())->count();
    }

    /**
     * @see \Auth\Dependency\ListInterface::getItems()
     */
    public function getItems(User $user, View $view, $limit)
    {
        $items = [];
        
        foreach ($this->repository->getUserOrganizations($user->getId(), $limit) as $organization) /* @var $organization \Organizations\Entity\Organization */
        {
            $name = $organization->getOrganizationName();
            $title = $name ? $name->getName() : '**** DRAFT ****';
            $url = $view->url('lang/organizations/edit', ['id' => $organization->getId()]);
            $items[] = new ListItem($title, $url);
        }
        
        return $items;
    }
    
    /**
     * @see \Auth\Dependency\ListInterface::getEntities()
     */
    public function getEntities(User $user)
    {
        return $this->repository->getUserOrganizations($user->getId());
    }
}
