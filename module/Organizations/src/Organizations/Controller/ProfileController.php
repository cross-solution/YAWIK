<?php

/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */

namespace Organizations\Controller;


use Core\Entity\Exception\NotFoundException;
use Interop\Container\ContainerInterface;
use Organizations\Entity\Organization;
use Organizations\Exception\MissingParentOrganizationException;
use Organizations\Repository\Organization as OrganizationRepository;
use Zend\Http\Response;
use Zend\I18n\Translator\TranslatorInterface;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

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
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(
        OrganizationRepository $repo,
        TranslatorInterface $translator
    )
    {
        $this->repo = $repo;
        $this->translator = $translator;
    }

    public function indexAction()
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

        $result = $this->pagination([
            'params' => [
                'Organization_Jobs',[
                    'organization_id' => $organization->getId()
                ]
            ],
            'paginator' => ['as' => 'jobs','Organizations/ListJob'],
        ]);

        $result['organization'] = $organization;

        return new ViewModel($result);
    }

    /**
     * @param ContainerInterface $container
     * @return ProfileController
     */
    static public function factory(ContainerInterface $container)
    {
        $repo = $container->get('repositories')
            ->get('Organizations/Organization')
        ;
        $translator = $container->get('translator');

        return new static($repo,$translator);
    }
}
