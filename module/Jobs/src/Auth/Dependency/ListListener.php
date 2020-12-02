<?php
/**
 * @filesource
 * @copyright https://yawik.org/COPYRIGHT.php
 * @license MIT
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @since 0.27
 */

namespace Jobs\Auth\Dependency;

use Doctrine\ODM\MongoDB\MongoDBException;
use Laminas\I18n\Translator\TranslatorInterface as Translator;
use Auth\Entity\UserInterface as User;
use Laminas\View\Renderer\PhpRenderer as View;
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
     * @see ListInterface::getTitle()
     */
    public function getTitle(Translator $translator)
    {
        return $translator->translate('Jobs');
    }

    /**
     * @param User $user
     * @return int
     * @throws MongoDBException
     * @see ListInterface::getCount()
     */
    public function getCount(User $user): int
    {
        return $this->repository->countUserJobs($user->getId());
    }

    /**
     * @see ListInterface::getItems()
     */
    public function getItems(User $user, View $view, $limit)
    {
        $items = [];

        foreach ($this->repository->getUserJobs($user->getId(), $limit) as $job)
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
