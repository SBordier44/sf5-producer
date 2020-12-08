<?php

namespace App\Tests;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

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

        $product = $manager->getRepository(Product::class)->findOneBy([]);

        $crawler = $client->request(
            Request::METHOD_GET,
            $router->generate('product_update', ['id' => $product->getId()])
        );

        $form = $crawler->filter('form[name=product]')->form(
            [
                'product[name]' => 'Produit laitier',
                'product[description]' => 'Super Produit de ma ferme biologique',
                'product[price][unitPrice]' => 100,
                'product[price][vat]' => 2.1
            ]
        );

        $client->submit($form);

        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);
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

        $form = $crawler->filter('form[name=product]')->form(
            [
                'product[name]' => 'Produit laitier',
                'product[description]' => 'Super Produit de ma ferme biologique',
                'product[price][unitPrice]' => 100,
                'product[price][vat]' => 2.1
            ]
        );

        $client->submit($form);

        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);
    }

    public function testSuccessfullProductStockUpdate(): void
    {
        $client = static::createAuthenticatedClient('producer@email.com');

        /** @var RouterInterface $router */
        $router = $client->getContainer()->get('router');

        /** @var EntityManagerInterface $manager */
        $manager = $client->getContainer()->get('doctrine.orm.entity_manager');

        $product = $manager->getRepository(Product::class)->findOneBy([]);

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

    public function testSuccessfullProductDelete(): void
    {
        $client = static::createAuthenticatedClient('producer@email.com');

        /** @var RouterInterface $router */
        $router = $client->getContainer()->get('router');

        /** @var EntityManagerInterface $manager */
        $manager = $client->getContainer()->get('doctrine.orm.entity_manager');

        $product = $manager->getRepository(Product::class)->findOneBy([]);

        $client->request(
            Request::METHOD_GET,
            $router->generate('product_delete', ['id' => $product->getId()])
        );

        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);
    }
}
