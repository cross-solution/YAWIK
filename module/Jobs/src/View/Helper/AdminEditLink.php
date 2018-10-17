<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Jobs\View\Helper;

use Jobs\Entity\JobInterface;
use Zend\Mvc\Router\RouteMatch;
use Zend\View\Helper\AbstractHelper;

/**
 * ${CARET}
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test
 */
class AdminEditLink extends AbstractHelper
{
    /**
     * The url to return to.
     *
     * @var string
     */
    private $returnUrl;

    /**
     * Url view helper
     *
     * @var \Zend\View\Helper\Url
     */
    private $urlHelper;

    /**
     * Creates an instance.
     *
     * @param \Zend\View\Helper\Url $urlHelper
     * @param string $returnUrl
     */
    public function __construct($urlHelper, $returnUrl)
    {
        $this->urlHelper = $urlHelper;
        $this->returnUrl = urlencode($returnUrl);
    }

    /**
     * Assembles an admin edit link.
     *
     * @param JobInterface $job
     *
     * @return string
     */
    public function __invoke(JobInterface $job)
    {
        return $this->urlHelper->__invoke(
            'lang/jobs/manage',
            ['action' => 'edit'],
            ['query' => [
                'id' => $job->getId(),
                'admin' => 1,
            ]],
            true
        ) . '&return=' . $this->returnUrl;
    }
}
