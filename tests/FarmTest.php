<?php

declare(strict_types=1);

namespace App\Tests;

use App\Entity\Farm;
use Generator;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

class FarmTest extends WebTestCase
{
    use AuthenticationTrait;

    public function testSuccessfullFarmAllIfConnectedUser(): void
    {
        $client = static::createAuthenticatedClient('customer@email.com');

        /** @var RouterInterface $router */
        $router = $client->getContainer()->get('router');

        $client->request(Request::METHOD_GET, $router->generate('farm_all'));

        self::assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testFailedFarmAllIfNotConnectedUser(): void
    {
        $client = static::createClient();

        /** @var RouterInterface $router */
        $router = $client->getContainer()->get('router');

        $client->request(Request::METHOD_GET, $router->generate('farm_all'));

        self::assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testSuccessfullFarmShowIfConnectedUser(): void
    {
        $client = static::createAuthenticatedClient('customer@email.com');

        /** @var RouterInterface $router */
        $router = $client->getContainer()->get('router');

        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');

        $farm = $entityManager->getRepository(Farm::class)->findOneBy([]);

        $client->request(
            Request::METHOD_GET,
            $router->generate(
                'farm_show',
                [
                    'slug' => $farm->getSlug()
                ]
            )
        );

        self::assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testSuccessfullFarmShowIfNotConnectedUser(): void
    {
        $client = static::createClient();

        /** @var RouterInterface $router */
        $router = $client->getContainer()->get('router');

        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');

        $farm = $entityManager->getRepository(Farm::class)->findOneBy([]);

        $client->request(
            Request::METHOD_GET,
            $router->generate(
                'farm_show',
                [
                    'slug' => $farm->getSlug()
                ]
            )
        );

        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $client->followRedirect();

        self::assertResponseStatusCodeSame(Response::HTTP_OK);

        self::assertRouteSame('security_login');
    }

    public function testSuccessfullUpdatedFarmInfoForProducer(): void
    {
        $client = static::createAuthenticatedClient('producer@email.com');

        /** @var RouterInterface $router */
        $router = $client->getContainer()->get('router');

        $crawler = $client->request(
            Request::METHOD_GET,
            $router->generate('farm_update')
        );

        $form = $crawler->filter('form[name=farm]')->form(
            [
                'farm[description]' => 'Super Exploitation de nouvelle génération',
                'farm[address][phone]' => '0607080910'
            ]
        );

        $client->submit($form);

        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $client->followRedirect();

        self::assertRouteSame('farm_update');

        self::assertResponseStatusCodeSame(Response::HTTP_OK);

        self::assertSelectorTextContains(
            'div.alert.alert-success',
            'Les informations de votre exploitation ont étés modifiées avec succès.'
        );
    }

    public function testFailedGetFarmInfoIfUserIsNotLogged(): void
    {
        $client = static::createClient();

        /** @var RouterInterface $router */
        $router = $client->getContainer()->get('router');

        $client->request(
            Request::METHOD_GET,
            $router->generate('farm_update')
        );

        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $client->followRedirect();

        self::assertRouteSame('security_login');
    }

    public function testFailedGetFarmInfoIfUserIsACustomer(): void
    {
        $client = static::createAuthenticatedClient('customer@email.com');

        /** @var RouterInterface $router */
        $router = $client->getContainer()->get('router');

        $client->request(
            Request::METHOD_GET,
            $router->generate('farm_update')
        );

        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function provideBadRequests(): Generator
    {
        yield [
            [
                'farm[description]' => 'Description',
                'farm[address][phone]' => '',
            ],
            'Cette valeur ne doit pas être vide.'
        ];
        yield [
            [
                'farm[description]' => '',
                'farm[address][phone]' => '0102030405'
            ],
            'Cette valeur ne doit pas être vide.'
        ];
    }
}
