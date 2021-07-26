<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright https://yawik.org/COPYRIGHT.php
 * @license   MIT
 */

/** NewApplication.php */
namespace Applications\Mail;

use Applications\Entity\ApplicationInterface;
use Core\Exception\MissingDependencyException;
use Core\Mail\StringTemplateMessage;
use Laminas\Router\RouteStackInterface;

/**
 * Sends Information about a new Application to the recruiter
 *
 * Class NewApplication
 * @package Applications\Mail
 */
class NewApplication extends StringTemplateMessage
{
    /**
     * Job posting
     *
     * @var ApplicationInterface
     */
    protected $application;

    /**
     * Owner of the job posting
     *
     * @var \Auth\Entity\User $user
     */
    protected $user;

    /**
     * Organization Admin
     *
     * @var bool|\Auth\Entity\User $admin
     */
    protected $admin;

    /**
     * @var bool
     */
    private $callInitOnSetJob = false;

    /**
     * @var bool
     */
    private $callInitOnSetApplication = false;

    /**
     *
     *
     * @var RouteStackInterface
     */
    private $router;

    /**
     * @param array $options
     */
    public function __construct($options = array())
    {
        if (!is_array($options)) {
            $this->router = $options;
            $options = [];
        } elseif (isset($options['router'])) {
            $this->router = $options['router'];
            unset($options['router']);
        }

        if (!$this->router) {
            throw new MissingDependencyException('Router', $this);
        }

        parent::__construct($options);
        $this->callInitOnSetJob = true;
    }

    public function init()
    {
        if (!$this->application) {
            return false;
        }

        /* @var \Auth\Entity\Info $userInfo */
        $job = $this->application->getJob();
        $userInfo = $this->user->getInfo();
        $name = $userInfo->getDisplayName();
        if ('' == trim($name)) {
            $name = $userInfo->getEmail();
        }

        $variables = [
            'name' => $name,
            'title' => $job->getTitle(),
            'link'  => $this->router->assemble(
                            ['id' => $this->application->getId()],
                            ['name'=>'lang/applications/detail', 'force_canonical'=>true]
                       ) . '?login=' . $this->user->getLogin(),
        ];

        $this->setTo($this->user->getInfo()->getEmail(), $this->user->getInfo()->getDisplayName(false));

        $this->setVariables($variables);
        $subject = /*@translate*/ 'New application for your vacancy "%s"';

        if ($this->isTranslatorEnabled()) {
            $subject = $this->getTranslator()->translate($subject);
        }
        $this->setSubject(sprintf($subject, $job->getTitle()));

        /* @var \Applications\Entity\Settings $settings */
        $settings = $this->user->getSettings('Applications');

        $body = $settings->getMailAccessText();
        if ('' == $body) {
            $body = /*@translate*/ "Hello ##name##,\n\nThere is a new application for your vacancy:\n\"##title##\"\n\n";

            if ($this->isTranslatorEnabled()) {
                $body = $this->getTranslator()->translate($body);
            }
            $body .= "##link##\n\n";
        }

        $this->setBody($body);
        return $this;
    }

    /**
     * @param ApplicationInterface $application
     * @param bool $init
     * @return $this
     */
    public function setApplication(ApplicationInterface $application, $init = true)
    {
        $this->application = $application;
        if ($this->callInitOnSetApplication) {
            $this->init();
        }
        return $this;
    }

    /**
     * @param \Auth\Entity\User $user
     * @return $this
     */
    public function setUser($user)
    {
        $this->user=$user;
        return $this;
    }
}
