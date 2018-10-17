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

class EmployeeListListener implements ListInterface
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
        return $translator->translate('Employees');
    }

    /**
     * @see \Auth\Dependency\ListInterface::getCount()
     */
    public function getCount(User $user)
    {
        $employees = $this->getEmployees($user);
        
        return $employees ? $employees->count() : 0;
    }

    /**
     * @see \Auth\Dependency\ListInterface::getItems()
     */
    public function getItems(User $user, View $view, $limit)
    {
        $items = [];
        $employees = $this->getEmployees($user);
        
        if (!$employees) {
            return $items;
        }
        
        foreach ($employees->slice(0, $limit) as $employee) /* @var $employee \Organizations\Entity\Employee */
        {
            $info = $employee->getUser()->getInfo();
            $title = $info->getDisplayName();
            $items[] = new ListItem($title);
        }
        
        return $items;
    }
    
    /**
     * @param User $user
     * @return \Doctrine\Common\Collections\Collection|null
     */
    protected function getEmployees(User $user)
    {
        $organization = $user->getOrganization();
        
        if (!$organization) {
            return;
        }
        
        $organization = $organization->getOrganization();
        
        if (!$organization) {
            return;
        }
        
        return $organization->getEmployees();
    }
    
    /**
     * @see \Auth\Dependency\ListInterface::getEntities()
     */
    public function getEntities(User $user)
    {
        return $this->getEmployees($user) ?: [];
    }
}
