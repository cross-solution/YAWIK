<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Install\Controller\Plugin;

use Auth\Entity\Filter\CredentialFilter;
use Auth\Entity\Info;
use Auth\Entity\User;
use Auth\Repository\User as UserRepository;
use Doctrine\ODM\MongoDB\DocumentManager;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;

/**
 * Helper for initial user creation.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @author Anthonius Munthi <me@itstoni.com>
 *
 * @since 0.20
 */
class UserCreator extends AbstractPlugin
{

    /**
     * @var \Auth\Entity\Filter\CredentialFilter
     */
    protected $credentialFilter;

    /**
     * @var DocumentManager
     */
    protected $documentManager;

    /**
     * UserCreator constructor.
     *
     * @param CredentialFilter $credentialFilter
     * @param DocumentManager $documentManager
     */
    public function __construct(CredentialFilter $credentialFilter, DocumentManager $documentManager)
    {
        $this->credentialFilter = $credentialFilter;
        $this->documentManager = $documentManager;
    }

    /**
     * Inserts a minimalistic user document into the database.
     *
     * @param   string $username Login name
     * @param   string $password Credential
     * @param   string $email Email
     * @throws  \Exception when fail store user entity
     *
     * @return bool
     */
    public function process($username, $password, $email)
    {
        $dm = $this->documentManager;

        /* @var UserRepository $repo */
        $repo = $dm->getRepository(User::class);
        $credential = $this->credentialFilter->filter($password);
        $info = new Info();
        $info->setEmail($email);
        $user = new User();
        $user
            ->setIsDraft(false)
            ->setRole('admin')
            ->setLogin($username)
            ->setCredential($credential)
            ->setInfo($info)
        ;
        $repo->setEntityPrototype(new User());

        $result = true;
        try{
            $repo->store($user);
        }catch (\Exception $e){
            throw $e;
        }
        return $result;
    }
}
