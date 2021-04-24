<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController {

    /**
     * @Route("/products", name="products")
     */
    public function showProducts (ProductRepository $productRepository) 
    {
        $product = $productRepository->findBy([], [], 3);

        return $this->render('product/products.html.twig', [
            'products' => $product
        ]);
    }

    /**
     * @Route("/add", name="add-product")
     */
    public function add (Product $product = null, Request $request, EntityManagerInterface $manager) 
    {
        //create an istance of my new product
        $product = new Product();

        //create my product form
        $form = $this->createForm(ProductType::class, $product);

        // hamdle my form request
        $form->handleRequest($request);

        // check if form is submitted
        if ($form->isSubmitted() && $form->isValid()) 
        {

            // presist and flush item
            $manager->persist($product);
            $manager->flush();

            // redirect to homepage
            return $this->redirectToRoute('homepage');
        }

        return $this->render('product/add_product.html.twig', [
            'products' => $product,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("product/{slug}", name="show_product")
     */
    public function show($slug, ProductRepository $productRepository)
    {
    
    $product = $productRepository->findOneBy([
        'slug' => $slug
    ]);

    if(!$product){
        throw $this->createNotFoundException("this product does not exists mehn");
    }
    return $this->render('product/show_product.html.twig', [
        'product' => $product
    ]);
    }

    /**
     * @Route("/{slug}", name="product_category")
     */
    public function getProductByCategory($slug, CategoryRepository $categoryRepository)
    {
        $category = $categoryRepository->findOneBy(['slug' => $slug]);
   
        if(!$category){
            throw $this->createNotFoundException("Category not found mehnnn");
        }
        return $this->render('product/category.html.twig', [
            'slug' => $slug,
            'category' => $category,
        ]);
    }


}