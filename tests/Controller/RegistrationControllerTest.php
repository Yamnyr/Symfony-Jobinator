<?php

namespace App\Tests\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RegistrationControllerTest extends WebTestCase
{
    public function testRegistration(): void
    {
        $client = static::createClient();
        $client->request('GET', '/register');

        $client->submitForm('S\'inscrire', [
            'registration_form[email]' => 'contact@nclshart.net',
            'registration_form[plainPassword]' => 'changeme',
        ]);

        $client->followRedirect();

        $user = self::getContainer()->get('security.token_storage')
            ->getToken()
            ->getUser();

        $this->assertInstanceOf(User::class, $user);
        $this->assertResponseRedirects('/job/');

        $client->followRedirect();

        $this->assertRouteSame('app_job_public_index');
        $this->assertResponseIsSuccessful();
    }
}
