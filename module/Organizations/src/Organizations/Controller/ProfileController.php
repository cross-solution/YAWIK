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

    public function __construct(
        OrganizationRepository $repo,
        JobRepository $jobRepository,
        TranslatorInterface $translator,
        ImageFileCacheManager $imageFileCacheManager
    )
    {
        $this->repo = $repo;
        $this->translator = $translator;
        $this->imageFileCacheManager = $imageFileCacheManager;
        $this->jobRepo = $jobRepository;
    }

    /**
     * List organization
     *
     * @return ViewModel
     */
    public function indexAction()
    {

        $result = $this->pagination([
            'paginator' => [
                'Organizations/Organization',
                'as' => 'organizations',
                'params' => [
                    'type' => 'profile',
                ]
            ],
            'form' => [
                'Core/Search',
                [
                    'text_name' => 'text',
                    'text_placeholder' => /*@translate*/ 'Search for organizations',
                    'button_element' => 'text',
                ],
                'as' => 'form'
            ]
        ]);
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

        if(is_null($id)){
            $this->getResponse()->setStatusCode(Response::STATUS_CODE_404);
            return [
                'message' => $translator->translate('Can not access profile page without id'),
                'exception' => new \InvalidArgumentException('Null Organization Profile Id')
            ];
        }

        $organization = $repo->find($id);
        if(!$organization instanceof Organization){
            throw new NotFoundException($id);
        }

        if(
            Organization::PROFILE_DISABLED == $organization->getProfileSetting()
            || is_null($organization->getProfileSetting())
        ){
            throw new UnauthorizedAccessException(/*@translate*/ 'This Organization Profile is disabled');
        }

        $result = $this->pagination([
            'params' => [
                'Organization_Jobs',[
                    'organization_id' => $organization->getId()
                ]
            ],
            'paginator' => ['as' => 'jobs','Organizations/ListJob'],
        ]);

        if(
            Organization::PROFILE_ACTIVE_JOBS == $organization->getProfileSetting()
        ){
            /* @var \Zend\Paginator\Paginator $paginator */
            $paginator = $result['jobs'];
            $count = $paginator->getTotalItemCount();
            if(0===$count){
                throw new UnauthorizedAccessException(/*@translate*/ 'This Organization Profile is disabled');
            }
        }
        $result['organization'] = $organization;
        $result['organizationImageCache'] = $this->imageFileCacheManager;

        return new ViewModel($result);
    }
}
