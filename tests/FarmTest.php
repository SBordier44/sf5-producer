<?php

namespace App\Tests;

use App\Entity\Farm;
use Generator;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Uid\Uuid;

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
                'farm[name]' => 'NextGenExploit Modified',
                'farm[description]' => 'Super Exploitation de nouvelle génération',
                'farm[address][address]' => '25 Rue de la pelouse verte',
                'farm[address][addressExtra]' => '',
                'farm[address][zipCode]' => '75000',
                'farm[address][city]' => 'Paris',
                'farm[address][region]' => 'Ile de France, Haut de france',
                'farm[address][country]' => 'France',
                'farm[address][phone]' => '0607080910',
                'farm[address][position][latitude]' => '48.441049',
                'farm[address][position][longitude]' => '1.546233',
                'farm[image][file]' => $this->createImage()
            ]
        );

        $client->submit($form);

        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);
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
                'farm[name]' => '',
                'farm[description]' => 'Description',
                'farm[address][address]' => 'address',
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
                'farm[address][address]' => 'address',
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
                'farm[address][address]' => '',
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
                'farm[address][address]' => 'address',
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
                'farm[address][address]' => 'address',
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
                'farm[address][address]' => 'address',
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
                'farm[address][address]' => 'address',
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
                'farm[address][address]' => 'address',
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
                'farm[address][address]' => 'address',
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
                'farm[address][address]' => 'address',
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
                'farm[address][address]' => 'address',
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

    private function createImage(): UploadedFile
    {
        $filename = Uuid::v4() . '.png';
        $path = __DIR__ . '/../public/uploads/';
        copy($path . 'TF300.png', $path . $filename);
        return new UploadedFile($path . $filename, $filename, 'image/png', null, true);
    }

    public static function tearDownAfterClass(): void
    {
        $finder = new Finder();
        $finder->files()->in(__DIR__ . '/../public/uploads');
        if ($finder->hasResults()) {
            foreach ($finder->files() as $file) {
                $filename = $file->getFilename();
                if ($filename !== 'TF300.png' && $filename !== '.gitignore') {
                    unlink($file->getPath() . '/' . $filename);
                }
            }
        }
    }
}
