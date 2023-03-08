<?php

namespace App\Tests\Controller;

use App\Entity\User;
use App\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
    private KernelBrowser $client;

    public function setUp(): void
    {
        parent::setUp();

        $this->client = static::createClient();

        UserFactory::createOne([
            'email' => 'contact@nclshart.net',
            'password' => 'changeme',
        ]);
    }

    public function testICanLogin(): void
    {
        $this->client->request('GET', '/login');

        $this->client->submitForm('Se connecter', [
            'email' => 'contact@nclshart.net',
            'password' => 'changeme',
        ]);

        $this->client->followRedirect();

        $user = self::getContainer()->get('security.token_storage')
            ->getToken()
            ->getUser();

        $this->assertInstanceOf(User::class, $user);
    }

    public function testICanLogout(): void
    {
        $this->client->request('GET', '/login');

        $this->client->submitForm('Se connecter', [
            'email' => 'contact@nclshart.net',
            'password' => 'changeme',
        ]);

        $this->client->request('GET', '/logout');

        $token = self::getContainer()->get('security.token_storage')->getToken();

        $this->assertNull($token);
    }
}
