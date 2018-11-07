<?php

/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */

namespace Organizations\Controller;

use Auth\Exception\UnauthorizedAccessException;
use Core\Entity\Exception\NotFoundException;
use Jobs\Repository\Job as JobRepository;
use Organizations\Entity\Organization;
use Organizations\Repository\Organization as OrganizationRepository;
use Zend\Http\Response;
use Zend\I18n\Translator\TranslatorInterface;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Organizations\ImageFileCache\Manager as ImageFileCacheManager;

/**
 * Class ProfileController
 *
 * @author Anthonius Munthi <me@itstoni.com>
 * @package Organizations\Controller
 * @since 0.30.1
 */
class ProfileController extends AbstractActionController
{
    /**
     * @var OrganizationRepository
     */
    private $repo;

    /**
     * @var JobRepository
     */
    private $jobRepo;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var ImageFileCacheManager
     */
    private $imageFileCacheManager;

    /**
     * @var array
     */
    private $options = [
        'count' => 10,
    ];

    public function __construct(
        OrganizationRepository $repo,
        JobRepository $jobRepository,
        TranslatorInterface $translator,
        ImageFileCacheManager $imageFileCacheManager,
        $options
    ) {
        $this->repo = $repo;
        $this->translator = $translator;
        $this->imageFileCacheManager = $imageFileCacheManager;
        $this->jobRepo = $jobRepository;
        $this->options = $options;
    }

    /**
     * List organization
     *
     * @return ViewModel
     */
    public function indexAction()
    {
        $result = $this->pagination([
            'params' => ['Organizations_Profile',[
                    'q',
                    'count' => $this->options['count'],
                    'page' => 1,
                ]
            ],
            'paginator' => [
                'Organizations/Organization',
                'as' => 'organizations',
                'params' => [
                    'type' => 'profile',
                ]
            ],
            'form' => [
                'Core/Search',
                'as' => 'form',
            ]
        ]);

        $organizationImageCache = $this->imageFileCacheManager;
        $result['organizationImageCache'] = $organizationImageCache;

        return new ViewModel($result);
    }

    /**
     * @return array|ViewModel
     */
    public function detailAction()
    {
        $translator      = $this->translator;
        $repo            = $this->repo;
        $id              = $this->params('id');

        if (is_null($id)) {
            $this->getResponse()->setStatusCode(Response::STATUS_CODE_404);
            return [
                'message' => $translator->translate('Can not access profile page without id'),
                'exception' => new \InvalidArgumentException('Null Organization Profile Id')
            ];
        }

        $organization = $repo->find($id);
        if (!$organization instanceof Organization) {
            throw new NotFoundException($id);
        }

        if (
            Organization::PROFILE_DISABLED == $organization->getProfileSetting()
            || is_null($organization->getProfileSetting())
        ) {
            return $this->disabledProfileViewModel($organization);
        }

        $result = $this->pagination([
            'params' => [
                'Organization_Jobs',[
                    'q',
                    'organization_id' => $id,
                    'count' => $this->options['count'],
                    'page' => 1,
                ],
            ],
            'paginator' => [
                'as' => 'jobs',
                'Organizations/ListJob',
            ],
        ]);

        if (
            Organization::PROFILE_ACTIVE_JOBS == $organization->getProfileSetting()
        ) {
            /* @var \Zend\Paginator\Paginator $paginator */
            $paginator = $result['jobs'];
            $count = $paginator->getTotalItemCount();
            if (0===$count) {
                return $this->disabledProfileViewModel($organization);
            }
        }
        $result['organization'] = $organization;
        $result['organizationImageCache'] = $this->imageFileCacheManager;

        /* @var \Zend\Mvc\Controller\Plugin\Url $url */
        $result['paginationControlRoute'] = 'lang/organizations/profileDetail';
        return new ViewModel($result);
    }

    private function disabledProfileViewModel($organization)
    {
        $model = new ViewModel([
            'organizationImageCache' => $this->imageFileCacheManager,
            'organization' => $organization,
        ]);
        $model->setTemplate('organizations/profile/disabled');

        return $model;
    }
}
