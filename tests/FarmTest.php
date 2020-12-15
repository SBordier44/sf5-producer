<?php

namespace App\Tests;

use App\Entity\Farm;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

class FarmTest extends WebTestCase
{
    use AuthenticationTrait;

    public function testSuccessfullFarmAll(): void
    {
        $client = static::createAuthenticatedClient('producer@email.com');

        /** @var RouterInterface $router */
        $router = $client->getContainer()->get('router');

        $client->request(Request::METHOD_GET, $router->generate('farm_all'));

        self::assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testSuccessfullFarmShow(): void
    {
        $client = static::createAuthenticatedClient('producer@email.com');

        /** @var RouterInterface $router */
        $router = $client->getContainer()->get('router');

        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');
        $farm = $entityManager->getRepository(Farm::class)->findOneBy([]);

        $client->request(
            Request::METHOD_GET,
            $router->generate(
                'farm_show',
                [
                    'id' => $farm->getId()
                ]
            )
        );

        self::assertResponseStatusCodeSame(Response::HTTP_OK);
    }
}
