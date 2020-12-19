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
                'reset_password[plainPassword][first]' => 'NEWpassword',
                'reset_password[plainPassword][second]' => 'NEWpassword'
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
     * @param array $formData
     * @param string $email
     * @param string $errorMessage
     * @dataProvider provideBadRequestsForResetPassword
     */
    public function testFailedResetPassword(array $formData, string $errorMessage, string $email): void
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

        $form = $crawler->filter('form[name=reset_password]')->form($formData);

        $client->submit($form);

        self::assertResponseStatusCodeSame(Response::HTTP_OK);

        self::assertSelectorTextContains('span.form-error-message', $errorMessage);
    }

    public function provideBadRequestsForResetPassword(): Generator
    {
        yield [
            [
                'reset_password[plainPassword][first]' => '',
                'reset_password[plainPassword][second]' => '',
            ],
            'Cette valeur ne doit pas être vide.',
            'producer@email.com'
        ];
        yield [
            [
                'reset_password[plainPassword][first]' => 'fail123',
                'reset_password[plainPassword][second]' => 'fail456',
            ],
            'Les mots de passe ne correspondent pas.',
            'producer@email.com'
        ];
        yield [
            [
                'reset_password[plainPassword][first]' => 'fail123',
                'reset_password[plainPassword][second]' => 'fail123',
            ],
            'Cette chaîne est trop courte. Elle doit avoir au minimum 8 caractères.',
            'producer@email.com'
        ];
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
