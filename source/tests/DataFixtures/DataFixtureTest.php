<?php


namespace App\DataFixtures;


use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Console\Input\StringInput;

abstract class DataFixtureTest extends WebTestCase
{
    /** @var  Application $application */
    protected static $application;

    /** @var  Client $client */
    protected $client;

    /** @var  EntityManager $entityManager */
    protected $entityManager;

    public function setUp() {
        self::runCommand('doctrine:database:drop --force');
        self::runCommand('doctrine:database:create');
        self::runCommand('doctrine:schema:create');
        self::runCommand('doctrine:fixtures:load --append');

        $this->client = static::createClient();
        $this->entityManager = self::$container->get('doctrine.orm.entity_manager');

        parent::setUp();
    }

    protected static function runCommand($command) {
        $command = sprintf('%s --quiet', $command);
        return self::getApplication()->run(new StringInput($command));
    }

    protected static function getApplication() {
        if (null === self::$application) {
            $client = static::createClient();

            self::$application = new Application($client->getKernel());
            self::$application->setAutoExit(false);
        }

        return self::$application;
    }

    protected function tearDown() {
        self::runCommand('doctrine:database:drop --force');

        $this->entityManager->close();
        $this->entityManager = null; // avoid memory leaks
    }
}