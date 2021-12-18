<?php

declare(strict_types=1);

namespace App\Tests;

use Generator;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

class CustomerRegistrationTest extends WebTestCase
{
    public function testSuccessfullRegistration(): void
    {
        $client = static::createClient();

        /** @var RouterInterface $router */
        $router = $client->getContainer()->get('router');

        $crawler = $client->request(Request::METHOD_GET, $router->generate('security_register'));

        $form = $crawler->filter('form[name=registration_form]')->form(
            [
                'registration_form[email]' => 'jane.doe@email.com',
                'registration_form[plainPassword][first]' => 'superPASS@123',
                'registration_form[plainPassword][second]' => 'superPASS@123',
                'registration_form[firstName]' => 'Jane',
                'registration_form[lastName]' => 'Doe'
            ]
        );

        $client->submit($form);

        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $client->followRedirect();

        self::assertRouteSame('security_login');

        self::assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    /**
     * @dataProvider provideBadRequests
     */
    public function testBadRequest(array $formData, array $errorMessages): void
    {
        $client = static::createClient();

        /** @var RouterInterface $router */
        $router = $client->getContainer()->get('router');

        $crawler = $client->request(Request::METHOD_GET, $router->generate('security_register'));

        $form = $crawler->filter('form[name=registration_form]')->form($formData);

        $client->submit($form);

        self::assertResponseStatusCodeSame(Response::HTTP_OK);

        foreach ($errorMessages as $errorMessage) {
            self::assertSelectorTextContains('span.invalid-feedback', $errorMessage);
        }
    }

    public function provideBadRequests(): Generator
    {
        yield [
            [
                'registration_form[email]' => 'jane.doe@email.com',
                'registration_form[plainPassword][first]' => 'password',
                'registration_form[plainPassword][second]' => 'password123',
                'registration_form[firstName]' => 'Jane',
                'registration_form[lastName]' => 'Doe'
            ],
            ['Les mots de passe ne correspondent pas.']
        ];
        yield [
            [
                'registration_form[email]' => 'jane.doe@email.com',
                'registration_form[plainPassword][first]' => 'pass',
                'registration_form[plainPassword][second]' => 'pass',
                'registration_form[firstName]' => 'Jane',
                'registration_form[lastName]' => 'Doe'
            ],
            [
                'Le mot de passe doit faire au moins 8 caractères.',
                'Le mot de passe doit contenir des majuscules et des minuscules.',
                'Le mot de passe doit contenir au moins un chiffre.',
                'Le mot de passe doit contenir au moins un caractère spécial.'
            ]
        ];
        yield [
            [
                'registration_form[email]' => '',
                'registration_form[plainPassword][first]' => 'superPASS@123',
                'registration_form[plainPassword][second]' => 'superPASS@123',
                'registration_form[firstName]' => 'John',
                'registration_form[lastName]' => 'Doe'
            ],
            ['Cette valeur ne doit pas être vide.']
        ];
        yield [
            [
                'registration_form[email]' => 'fail@mailfake',
                'registration_form[plainPassword][first]' => 'superPASS@123',
                'registration_form[plainPassword][second]' => 'superPASS@123',
                'registration_form[firstName]' => 'John',
                'registration_form[lastName]' => 'Doe'
            ],
            ['Cette valeur n\'est pas une adresse email valide.']
        ];
        yield [
            [
                'registration_form[email]' => 'jane.doe@email.com',
                'registration_form[plainPassword][first]' => 'superPASS@123',
                'registration_form[plainPassword][second]' => 'superPASS@123',
                'registration_form[firstName]' => '',
                'registration_form[lastName]' => 'Doe'
            ],
            ['Cette valeur ne doit pas être vide.']
        ];
        yield [
            [
                'registration_form[email]' => 'jane.doe@email.com',
                'registration_form[plainPassword][first]' => 'superPASS@123',
                'registration_form[plainPassword][second]' => 'superPASS@123',
                'registration_form[firstName]' => 'Jane',
                'registration_form[lastName]' => ''
            ],
            ['Cette valeur ne doit pas être vide.']
        ];
    }
}
