<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright https://yawik.org/COPYRIGHT.php
 */

/** */
namespace Jobs\View\Helper;

use Jobs\Entity\JobInterface;
use Laminas\Mvc\Router\RouteMatch;
use Laminas\View\Helper\AbstractHelper;

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
     * @var \Laminas\View\Helper\Url
     */
    private $urlHelper;

    /**
     * Creates an instance.
     *
     * @param \Laminas\View\Helper\Url $urlHelper
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
