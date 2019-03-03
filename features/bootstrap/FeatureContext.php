<?php

use App\Entity\User;
use Behat\Behat\Context\Context;
use Behat\Behat\Context\Environment\InitializedContextEnvironment;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behatch\Context\RestContext;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Doctrine\ORM\EntityManagerInterface;

/**
 * This context class contains the definitions of the steps used by the demo
 * feature file. Learn how to get started with Behat and BDD on Behat's website.
 *
 * @see http://behat.org/en/latest/quick_start.html
 */
class FeatureContext implements Context
{
    /**
     * @var KernelInterface
     */
    private $kernel;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var JWTTokenManagerInterface
     */
    private $jwtManager;

    /**
     * @var RestContext
     */
    private $restContext;

    /**
     * @var Response|null
     */
    private $response;

    public function __construct(KernelInterface $kernel, EntityManagerInterface $em, JWTTokenManagerInterface $jwtManager)
    {
        $this->kernel = $kernel;
        $this->em = $em;
        $this->jwtManager = $jwtManager;
    }

    /**
     * @When a demo scenario sends a request to :path
     */
    public function aDemoScenarioSendsARequestTo(string $path)
    {
        $this->response = $this->kernel->handle(Request::create($path, 'GET'));
    }

    /**
     * @Then the response should be received
     */
    public function theResponseShouldBeReceived()
    {
        if ($this->response === null) {
            throw new \RuntimeException('No response received');
        }
    }

    /**
     * @BeforeScenario @login
     *
     * @param BeforeScenarioScope $scope
     */
    public function login(BeforeScenarioScope $scope)
    {
        $user = $this->findUser('admin');
        /** @var User $user */
        $token = $this->jwtManager->create($user);
        /** @var InitializedContextEnvironment $environment */
        $environment = $scope->getEnvironment();
        $this->restContext = $environment->getContext(RestContext::class);
        $this->restContext->iAddHeaderEqualTo('Authorization', "Bearer $token");
    }

    /**
     * @param $username
     * @return User|null|object
     */
    private function findUser($username)
    {
        $repository = $this->em->getRepository(User::class);

        return $repository->findBy(['username' => $username]);
    }
}
