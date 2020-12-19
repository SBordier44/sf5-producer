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
     * @dataProvider provideBadRequests
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

    public function provideBadRequests(): \Generator
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
}
