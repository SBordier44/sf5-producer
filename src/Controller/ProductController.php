<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Producer;
use App\Entity\Product;
use App\Form\ProductType;
use App\Handler\CreateProductHandler;
use App\Handler\ProductStockUpdateHandler;
use App\HandlerFactory\HandlerFactoryInterface;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/product', name: 'product_')]
#[IsGranted('ROLE_PRODUCER')]
class ProductController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(
        Request $request,
        ProductRepository $productRepository,
        PaginatorInterface $paginator
    ): Response {
        /** @var Producer $producer */
        $producer = $this->getUser();

        $products = $paginator->paginate(
            $productRepository->findByFarm($producer->getFarm()),
            $request->query->getInt('page', 1)
        );

        $products->setCustomParameters(
            [
                'align' => 'center',
                'size' => 'small',
                'rounded' => true
            ]
        );

        return $this->render(
            'ui/product/index.html.twig',
            [
                'products' => $products
            ]
        );
    }

    #[Route('/create', name: 'create')]
    public function create(Request $request, HandlerFactoryInterface $handlerFactory): Response
    {
        $product = new Product();

        $handler = $handlerFactory->createHandler(CreateProductHandler::class);

        if ($handler->handle($request, $product)) {
            return $this->redirectToRoute('product_index');
        }

        return $this->render(
            'ui/product/create.html.twig',
            [
                'form' => $handler->createView()
            ]
        );
    }

    #[Route('/{id}/update', name: 'update')]
    #[IsGranted('update', subject: 'product')]
    public function update(
        Product $product,
        Request $request,
        HandlerFactoryInterface $handlerFactory,
        EntityManagerInterface $em
    ): Response {
        /*$handler = $handlerFactory->createHandler(UpdateProductHandler::class);

        if ($handler->handle($request, $product)) {
            return $this->redirectToRoute('product_index');
        }*/

        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($product);
            $em->flush();

            $this->addFlash('success', 'Votre produit a été mis à jour avec succès.');

            return $this->redirectToRoute('product_index');
        }

        return $this->render(
            'ui/product/update.html.twig',
            [
                'form' => $form->createView()
            ]
        );
    }

    #[Route('/{id}/stock', name: 'stock')]
    #[IsGranted('update', subject: 'product')]
    public function stock(Product $product, Request $request, HandlerFactoryInterface $handlerFactory): Response
    {
        $handler = $handlerFactory->createHandler(ProductStockUpdateHandler::class);

        if ($handler->handle($request, $product)) {
            return $this->redirectToRoute('product_index');
        }

        return $this->render(
            'ui/product/stock.html.twig',
            [
                'form' => $handler->createView()
            ]
        );
    }

    #[Route('/{id}/delete', name: 'delete')]
    #[IsGranted('delete', subject: 'product')]
    public function delete(Product $product, EntityManagerInterface $em): Response
    {
        $em->remove($product);

        $em->flush();

        $this->addFlash('success', 'Votre produit à été supprimé avec succès');

        return $this->redirectToRoute('product_index');
    }
}
