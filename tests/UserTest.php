<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

class UserTest extends WebTestCase
{
    use AuthenticationTrait;

    public function testSuccessfullEditPassword(): void
    {
        $client = static::createAuthenticatedClient('customer@email.com');

        /** @var RouterInterface $router */
        $router = $client->getContainer()->get('router');

        $crawler = $client->request(Request::METHOD_GET, $router->generate('user_edit_password'));

        self::assertResponseStatusCodeSame(Response::HTTP_OK);

        $form = $crawler->filter('form[name=user_password]')->form(
            [
                'user_password[currentPassword]' => 'password',
                'user_password[plainPassword][first]' => 'password1234',
                'user_password[plainPassword][second]' => 'password1234'
            ]
        );

        $client->submit($form);

        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);
    }

    /**
     * @dataProvider provideBadRequestsPasswordEdit
     * @param array $formData
     * @param string $errorMessage
     */
    public function testBadRequestEditPassword(array $formData, string $errorMessage): void
    {
        $client = static::createAuthenticatedClient('customer@email.com');

        /** @var RouterInterface $router */
        $router = $client->getContainer()->get('router');

        $crawler = $client->request(Request::METHOD_GET, $router->generate('user_edit_password'));

        $form = $crawler->filter('form[name=user_password]')->form($formData);

        $client->submit($form);

        self::assertResponseStatusCodeSame(Response::HTTP_OK);

        self::assertSelectorTextContains('span.form-error-message', $errorMessage);
    }

    public function testRedirectToLoginIfUserIsNotLoggedForPasswordEdit(): void
    {
        $client = static::createClient();

        /** @var RouterInterface $router */
        $router = $client->getContainer()->get('router');

        $client->request(Request::METHOD_GET, $router->generate('user_edit_password'));

        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $client->followRedirect();

        self::assertRouteSame('security_login');
    }

    public function provideBadRequestsPasswordEdit(): \Generator
    {
        yield [
            [
                'user_password[currentPassword]' => 'fail',
                'user_password[plainPassword][first]' => 'password1234',
                'user_password[plainPassword][second]' => 'password1234'
            ],
            'Le mot de passe saisi n\'est pas celui utilisé actuellement.'
        ];
        yield [
            [
                'user_password[currentPassword]' => 'password',
                'user_password[plainPassword][first]' => 'password1234',
                'user_password[plainPassword][second]' => 'fail'
            ],
            'Les mots de passe ne correspondent pas.'
        ];
        yield [
            [
                'user_password[currentPassword]' => 'password',
                'user_password[plainPassword][first]' => 'fail',
                'user_password[plainPassword][second]' => 'password1234'
            ],
            'Les mots de passe ne correspondent pas.'
        ];
        yield [
            [
                'user_password[currentPassword]' => '',
                'user_password[plainPassword][first]' => 'fail',
                'user_password[plainPassword][second]' => 'password1234'
            ],
            'Le mot de passe saisi n\'est pas celui utilisé actuellement.'
        ];
    }

    public function testSuccessfullEditInfo(): void
    {
        $client = static::createAuthenticatedClient('customer@email.com');

        /** @var RouterInterface $router */
        $router = $client->getContainer()->get('router');

        $crawler = $client->request(Request::METHOD_GET, $router->generate('user_edit_info'));

        self::assertResponseStatusCodeSame(Response::HTTP_OK);

        $form = $crawler->filter('form[name=user_info]')->form(
            [
                'user_info[email]' => 'john.doe@email.com',
                'user_info[firstName]' => 'John',
                'user_info[lastName]' => 'Doe'
            ]
        );

        $client->submit($form);

        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);
    }

    /**
     * @dataProvider provideBadRequestsUserInfo
     * @param array $formData
     * @param string $errorMessage
     */
    public function testBadRequestEditInfo(array $formData, string $errorMessage): void
    {
        $client = static::createAuthenticatedClient('customer@email.com');

        /** @var RouterInterface $router */
        $router = $client->getContainer()->get('router');

        $crawler = $client->request(Request::METHOD_GET, $router->generate('user_edit_info'));

        $form = $crawler->filter('form[name=user_info]')->form($formData);

        $client->submit($form);

        self::assertResponseStatusCodeSame(Response::HTTP_OK);

        self::assertSelectorTextContains('span.form-error-message', $errorMessage);
    }

    public function testRedirectToLoginIfUserIsNotLoggedForUserInfoEdit(): void
    {
        $client = static::createClient();

        /** @var RouterInterface $router */
        $router = $client->getContainer()->get('router');

        $client->request(Request::METHOD_GET, $router->generate('user_edit_info'));

        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $client->followRedirect();

        self::assertRouteSame('security_login');
    }

    public function provideBadRequestsUserInfo(): \Generator
    {
        yield [
            [
                'user_info[email]' => '',
                'user_info[firstName]' => 'John',
                'user_info[lastName]' => 'Doe'
            ],
            'Cette valeur ne doit pas être vide.'
        ];
        yield [
            [
                'user_info[email]' => 'john.doe@email.com',
                'user_info[firstName]' => '',
                'user_info[lastName]' => 'Doe'
            ],
            'Cette valeur ne doit pas être vide.'
        ];
        yield [
            [
                'user_info[email]' => 'john.doe@email.com',
                'user_info[firstName]' => 'John',
                'user_info[lastName]' => ''
            ],
            'Cette valeur ne doit pas être vide.'
        ];
        yield [
            [
                'user_info[email]' => 'invalid@Mail',
                'user_info[firstName]' => 'John',
                'user_info[lastName]' => 'Doe'
            ],
            'Cette valeur n\'est pas une adresse email valide.'
        ];
    }
}
