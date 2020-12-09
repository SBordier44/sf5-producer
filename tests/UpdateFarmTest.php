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
                'farm[description]' => 'Super Exploitation de nouvelle génération',
                'farm[address][address1]' => '25 Rue de la pelouse verte',
                'farm[address][address2]' => 'Appt 320',
                'farm[address][address3]' => '1er etage, Porte gauche',
                'farm[address][zipCode]' => '75000',
                'farm[address][city]' => 'Paris',
                'farm[address][region]' => 'Ile de France, Haut de france',
                'farm[address][country]' => 'France',
                'farm[address][phone]' => '0607080910',
                'farm[address][phone2]' => '0203040506',
                'farm[address][position][latitude]' => '48.441049',
                'farm[address][position][longitude]' => '1.546233',
            ]
        );

        $client->submit($form);

        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);
    }
}
