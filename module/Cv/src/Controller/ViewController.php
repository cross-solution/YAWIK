<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Cv\Controller;

use Cv\Repository\Cv as CvRepository;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Http\PhpEnvironment\Response;
use Zend\I18n\Translator\TranslatorInterface as Translator;

/**
 * ${CARET}
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 */
class ViewController extends AbstractActionController
{

    /**
     * @var \Cv\Repository\Cv
     */
    private $repository;
    
    /**
     * @var Translator
     */
    private $translator;

    /**
     * @param CvRepository $repository
     * @param Translator $translator
     */
    public function __construct(CvRepository $repository, Translator $translator)
    {
        $this->repository = $repository;
        $this->translator = $translator;
    }

    public function indexAction()
    {
        /** @var string|null $id */
        $id = $this->params('id');
        $resume = $this->repository->find($id);

        if (!$resume) {
            $this->getResponse()->setStatusCode(Response::STATUS_CODE_404);
            return [
                'message' => sprintf($this->translator->translate('Resume with id "%s" not found'), $id)
            ];
        }

        /* @todo REMOVE THIS
         * @codeCoverageIgnoreStart */
        if (!$resume->getDateCreated()) {
            $resume->setDateCreated();
        }
        /* @codeCoverageIgnoreEnd */

        $this->acl($resume, 'view');

        return [
            'resume' => $resume
        ];
    }
}
