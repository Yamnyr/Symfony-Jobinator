<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HomepageControllerTest extends WebTestCase
{
    public function testHomepage(): void
    {
        $client = static::createClient();
        $client->request('GET', '/');

        $this->assertResponseRedirects('/job/');

        $client->followRedirect();

        $this->assertRouteSame('app_job_public_index');
        $this->assertResponseIsSuccessful();
    }
}
