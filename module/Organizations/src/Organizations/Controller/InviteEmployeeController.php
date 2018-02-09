<?php
/**
 * YAWIK
 *
 * @filesource
 * @license    MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */

/** */
namespace Organizations\Controller;

use Organizations\Controller\Plugin\AcceptInvitationHandler;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;
use Organizations\Repository\Organization as OrganizationRepository;

/**
 * This controller is responsible for the user invitation process.
 *
 * Which consists on these tasks:
 * - Create temporary users if necessary or find the existent one for an email address.
 * - Send an invitation mail.
 * - Handles the acceptance of such an invitation.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @author Anthonius Munthi <me@itstoni.com>
 * @since  0.19
 */
class InviteEmployeeController extends AbstractActionController
{
    /**
     * @var OrganizationRepository
     */
    private $orgRepo;

	public function __construct(
        OrganizationRepository $orgRepo
    )
    {
        $this->orgRepo = $orgRepo;
    }

    /**
     * Invitation first step: Create or find user and send mail.
     *
     * @return JsonModel
     */
    public function inviteAction()
    {
        $email   = $this->params()->fromQuery('email');
        $handler = $this->plugin('Organizations/InvitationHandler');
        $result  = $handler->process($email);

        return new JsonModel($result);
    }

    /**
     * Invitation second step: Acceptance of the invitation.
     *
     * @return ViewModel
     */
    public function acceptAction()
    {
        /* @var $request \Zend\Http\PhpEnvironment\Request */
        $request = $this->getRequest();

        if ($request->isPost()) {
            return $this->createSetPasswordViewModel();
        }

        $token        = $this->params()->fromQuery('token');
        $organization = $this->params()->fromQuery('organization');

        /* @var $handler \Organizations\Controller\Plugin\AcceptInvitationHandler */
        $handler = $this->plugin('Organizations/AcceptInvitationHandler');
        $result  = $handler->process($token, $organization);

        switch ($result) {
            default:
            case AcceptInvitationHandler::OK:
                $model = $this->createSuccessViewModel();
                break;

            case AcceptInvitationHandler::OK_SET_PW:
                $model = $this->createSetPasswordViewModel();
                break;

            case AcceptInvitationHandler::ERROR_ORGANIZATION_NOT_FOUND:
                $model = $this->createErrorViewModel(
                    /*@translate*/ 'The organization referenced in your request could not be found.'
                );
                break;

            case AcceptInvitationHandler::ERROR_TOKEN_INVALID:
                $model = $this->createErrorViewModel(
                    /*@translate*/ 'The access token you provided seems to have expired.'
                );
                break;
        }

        return $model;
    }

    /**
     * Creates a view model for success view script with set password form.
     *
     * @return ViewModel
     */
    protected function createSetPasswordViewModel()
    {
        $organization = $this->getOrganizationEntity();
        $result       = $this->forward()->dispatch('Auth\Controller\Password', array('action' => 'index'));
        $model        = new ViewModel(
            array(
                'organization' => $organization->getOrganizationName()->getName()
            )
        );

        if (!$result->getVariable('valid', false)) {
            $model->setVariable('form', $result->getVariable('form'));
        }

        return $model;
    }

    /**
     * Gets the referenced organization entity.
     *
     * Retrieves the identifier from the requests' query params.
     *
     * @return \Organizations\Entity\OrganizationInterface
     */
    protected function getOrganizationEntity()
    {
        /* @var $orgRepo \Organizations\Repository\Organization */
        $orgRepo = $this->orgRepo;
        $organiationId = $this->params()->fromQuery('organization');

        $organization = $orgRepo->find($organiationId);

        return $organization;
    }

    /**
     * Creates the view model for the success view script.
     *
     * @return ViewModel
     */
    protected function createSuccessViewModel()
    {
        $organization = $this->getOrganizationEntity();

        return new ViewModel(
            array(
                'organization' => $organization->getOrganizationName()->getName()
            )
        );
    }

    /**
     * Creates a view model for the error page view script.
     *
     * Sets the response status code to 500 (indicating an internal error).
     *
     * @param string $message
     *
     * @return ViewModel
     */
    protected function createErrorViewModel($message)
    {
        /* @var $response \Zend\Http\Response */
        $response = $this->getResponse();
        $response->setStatusCode(500);

        $model = new ViewModel(array('message' => $message));
        $model->setTemplate('organizations/error/invite');

        return $model;
    }
}
