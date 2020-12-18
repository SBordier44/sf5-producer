<?php

namespace App\Tests;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Generator;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

class ForgottenPasswordTest extends WebTestCase
{
    /**
     * @param string $email
     * @dataProvider provideEmails
     */
    public function testSuccessfullForgottenPassword(string $email): void
    {
        $client = static::createClient();

        /** @var RouterInterface $router */
        $router = $client->getContainer()->get('router');

        /** @var EntityManagerInterface $entityManager */
        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');

        $crawler = $client->request(
            Request::METHOD_GET,
            $router->generate('security_forgotten_password')
        );

        $form = $crawler->filter('form[name=forgotten_password]')->form(
            [
                'forgotten_password[email]' => $email
            ]
        );

        $client->submit($form);

        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);

        /** @var User $user */
        $user = $entityManager->getRepository(User::class)->findOneByEmail($email);

        $crawler = $client->request(
            Request::METHOD_GET,
            $router->generate(
                'security_forgotten_password_reset',
                [
                    'token' => $user->getForgottenPassword()->getToken()
                ]
            )
        );

        $form = $crawler->filter('form[name=reset_password]')->form(
            [
                'reset_password[plainPassword]' => 'NEWpassword'
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

    /**
     * @param string $email
     * @param string $errorMessage
     * @dataProvider provideBadEmailsForFogottenPassword
     */
    public function testBadRequestForForgottenPassword(string $email, string $errorMessage): void
    {
        $client = static::createClient();

        /** @var RouterInterface $router */
        $router = $client->getContainer()->get('router');

        $crawler = $client->request(
            Request::METHOD_GET,
            $router->generate('security_forgotten_password')
        );

        $form = $crawler->filter('form[name=forgotten_password]')->form(
            [
                'forgotten_password[email]' => $email
            ]
        );

        $client->submit($form);

        self::assertResponseStatusCodeSame(Response::HTTP_OK);

        self::assertSelectorTextContains('span.form-error-message', $errorMessage);
    }

    public function provideBadEmailsForFogottenPassword(): Generator
    {
        yield ['fail@email.com', 'Cette adresse email n\'existe pas.'];
    }

    /**
     * @param string $email
     * @param string $password
     * @param string $errorMessage
     * @dataProvider provideBadRequestsForResetPassword
     */
    public function testFailedResetPassword(string $email, string $password, string $errorMessage): void
    {
        $client = static::createClient();

        /** @var RouterInterface $router */
        $router = $client->getContainer()->get('router');

        /** @var EntityManagerInterface $entityManager */
        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');

        $crawler = $client->request(
            Request::METHOD_GET,
            $router->generate('security_forgotten_password')
        );

        $form = $crawler->filter('form[name=forgotten_password]')->form(
            [
                'forgotten_password[email]' => $email
            ]
        );

        $client->submit($form);

        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);

        /** @var User $user */
        $user = $entityManager->getRepository(User::class)->findOneByEmail($email);

        $crawler = $client->request(
            Request::METHOD_GET,
            $router->generate(
                'security_forgotten_password_reset',
                [
                    'token' => $user->getForgottenPassword()->getToken()
                ]
            )
        );

        $form = $crawler->filter('form[name=reset_password]')->form(
            [
                'reset_password[plainPassword]' => $password
            ]
        );

        $client->submit($form);

        self::assertResponseStatusCodeSame(Response::HTTP_OK);

        self::assertSelectorTextContains('span.form-error-message', $errorMessage);
    }

    public function provideBadRequestsForResetPassword(): Generator
    {
        yield ['producer@email.com', '', 'Cette valeur ne doit pas être vide.'];
        yield ['producer@email.com', 'fail', 'Cette chaîne est trop courte. Elle doit avoir au minimum 8 caractères.'];
        yield ['customer@email.com', '', 'Cette valeur ne doit pas être vide.'];
        yield ['customer@email.com', 'fail', 'Cette chaîne est trop courte. Elle doit avoir au minimum 8 caractères.'];
    }

    public function testFailedResetPasswordWithBadToken(): void
    {
        $client = static::createClient();

        /** @var RouterInterface $router */
        $router = $client->getContainer()->get('router');

        $client->request(
            Request::METHOD_GET,
            $router->generate(
                'security_forgotten_password_reset',
                [
                    'token' => 'fail'
                ]
            )
        );

        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);
    }
}
