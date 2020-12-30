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
     * @param array $formData
     */
    public function testSuccessfullRegistration(string $role, array $formData): void
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

        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);
    }

    public function provideRoles(): Generator
    {
        yield [
            'producer',
            [
                'registration[email]' => 'john.doe@email.com',
                'registration[plainPassword][first]' => 'password',
                'registration[plainPassword][second]' => 'password',
                'registration[firstName]' => 'John',
                'registration[lastName]' => 'Doe',
                'registration[farm][siret]' => '34237633200082'
            ]
        ];
        yield [
            'producer',
            [
                'registration[email]' => 'john.doe@email.com',
                'registration[plainPassword][first]' => 'password',
                'registration[plainPassword][second]' => 'password',
                'registration[firstName]' => 'John',
                'registration[lastName]' => 'Doe',
                'registration[farm][siret]' => '34237633200082'
            ]
        ];
        yield [
            'customer',
            [
                'registration[email]' => 'jane.doe@email.com',
                'registration[plainPassword][first]' => 'password',
                'registration[plainPassword][second]' => 'password',
                'registration[firstName]' => 'Jane',
                'registration[lastName]' => 'Doe',
            ]
        ];
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
        yield from $this->provideProducerBadRequests();
        yield from $this->provideCustomerBadRequests();
    }

    public function provideProducerBadRequests(): Generator
    {
        yield [
            [
                'registration[email]' => 'john.doe@email.com',
                'registration[plainPassword][first]' => 'password',
                'registration[plainPassword][second]' => 'password123',
                'registration[firstName]' => 'John',
                'registration[lastName]' => 'Doe',
                'registration[farm][siret]' => '34237633200082'
            ],
            'Les mots de passe ne correspondent pas.',
            "producer"
        ];
        yield [
            [
                'registration[email]' => 'john.doe@email.com',
                'registration[plainPassword][first]' => 'password',
                'registration[plainPassword][second]' => 'password',
                'registration[firstName]' => 'John',
                'registration[lastName]' => 'Doe',
                'registration[farm][siret]' => ''
            ],
            'Cette valeur ne doit pas être vide.',
            "producer"
        ];
        yield [
            [
                'registration[email]' => '',
                'registration[plainPassword][first]' => 'password',
                'registration[plainPassword][second]' => 'password',
                'registration[firstName]' => 'John',
                'registration[lastName]' => 'Doe',
                'registration[farm][siret]' => '34237633200082'
            ],
            'Cette valeur ne doit pas être vide.',
            "producer"
        ];
        yield [
            [
                'registration[email]' => 'john.doe@email.com',
                'registration[plainPassword][first]' => '',
                'registration[plainPassword][second]' => '',
                'registration[firstName]' => 'John',
                'registration[lastName]' => 'Doe',
                'registration[farm][siret]' => '34237633200082'
            ],
            'Cette valeur ne doit pas être vide.',
            "producer"
        ];
        yield [
            [
                'registration[email]' => 'john.doe@email.com',
                'registration[plainPassword][first]' => 'password',
                'registration[plainPassword][second]' => 'password',
                'registration[firstName]' => '',
                'registration[lastName]' => 'Doe',
                'registration[farm][siret]' => '34237633200082'
            ],
            'Cette valeur ne doit pas être vide.',
            "producer"
        ];
        yield [
            [
                'registration[email]' => 'john.doe@email.com',
                'registration[plainPassword][first]' => 'password',
                'registration[plainPassword][second]' => 'password',
                'registration[firstName]' => 'John',
                'registration[lastName]' => '',
                'registration[farm][siret]' => '34237633200082'
            ],
            'Cette valeur ne doit pas être vide.',
            "producer"
        ];
        yield [
            [
                'registration[email]' => 'fail@mailfake',
                'registration[plainPassword][first]' => 'password',
                'registration[plainPassword][second]' => 'password',
                'registration[firstName]' => 'John',
                'registration[lastName]' => 'Doe',
                'registration[farm][siret]' => '34237633200082'
            ],
            'Cette valeur n\'est pas une adresse email valide.',
            "producer"
        ];
        yield [
            [
                'registration[email]' => 'john.doe@email.com',
                'registration[plainPassword][first]' => 'fail',
                'registration[plainPassword][second]' => 'fail',
                'registration[firstName]' => 'John',
                'registration[lastName]' => 'Doe',
                'registration[farm][siret]' => '34237633200082'
            ],
            'Cette chaîne est trop courte. Elle doit avoir au minimum 8 caractères.',
            "producer"
        ];
        yield [
            [
                'registration[email]' => 'customer@email.com',
                'registration[plainPassword][first]' => 'password',
                'registration[plainPassword][second]' => 'password',
                'registration[firstName]' => 'John',
                'registration[lastName]' => 'Doe',
                'registration[farm][siret]' => '34237633200082'
            ],
            'Cette adresse email est déjà utilisée.',
            "producer"
        ];
    }

    public function provideCustomerBadRequests(): Generator
    {
        yield [
            [
                'registration[email]' => 'john.doe@email.com',
                'registration[plainPassword][first]' => 'password',
                'registration[plainPassword][second]' => 'password123',
                'registration[firstName]' => 'John',
                'registration[lastName]' => 'Doe'
            ],
            'Les mots de passe ne correspondent pas.',
            "customer"
        ];
        yield [
            [
                'registration[email]' => '',
                'registration[plainPassword][first]' => 'password',
                'registration[plainPassword][second]' => 'password',
                'registration[firstName]' => 'John',
                'registration[lastName]' => 'Doe'
            ],
            'Cette valeur ne doit pas être vide.',
            "customer"
        ];
        yield [
            [
                'registration[email]' => 'john.doe@email.com',
                'registration[plainPassword][first]' => '',
                'registration[plainPassword][second]' => '',
                'registration[firstName]' => 'John',
                'registration[lastName]' => 'Doe'
            ],
            'Cette valeur ne doit pas être vide.',
            "customer"
        ];
        yield [
            [
                'registration[email]' => 'john.doe@email.com',
                'registration[plainPassword][first]' => 'password',
                'registration[plainPassword][second]' => 'password',
                'registration[firstName]' => '',
                'registration[lastName]' => 'Doe'
            ],
            'Cette valeur ne doit pas être vide.',
            "customer"
        ];
        yield [
            [
                'registration[email]' => 'john.doe@email.com',
                'registration[plainPassword][first]' => 'password',
                'registration[plainPassword][second]' => 'password',
                'registration[firstName]' => 'John',
                'registration[lastName]' => ''
            ],
            'Cette valeur ne doit pas être vide.',
            "customer"
        ];
        yield [
            [
                'registration[email]' => 'fail@mailfake',
                'registration[plainPassword][first]' => 'password',
                'registration[plainPassword][second]' => 'password',
                'registration[firstName]' => 'John',
                'registration[lastName]' => 'Doe'
            ],
            'Cette valeur n\'est pas une adresse email valide.',
            "customer"
        ];
        yield [
            [
                'registration[email]' => 'john.doe@email.com',
                'registration[plainPassword][first]' => 'fail',
                'registration[plainPassword][second]' => 'fail',
                'registration[firstName]' => 'John',
                'registration[lastName]' => 'Doe'
            ],
            'Cette chaîne est trop courte. Elle doit avoir au minimum 8 caractères.',
            "customer"
        ];
        yield [
            [
                'registration[email]' => 'customer@email.com',
                'registration[plainPassword][first]' => 'password',
                'registration[plainPassword][second]' => 'password',
                'registration[firstName]' => 'John',
                'registration[lastName]' => 'Doe'
            ],
            'Cette adresse email est déjà utilisée.',
            "customer"
        ];
    }
}
