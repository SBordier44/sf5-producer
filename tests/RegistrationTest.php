<?php

namespace App\Tests;

use Generator;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

class RegistrationTest extends WebTestCase
{
    /**
     * @dataProvider provideRoles
     * @param string $role
     */
    public function testSuccessfullRegistration(string $role): void
    {
        $client = static::createClient();

        /** @var RouterInterface $router */
        $router = $client->getContainer()->get('router');

        $crawler = $client->request(
            Request::METHOD_GET,
            $router->generate(
                'security_registration',
                [
                    'role' => $role,
                ]
            )
        );

        $form = $crawler->filter('form[name=registration]')->form(
            [
                'registration[email]' => 'john.doe@email.com',
                'registration[plainPassword]' => 'password',
                'registration[firstName]' => 'John',
                'registration[lastName]' => 'Doe',
            ]
        );

        $client->submit($form);

        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);
    }

    public function provideRoles(): Generator
    {
        yield ['producer'];
        yield ['customer'];
    }

    /**
     * @dataProvider provideBadRequests
     * @param array $formData
     * @param string $errorMessage
     * @param string $role
     */
    public function testBadRequest(array $formData, string $errorMessage, string $role): void
    {
        $client = static::createClient();

        /** @var RouterInterface $router */
        $router = $client->getContainer()->get('router');

        $crawler = $client->request(
            Request::METHOD_GET,
            $router->generate(
                'security_registration',
                [
                    'role' => $role,
                ]
            )
        );

        $form = $crawler->filter('form[name=registration]')->form($formData);

        $client->submit($form);

        self::assertResponseStatusCodeSame(Response::HTTP_OK);

        self::assertSelectorTextContains('span.form-error-message', $errorMessage);
    }

    public function provideBadRequests(): Generator
    {
        foreach (['customer', 'producer'] as $role) {
            yield [
                [
                    'registration[email]' => '',
                    'registration[plainPassword]' => 'password',
                    'registration[firstName]' => 'John',
                    'registration[lastName]' => 'Doe',
                ],
                'Cette valeur ne doit pas être vide.',
                $role,
            ];
            yield [
                [
                    'registration[email]' => 'john.doe@email.com',
                    'registration[plainPassword]' => '',
                    'registration[firstName]' => 'John',
                    'registration[lastName]' => 'Doe',
                ],
                'Cette valeur ne doit pas être vide.',
                $role,
            ];
            yield [
                [
                    'registration[email]' => 'john.doe@email.com',
                    'registration[plainPassword]' => 'password',
                    'registration[firstName]' => '',
                    'registration[lastName]' => 'Doe',
                ],
                'Cette valeur ne doit pas être vide.',
                $role,
            ];
            yield [
                [
                    'registration[email]' => 'john.doe@email.com',
                    'registration[plainPassword]' => 'password',
                    'registration[firstName]' => 'John',
                    'registration[lastName]' => '',
                ],
                'Cette valeur ne doit pas être vide.',
                $role,
            ];
            yield [
                [
                    'registration[email]' => 'fail@mailfake',
                    'registration[plainPassword]' => 'password',
                    'registration[firstName]' => 'John',
                    'registration[lastName]' => 'Doe',
                ],
                'Cette valeur n\'est pas une adresse email valide.',
                $role,
            ];
            yield [
                [
                    'registration[email]' => 'john.doe@email.com',
                    'registration[plainPassword]' => 'fail',
                    'registration[firstName]' => 'John',
                    'registration[lastName]' => 'Doe',
                ],
                'Cette chaîne est trop courte. Elle doit avoir au minimum 8 caractères.',
                $role,
            ];
            yield [
                [
                    'registration[email]' => 'customer@email.com',
                    'registration[plainPassword]' => 'password',
                    'registration[firstName]' => 'John',
                    'registration[lastName]' => 'Doe',
                ],
                'Cette adresse email est déjà utilisée.',
                $role,
            ];
        }
    }
}
