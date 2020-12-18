<?php

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

    public function testSuccessfullFarmAll(): void
    {
        $client = static::createAuthenticatedClient('customer@email.com');

        /** @var RouterInterface $router */
        $router = $client->getContainer()->get('router');

        $client->request(Request::METHOD_GET, $router->generate('farm_all'));

        self::assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testSuccessfullFarmShow(): void
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
                    'id' => $farm->getId()
                ]
            )
        );

        self::assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testSuccessfullFarmUpdate(): void
    {
        $client = static::createAuthenticatedClient('producer@email.com');

        /** @var RouterInterface $router */
        $router = $client->getContainer()->get('router');

        $crawler = $client->request(Request::METHOD_GET, $router->generate('farm_update'));

        $form = $crawler->filter('form[name=farm]')->form(
            [
                'farm[name]' => 'Exploitation',
                'farm[description]' => 'Description',
                'farm[address][address1]' => 'address',
                'farm[address][zipCode]' => '28000',
                'farm[address][city]' => 'Chartres',
                'farm[address][region]' => 'Touraine',
                'farm[address][country]' => 'France',
                'farm[address][phone]' => '0102030405',
                'farm[address][position][latitude]' => 46.5,
                'farm[address][position][longitude]' => 7.5
            ]
        );

        $client->submit($form);

        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);
    }

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

    public function provideBadRequests(): Generator
    {
        yield [
            [
                'farm[name]' => '',
                'farm[description]' => 'Description',
                'farm[address][address1]' => 'address',
                'farm[address][zipCode]' => '28000',
                'farm[address][city]' => 'Chartres',
                'farm[address][region]' => 'Touraine',
                'farm[address][country]' => 'France',
                'farm[address][phone]' => '0102030405',
                'farm[address][position][latitude]' => 46.5,
                'farm[address][position][longitude]' => 7.5
            ],
            'Cette valeur ne doit pas être vide.'
        ];
        yield [
            [
                'farm[name]' => 'Exploitation',
                'farm[description]' => '',
                'farm[address][address1]' => 'address',
                'farm[address][zipCode]' => '28000',
                'farm[address][city]' => 'Chartres',
                'farm[address][region]' => 'Touraine',
                'farm[address][country]' => 'France',
                'farm[address][phone]' => '0102030405',
                'farm[address][position][latitude]' => 46.5,
                'farm[address][position][longitude]' => 7.5
            ],
            'Cette valeur ne doit pas être vide.'
        ];
        yield [
            [
                'farm[name]' => 'Exploitation',
                'farm[description]' => 'Description',
                'farm[address][address1]' => '',
                'farm[address][zipCode]' => '28000',
                'farm[address][city]' => 'Chartres',
                'farm[address][region]' => 'Touraine',
                'farm[address][country]' => 'France',
                'farm[address][phone]' => '0102030405',
                'farm[address][position][latitude]' => 46.5,
                'farm[address][position][longitude]' => 7.5
            ],
            'Cette valeur ne doit pas être vide.'
        ];
        yield [
            [
                'farm[name]' => 'Exploitation',
                'farm[description]' => 'Description',
                'farm[address][address1]' => 'address',
                'farm[address][zipCode]' => '',
                'farm[address][city]' => 'Chartres',
                'farm[address][region]' => 'Touraine',
                'farm[address][country]' => 'France',
                'farm[address][phone]' => '0102030405',
                'farm[address][position][latitude]' => 46.5,
                'farm[address][position][longitude]' => 7.5
            ],
            'Cette valeur ne doit pas être vide.'
        ];
        yield [
            [
                'farm[name]' => 'Exploitation',
                'farm[description]' => 'Description',
                'farm[address][address1]' => 'address',
                'farm[address][zipCode]' => '28000',
                'farm[address][city]' => '',
                'farm[address][region]' => 'Touraine',
                'farm[address][country]' => 'France',
                'farm[address][phone]' => '0102030405',
                'farm[address][position][latitude]' => 46.5,
                'farm[address][position][longitude]' => 7.5
            ],
            'Cette valeur ne doit pas être vide.'
        ];
        yield [
            [
                'farm[name]' => 'Exploitation',
                'farm[description]' => 'Description',
                'farm[address][address1]' => 'address',
                'farm[address][zipCode]' => '28000',
                'farm[address][city]' => 'Chartres',
                'farm[address][region]' => '',
                'farm[address][country]' => 'France',
                'farm[address][phone]' => '0102030405',
                'farm[address][position][latitude]' => 46.5,
                'farm[address][position][longitude]' => 7.5
            ],
            'Cette valeur ne doit pas être vide.'
        ];
        yield [
            [
                'farm[name]' => 'Exploitation',
                'farm[description]' => 'Description',
                'farm[address][address1]' => 'address',
                'farm[address][zipCode]' => '28000',
                'farm[address][city]' => 'Chartres',
                'farm[address][region]' => 'Touraine',
                'farm[address][country]' => '',
                'farm[address][phone]' => '0102030405',
                'farm[address][position][latitude]' => 46.5,
                'farm[address][position][longitude]' => 7.5
            ],
            'Cette valeur ne doit pas être vide.'
        ];
        yield [
            [
                'farm[name]' => 'Exploitation',
                'farm[description]' => 'Description',
                'farm[address][address1]' => 'address',
                'farm[address][zipCode]' => '28000',
                'farm[address][city]' => 'Chartres',
                'farm[address][region]' => 'Touraine',
                'farm[address][country]' => 'France',
                'farm[address][phone]' => '',
                'farm[address][position][latitude]' => 46.5,
                'farm[address][position][longitude]' => 7.5
            ],
            'Cette valeur ne doit pas être vide.'
        ];
        yield [
            [
                'farm[name]' => 'Exploitation',
                'farm[description]' => 'Description',
                'farm[address][address1]' => 'address',
                'farm[address][zipCode]' => '28000',
                'farm[address][city]' => 'Chartres',
                'farm[address][region]' => 'Touraine',
                'farm[address][country]' => 'France',
                'farm[address][phone]' => '0102030405',
                'farm[address][position][latitude]' => '',
                'farm[address][position][longitude]' => 7.5
            ],
            'Cette valeur ne doit pas être vide.'
        ];
        yield [
            [
                'farm[name]' => 'Exploitation',
                'farm[description]' => 'Description',
                'farm[address][address1]' => 'address',
                'farm[address][zipCode]' => '28000',
                'farm[address][city]' => 'Chartres',
                'farm[address][region]' => 'Touraine',
                'farm[address][country]' => 'France',
                'farm[address][phone]' => '0102030405',
                'farm[address][position][latitude]' => 46.5,
                'farm[address][position][longitude]' => ''
            ],
            'Cette valeur ne doit pas être vide.'
        ];
        yield [
            [
                'farm[name]' => 'Exploitation',
                'farm[description]' => 'Description',
                'farm[address][address1]' => 'address',
                'farm[address][zipCode]' => 'fail',
                'farm[address][city]' => 'Chartres',
                'farm[address][region]' => 'Touraine',
                'farm[address][country]' => 'France',
                'farm[address][phone]' => '0102030405',
                'farm[address][position][latitude]' => 46.5,
                'farm[address][position][longitude]' => ''
            ],
            'Code postal invalide.'
        ];
    }
}
