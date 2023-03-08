<?php

namespace App\Tests\Controller;

use App\Factory\JobFactory;
use App\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class JobControllerTest extends WebTestCase
{
    public function testListPublicJobs(): void
    {
        $client = static::createClient();

        $user = UserFactory::createOne();
        $client->loginUser($user->object());

        $client->request('GET', '/job/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('body', 'Aucune offre publiée pour le moment.');

        JobFactory::createOne([
            'title' => 'Développeur Symfony',
            'published' => true,
            'owner' => $user,
        ]);
        JobFactory::createOne([
            'title' => 'Développeur Laravel',
            'published' => false,
            'owner' => $user,
        ]);

        $client->request('GET', '/job/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('body', 'Développeur Symfony');
        $this->assertSelectorTextNotContains('body', 'Développeur Laravel');
    }

    public function testListMyJobs(): void
    {
        $client = static::createClient();

        $user = UserFactory::createOne();
        $client->loginUser($user->object());

        $client->request('GET', '/job/my');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('body', 'Aucune offre d\'emploi');

        JobFactory::createOne([
            'title' => 'Développeur Symfony',
            'published' => false,
            'owner' => $user,
        ]);
        JobFactory::createOne([
            'title' => 'Développeur Laravel',
            'published' => false,
        ]);

        $client->request('GET', '/job/my');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('body', 'Développeur Symfony');
        $this->assertSelectorTextNotContains('body', 'Développeur Laravel');
    }

    public function testShowPublicJob(): void
    {
        $client = static::createClient();

        $publishedJob = JobFactory::createOne([
            'title' => 'Développeur Symfony',
            'published' => true,
        ]);
        $notPublishedJob = JobFactory::createOne([
            'title' => 'Développeur Laravel',
            'published' => false,
        ]);

        $client->request('GET', sprintf('/job/%s', $publishedJob->getId()));

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('body', 'Développeur Symfony');

        $user = UserFactory::createOne();
        $client->loginUser($user->object());

        $client->request('GET', sprintf('/job/%s', $notPublishedJob->getId()));

        $this->assertResponseStatusCodeSame(403);
    }

    public function testCreateJob(): void
    {
        $client = static::createClient();

        $user = UserFactory::createOne();
        $client->loginUser($user->object());

        $client->request('GET', '/job/new');

        $client->submitForm('Créer', [
            'job[title]' => 'Développeur Symfony',
            'job[published]' => true,
            'job[description]' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. In in orci quam. Duis in nisl vitae lorem ac.',
        ]);
        $client->followRedirect();

        $this->assertResponseIsSuccessful();
        $this->assertRouteSame('app_job_my_index');
        $this->assertSelectorTextNotContains('body', 'Aucune offre d\'emploi');
        $this->assertSelectorTextContains('body', 'Développeur Symfony');
    }

    public function testEditMyJob(): void
    {
        $client = static::createClient();

        $user = UserFactory::createOne();
        $client->loginUser($user->object());

        $job = JobFactory::createOne([
            'title' => 'Développeur Symfony',
            'published' => false,
            'owner' => $user,
        ]);

        $client->request('GET', sprintf('/job/%s/edit', $job->getId()));

        $client->submitForm('Modifier', [
            'job[title]' => 'Développeur Laravel',
        ]);
        $client->followRedirect();

        $this->assertResponseIsSuccessful();
        $this->assertRouteSame('app_job_my_index');
        $this->assertSelectorTextNotContains('body', 'Développeur Symfony');
        $this->assertSelectorTextContains('body', 'Développeur Laravel');
    }

    public function testEditJobOfAnotherUser(): void
    {
        $client = static::createClient();

        $user = UserFactory::createOne();
        $client->loginUser($user->object());

        $job = JobFactory::createOne([
            'title' => 'Développeur Symfony',
            'published' => false,
        ]);

        $client->request('GET', sprintf('/job/%s/edit', $job->getId()));

        $this->assertResponseStatusCodeSame(403);
    }

    public function testDeleteMyJob(): void
    {
        $client = static::createClient();

        $user = UserFactory::createOne();
        $client->loginUser($user->object());

        JobFactory::createOne([
            'title' => 'Développeur Symfony',
            'published' => false,
            'owner' => $user,
        ]);

        $client->request('GET', '/job/my');

        $client->submitForm('Supprimer');
        $client->followRedirect();

        $this->assertResponseIsSuccessful();
        $this->assertRouteSame('app_job_my_index');
        $this->assertSelectorTextNotContains('body', 'Développeur Symfony');
        $this->assertSelectorTextContains('body', 'Aucune offre d\'emploi');
    }
}
