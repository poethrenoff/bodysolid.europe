<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use AppBundle\Entity\Category;
use AppBundle\Entity\Product;
use AppBundle\Util\Pager;

class ProductController extends Controller
{
    /**
     * @var int
     */
    const LIMIT = 20;

    /**
     * Category menu
     *
     * @param Request $request
     * @return Response
     */
    public function menuAction(Request $request)
    {
        $categoryList = $this->getDoctrine()->getManager()
            ->getRepository(Category::class)->findBy(['active' => true, 'category' => null]);

        return $this->render('AppBundle::Product/menu.html.twig', array(
            'categoryList' => $categoryList
        ));
    }

    /**
     * Category item
     *
     * @Route("/product/{categoryName}", name="categoryItem")
     *
     * @param Request $request
     * @param string $categoryName
     * @return Response
     */
    public function categoryAction(Request $request, string $categoryName)
    {
        $categoryItem = $this->getDoctrine()->getManager()
            ->getRepository(Category::class)->findOneBy(['active' => true, 'name' => $categoryName]);

        if (!$categoryItem) {
            throw new NotFoundHttpException('Страница не найдена');
        }

        return $this->render('AppBundle::Product/category.html.twig', array(
            'categoryItem' => $categoryItem
        ));
    }

    /**
     * Product item
     *
     * @Route("/product/{categoryName}/{id}", name="productItem")
     */
    public function productAction(Request $request, $categoryName, $id)
    {
        $productItem = $this->getDoctrine()->getManager()
            ->getRepository(Product::class)->findActive($id);

        if (!$productItem) {
            throw new NotFoundHttpException('Страница не найдена');
        }

        return $this->render('AppBundle::Product/product.html.twig', array(
            'productItem' => $productItem
        ));
    }

    /**
     * Search result
     *
     * @Route("/search", name="search")
     *
     * @param Request $request
     * @return Response
     */
    public function searchAction(Request $request)
    {
        $page = max(1, $request->query->getInt('page', 1));
        $offset = ($page - 1) * self::LIMIT;

        $productList = array();
        $text = $request->query->get('text');
        if ($text !== '') {
            $productList = $this->getDoctrine()->getManager()
                ->getRepository(Product::class)->findByText($text, $offset, self::LIMIT);
        }

        return $this->render('AppBundle::Product/search.html.twig', array(
            'text' => $text,
            'productList' => $productList,
            'pager' => Pager::create(count($productList), self::LIMIT, $page),
        ));
    }

    /**
     * Pricelist
     *
     * @Route("/price", name="price")
     */
    public function priceAction(Request $request)
    {
        $catalogueList = $this->getDoctrine()->getManager()
            ->getRepository(Category::class)->findAllActive();

        return $this->render('AppBundle::Product/price.html.twig', array(
            'catalogueList' => $catalogueList
        ));
    }
}