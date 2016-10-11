<?php
/**
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license MIT
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @since 0.27
 */

namespace Jobs\Auth\Dependency;

use Zend\I18n\Translator\TranslatorInterface as Translator;
use Auth\Entity\UserInterface as User;
use Zend\View\Renderer\PhpRenderer as View;
use Auth\Dependency\ListInterface;
use Auth\Dependency\ListItem;
use Jobs\Repository\Job as Repository;

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
        return $translator->translate('Jobs');
    }

    /**
     * @see \Auth\Dependency\ListInterface::getCount()
     */
    public function getCount(User $user)
    {
        return $this->repository->getUserJobs($user->getId())->count();
    }

    /**
     * @see \Auth\Dependency\ListInterface::getItems()
     */
    public function getItems(User $user, View $view, $limit)
    {
        $items = [];
        
        foreach ($this->repository->getUserJobs($user->getId(), $limit) as $job) /* @var $job \Jobs\Entity\Job */
        {
            $title = $job->getTitle() ?: $view->translate('untitled');
            $title .= ' ('. $view->dateFormat($job->getDateCreated(), 'short', 'none') . ')';
            $url = $view->url('lang/jobs/manage', ['action' => 'edit'], [
                'query' => [
                    'id' => $job->getId()
                ]
            ]);
            $items[] = new ListItem($title, $url);
        }
        
        return $items;
    }
    
    /**
     * @see \Auth\Dependency\ListInterface::getEntities()
     */
    public function getEntities(User $user)
    {
        return $this->repository->getUserJobs($user->getId());
    }
}
