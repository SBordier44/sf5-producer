<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

class UpdateFarmTest extends WebTestCase
{
    use AuthenticationTrait;

    public function testSuccessfullUpdatedFarmInfo(): void
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
                'farm[name]' => 'NextGenExploit',
                'farm[description]' => 'Super Exploitation de nouvelle génération'
            ]
        );

        $client->submit($form);

        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);
    }
}
