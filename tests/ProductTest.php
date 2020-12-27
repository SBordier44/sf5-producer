<?php

namespace App\Tests;

use App\Entity\Farm;
use App\Entity\Producer;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Generator;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Uid\Uuid;

class ProductTest extends WebTestCase
{
    use AuthenticationTrait;

    public function testSuccessfullGetProductList(): void
    {
        $client = static::createAuthenticatedClient('producer@email.com');

        /** @var RouterInterface $router */
        $router = $client->getContainer()->get('router');

        $client->request(
            Request::METHOD_GET,
            $router->generate('product_index')
        );

        self::assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testSuccessfullProductUpdate(): void
    {
        $client = static::createAuthenticatedClient('producer@email.com');

        /** @var RouterInterface $router */
        $router = $client->getContainer()->get('router');

        /** @var EntityManagerInterface $manager */
        $manager = $client->getContainer()->get('doctrine.orm.entity_manager');

        $producer = $manager->getRepository(Producer::class)->findOneByEmail("producer@email.com");

        $farm = $manager->getRepository(Farm::class)->findOneByProducer($producer);

        $product = $manager->getRepository(Product::class)->getOneBy(
            [
                'farm' => $farm->getId()
            ]
        );

        $crawler = $client->request(
            Request::METHOD_GET,
            $router->generate('product_update', ['id' => $product->getId()])
        );

        $image = $this->createImage();

        $form = $crawler->filter('form[name=product]')->form(
            [
                'product[name]' => 'Produit laitier',
                'product[description]' => 'Super Produit de ma ferme biologique',
                'product[price][unitPrice]' => 100,
                'product[price][vat]' => 2.1,
                'product[image][file]' => $image
            ]
        );

        $client->submit($form);

        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);

        unlink(__DIR__ . '/../public/uploads/' . $image->getFilename());
    }

    /**
     * @dataProvider provideBadRequests
     * @param array $formData
     * @param string $errorMessage
     */
    public function testFailedProductUpdate(array $formData, string $errorMessage): void
    {
        $client = static::createAuthenticatedClient('producer@email.com');

        /** @var RouterInterface $router */
        $router = $client->getContainer()->get('router');

        /** @var EntityManagerInterface $manager */
        $manager = $client->getContainer()->get('doctrine.orm.entity_manager');

        $producer = $manager->getRepository(Producer::class)->findOneByEmail("producer@email.com");

        $farm = $manager->getRepository(Farm::class)->findOneByProducer($producer);

        $product = $manager->getRepository(Product::class)->getOneBy(
            [
                'farm' => $farm->getId()
            ]
        );

        $crawler = $client->request(
            Request::METHOD_GET,
            $router->generate('product_update', ['id' => $product->getId()])
        );

        $form = $crawler->filter('form[name=product]')->form($formData);

        $client->submit($form);

        self::assertResponseStatusCodeSame(Response::HTTP_OK);

        self::assertSelectorTextContains('span.form-error-message', $errorMessage);
    }

    public function testSuccessfullProductCreate(): void
    {
        $client = static::createAuthenticatedClient('producer@email.com');

        /** @var RouterInterface $router */
        $router = $client->getContainer()->get('router');

        $crawler = $client->request(
            Request::METHOD_GET,
            $router->generate('product_create')
        );

        $image = $this->createImage();

        $form = $crawler->filter('form[name=product]')->form(
            [
                'product[name]' => 'Produit laitier',
                'product[description]' => 'Super Produit de ma ferme biologique',
                'product[price][unitPrice]' => 100,
                'product[price][vat]' => 2.1,
                'product[image][file]' => $image
            ]
        );

        $client->submit($form);

        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);

        unlink(__DIR__ . '/../public/uploads/' . $image->getFilename());
    }

    /**
     * @param array $formData
     * @param string $errorMessage
     * @dataProvider provideBadRequests
     */
    public function testFailedProductCreate(array $formData, string $errorMessage): void
    {
        $client = static::createAuthenticatedClient('producer@email.com');

        /** @var RouterInterface $router */
        $router = $client->getContainer()->get('router');

        $crawler = $client->request(
            Request::METHOD_GET,
            $router->generate('product_create')
        );

        $form = $crawler->filter('form[name=product]')->form($formData);

        $client->submit($form);

        self::assertResponseStatusCodeSame(Response::HTTP_OK);

        self::assertSelectorTextContains('span.form-error-message', $errorMessage);
    }

    public function testSuccessfullProductStockUpdate(): void
    {
        $client = static::createAuthenticatedClient('producer@email.com');

        /** @var RouterInterface $router */
        $router = $client->getContainer()->get('router');

        /** @var EntityManagerInterface $manager */
        $manager = $client->getContainer()->get('doctrine.orm.entity_manager');

        $producer = $manager->getRepository(Producer::class)->findOneByEmail("producer@email.com");

        $farm = $manager->getRepository(Farm::class)->findOneByProducer($producer);

        $product = $manager->getRepository(Product::class)->getOneBy(
            [
                'farm' => $farm->getId()
            ]
        );

        $crawler = $client->request(
            Request::METHOD_GET,
            $router->generate('product_stock', ['id' => $product->getId()])
        );

        $form = $crawler->filter('form[name=stock]')->form(
            [
                'stock[quantity]' => 25
            ]
        );

        $client->submit($form);

        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);
    }

    public function testFailedProductStockUpdate(): void
    {
        $client = static::createAuthenticatedClient('producer@email.com');

        /** @var RouterInterface $router */
        $router = $client->getContainer()->get('router');

        /** @var EntityManagerInterface $manager */
        $manager = $client->getContainer()->get('doctrine.orm.entity_manager');

        $producer = $manager->getRepository(Producer::class)->findOneByEmail("producer@email.com");

        $farm = $manager->getRepository(Farm::class)->findOneByProducer($producer);

        $product = $manager->getRepository(Product::class)->getOneBy(
            [
                'farm' => $farm->getId()
            ]
        );

        $crawler = $client->request(
            Request::METHOD_GET,
            $router->generate('product_stock', ['id' => $product->getId()])
        );

        $form = $crawler->filter('form[name=stock]')->form(
            [
                'stock[quantity]' => -24
            ]
        );

        $client->submit($form);

        self::assertResponseStatusCodeSame(Response::HTTP_OK);

        self::assertSelectorTextContains('span.form-error-message', 'Cette valeur doit être supérieure ou égale à 0.');
    }

    public function testSuccessfullProductDelete(): void
    {
        $client = static::createAuthenticatedClient('producer@email.com');

        /** @var RouterInterface $router */
        $router = $client->getContainer()->get('router');

        /** @var EntityManagerInterface $manager */
        $manager = $client->getContainer()->get('doctrine.orm.entity_manager');

        $producer = $manager->getRepository(Producer::class)->findOneByEmail("producer@email.com");

        $farm = $manager->getRepository(Farm::class)->findOneByProducer($producer);

        $product = $manager->getRepository(Product::class)->getOneBy(
            [
                'farm' => $farm->getId()
            ]
        );

        $client->request(
            Request::METHOD_GET,
            $router->generate('product_delete', ['id' => $product->getId()])
        );

        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);
    }

    public function provideBadRequests(): Generator
    {
        yield [
            [
                'product[name]' => '',
                'product[description]' => 'Super Produit de ma ferme biologique',
                'product[price][unitPrice]' => 100,
                'product[price][vat]' => 2.1
            ],
            'Cette valeur ne doit pas être vide.'
        ];
        yield [
            [
                'product[name]' => 'Produit laitier',
                'product[description]' => '',
                'product[price][unitPrice]' => 100,
                'product[price][vat]' => 2.1
            ],
            'Cette valeur ne doit pas être vide.'
        ];
        yield [
            [
                'product[name]' => 'Produit laitier',
                'product[description]' => 'Super Produit de ma ferme biologique',
                'product[price][unitPrice]' => '',
                'product[price][vat]' => 2.1
            ],
            'Cette valeur n\'est pas valide.'
        ];
        yield [
            [
                'product[name]' => 'Produit laitier',
                'product[description]' => 'Super Produit de ma ferme biologique',
                'product[price][unitPrice]' => -10,
                'product[price][vat]' => 2.1
            ],
            'Cette valeur doit être supérieure ou égale à 0.'
        ];
    }

    public function testAccessDeniedProductCreate(): void
    {
        $client = static::createAuthenticatedClient('customer@email.com');

        /** @var RouterInterface $router */
        $router = $client->getContainer()->get('router');

        $client->request(
            Request::METHOD_GET,
            $router->generate('product_create')
        );

        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testAccessDeniedProductUpdate(): void
    {
        $client = static::createAuthenticatedClient('customer@email.com');

        /** @var RouterInterface $router */
        $router = $client->getContainer()->get('router');

        /** @var EntityManagerInterface $manager */
        $manager = $client->getContainer()->get('doctrine.orm.entity_manager');

        $product = $manager->getRepository(Product::class)->getOne();

        $client->request(
            Request::METHOD_GET,
            $router->generate(
                'product_update',
                [
                    'id' => $product->getId()
                ]
            )
        );

        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testAccessDeniedProductDelete(): void
    {
        $client = static::createAuthenticatedClient('customer@email.com');

        /** @var RouterInterface $router */
        $router = $client->getContainer()->get('router');

        /** @var EntityManagerInterface $manager */
        $manager = $client->getContainer()->get('doctrine.orm.entity_manager');

        $product = $manager->getRepository(Product::class)->getOne();

        $client->request(
            Request::METHOD_GET,
            $router->generate(
                'product_delete',
                [
                    'id' => $product->getId()
                ]
            )
        );

        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testAccessDeniedProducts(): void
    {
        $client = static::createAuthenticatedClient('customer@email.com');

        /** @var RouterInterface $router */
        $router = $client->getContainer()->get('router');

        $client->request(
            Request::METHOD_GET,
            $router->generate(
                'product_index'
            )
        );

        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testRedirectToLoginIfNotLoggedUserInProductCreateAction(): void
    {
        $client = static::createClient();

        /** @var RouterInterface $router */
        $router = $client->getContainer()->get('router');

        $client->request(
            Request::METHOD_GET,
            $router->generate('product_create')
        );

        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $client->followRedirect();

        self::assertRouteSame('security_login');
    }

    public function testRedirectToLoginIfNotLoggedUserInProductUpdateAction(): void
    {
        $client = static::createClient();

        /** @var RouterInterface $router */
        $router = $client->getContainer()->get('router');

        /** @var EntityManagerInterface $manager */
        $manager = $client->getContainer()->get('doctrine.orm.entity_manager');

        $product = $manager->getRepository(Product::class)->getOne();

        $client->request(
            Request::METHOD_GET,
            $router->generate(
                'product_update',
                [
                    'id' => $product->getId()
                ]
            )
        );

        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $client->followRedirect();

        self::assertRouteSame('security_login');
    }

    public function testRedirectToLoginIfNotLoggedUserInProductDeleteAction(): void
    {
        $client = static::createClient();

        /** @var RouterInterface $router */
        $router = $client->getContainer()->get('router');

        /** @var EntityManagerInterface $manager */
        $manager = $client->getContainer()->get('doctrine.orm.entity_manager');

        $product = $manager->getRepository(Product::class)->getOne();

        $client->request(
            Request::METHOD_GET,
            $router->generate(
                'product_delete',
                [
                    'id' => $product->getId()
                ]
            )
        );

        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $client->followRedirect();

        self::assertRouteSame('security_login');
    }

    public function testRedirectToLoginIfNotLoggedUserInProductsAction(): void
    {
        $client = static::createClient();

        /** @var RouterInterface $router */
        $router = $client->getContainer()->get('router');

        $client->request(
            Request::METHOD_GET,
            $router->generate(
                'product_index'
            )
        );

        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $client->followRedirect();

        self::assertRouteSame('security_login');
    }

    private function createImage(): UploadedFile
    {
        $filename = Uuid::v4() . '.png';
        $path = __DIR__ . '/../public/uploads/';
        copy($path . 'TF300.png', $path . $filename);
        return new UploadedFile($path . $filename, $filename, 'image/png', null, true);
    }
}
