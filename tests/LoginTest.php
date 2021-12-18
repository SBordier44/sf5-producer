<?php

declare(strict_types=1);

namespace App\Tests;

use App\Entity\User;
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
                '_username' => $email,
                '_password' => 'password'
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

    public function testInvalidPassword(): void
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
                '_username' => 'producer@email.com',
                '_password' => 'fail'
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
                '_username' => 'fail@email.com',
                '_password' => 'password'
            ]
        );

        $client->submit($form);

        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $client->followRedirect();

        self::assertSelectorTextContains('div.alert-danger', "Identifiants invalides.");
    }

    /**
     * @param string $email
     * @dataProvider provideEmails
     */
    public function testInvalidCsrfTokenLogin(string $email): void
    {
        $client = static::createClient();

        /** @var RouterInterface $router */
        $router = $client->getContainer()->get("router");

        $crawler = $client->request(Request::METHOD_GET, $router->generate("security_login"));

        $form = $crawler->filter("form[name=login]")->form(
            [
                "_csrf_token" => "fail",
                "_username" => $email,
                "_password" => "password"
            ]
        );

        $client->submit($form);

        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $client->followRedirect();

        self::assertSelectorTextContains("div.alert-danger", 'Jeton CSRF invalide.');
    }

    public function testLoginFailedIfEmailIsNotVerified(): void
    {
        $client = static::createClient();

        /** @var RouterInterface $router */
        $router = $client->getContainer()->get('router');

        $crawler = $client->request(
            Request::METHOD_GET,
            $router->generate('security_login')
        );

        $user = $client
            ->getContainer()
            ->get('doctrine.orm.entity_manager')
            ->getRepository(User::class)
            ->findOneBy(['isVerified' => false]);

        $form = $crawler->filter('form[name=login]')->form(
            [
                '_username' => $user->getEmail(),
                '_password' => 'password'
            ]
        );

        $client->submit($form);

        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $client->followRedirect();

        self::assertRouteSame('security_login');

        self::assertResponseStatusCodeSame(Response::HTTP_OK);

        self::assertSelectorTextContains(
            'div.alert-danger',
            "Votre compte n'a pas été confirmé. Veuillez vérifier vos emails afin de confirmer votre compte."
        );
    }
}
