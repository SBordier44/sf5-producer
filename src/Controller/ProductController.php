<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Product;
use App\Handler\CreateProductHandler;
use App\Handler\ProductStockUpdateHandler;
use App\Handler\UpdateProductHandler;
use App\HandlerFactory\HandlerFactoryInterface;
use App\Repository\ProductRepository;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ProductController
 * @package App\Controller
 * @Route("/products")
 * @IsGranted("ROLE_PRODUCER")
 */
class ProductController extends AbstractController
{
    /**
     * @param Request $request
     * @param ProductRepository $productRepository
     * @param PaginatorInterface $paginator
     * @return Response
     * @Route("/", name="product_index")
     */
    public function index(
        Request $request,
        ProductRepository $productRepository,
        PaginatorInterface $paginator
    ): Response {
        $products = $paginator->paginate(
            $productRepository->findByFarm($this->getUser()->getFarm()),
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

    /**
     * @param Request $request
     * @param HandlerFactoryInterface $handlerFactory
     * @return Response
     * @Route("/create", name="product_create")
     */
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

    /**
     * @param Product $product
     * @param Request $request
     * @param HandlerFactoryInterface $handlerFactory
     * @return Response
     * @Route("/{id}/update", name="product_update")
     * @IsGranted("update", subject="product")
     */
    public function update(Product $product, Request $request, HandlerFactoryInterface $handlerFactory): Response
    {
        $handler = $handlerFactory->createHandler(UpdateProductHandler::class);

        if ($handler->handle($request, $product)) {
            return $this->redirectToRoute('product_index');
        }

        return $this->render(
            'ui/product/update.html.twig',
            [
                'form' => $handler->createView()
            ]
        );
    }

    /**
     * @param Product $product
     * @param Request $request
     * @param HandlerFactoryInterface $handlerFactory
     * @return Response
     * @Route("/{id}/stock", name="product_stock")
     * @IsGranted("update", subject="product")
     */
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

    /**
     * @param Product $product
     * @return Response
     * @Route("/{id}/delete", name="product_delete")
     * @IsGranted("delete", subject="product")
     */
    public function delete(Product $product): Response
    {
        $this->getDoctrine()->getManager()->remove($product);
        $this->getDoctrine()->getManager()->flush();
        $this->addFlash('success', 'Votre produit à été supprimé avec succès');
        return $this->redirectToRoute('product_index');
    }
}
