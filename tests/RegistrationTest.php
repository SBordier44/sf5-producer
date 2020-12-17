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
     * @param string $role
     * @dataProvider provideRoles
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
                    'role' => $role
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
}
