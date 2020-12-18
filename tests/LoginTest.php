<?php

namespace App\Tests;

use Generator;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

class LoginTest extends WebTestCase
{
    /**
     * @param string $email
     * @dataProvider provideEmails
     */
    public function testSuccessfullLogin(string $email): void
    {
        $client = static::createClient();

        /** @var RouterInterface $router */
        $router = $client->getContainer()->get('router');

        $crawler = $client->request(
            Request::METHOD_GET,
            $router->generate('security_login')
        );

        $form = $crawler->filter('form[name=login]')->form(
            [
                'email' => $email,
                'password' => 'password'
            ]
        );

        $client->submit($form);

        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);
    }

    public function provideEmails(): Generator
    {
        yield ['producer@email.com'];
        yield ['customer@email.com'];
    }

    public function testInvalidCredentials(): void
    {
        $client = static::createClient();

        /** @var RouterInterface $router */
        $router = $client->getContainer()->get('router');

        $crawler = $client->request(
            Request::METHOD_GET,
            $router->generate('security_login')
        );

        $form = $crawler->filter('form[name=login]')->form(
            [
                'email' => 'producer@email.com',
                'password' => 'fail'
            ]
        );

        $client->submit($form);

        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $client->followRedirect();

        self::assertSelectorTextContains('div.alert-danger', 'Identifiants invalides.');
    }

    public function testInvalidEmail(): void
    {
        $client = static::createClient();

        /** @var RouterInterface $router */
        $router = $client->getContainer()->get('router');

        $crawler = $client->request(
            Request::METHOD_GET,
            $router->generate('security_login')
        );

        $form = $crawler->filter('form[name=login]')->form(
            [
                'email' => 'fail@email.com',
                'password' => 'password'
            ]
        );

        $client->submit($form);

        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $client->followRedirect();

        self::assertSelectorTextContains('div.alert-danger', "Identifiants invalides.");
    }
}
