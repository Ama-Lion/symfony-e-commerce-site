<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
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
     * @Route("/admin/products/create", name="create_product")
     */
    public function createProduct 
    (
        Product $product = null, 
        Request $request, 
        EntityManagerInterface $manager,
        SluggerInterface $slugger
    ) 
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

            $product->setSlug(strtolower($slugger->slug($product->getName())));
             
            // presist and flush item
            $manager->persist($product);
            $manager->flush();

            $this->addFlash('success', 'Product' . ' ' .$product->getName() . ' ' . 'created successfully');
            // redirect to homepage
            return $this->redirectToRoute('show_product', [
                'slug' => $product->getSlug()
            ]);
        }
        return $this->render('product/add_product.html.twig', [
            'products' => $product,
            'form' => $form->createView()
        ]);
    }
    
    /**
     * @Route("/admin/product/edit/{id}", name="edit_product")
     */
    public function editProduct
    (
        $id,
        ProductRepository $productRepository,
        Request $request, 
        EntityManagerInterface $manager
    )
    {
    
        $product = $productRepository->find($id);

        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);

        if ($form->isSubmitted()){
            $manager->flush();
            return $this->redirectToRoute('show_product', [
                'slug' => $product->getSlug()
            ]);
            $this->addFlash('info', 'Product' . ' ' .$product->getName() . ' ' . 'updated successfully');
        }

        return $this->render('product/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/product/delete/{id}", name="delete_product")
     */
    public function deleteProduct
    (
        $id,
        ProductRepository $productRepository,
        EntityManagerInterface $manager
    )
    {
        $product = $productRepository->find($id);
        $manager->remove($product);
        $manager->flush();
        $this->addFlash('success', 'Product' . ' ' .$product->getName() . ' ' . 'deleted successfully');
        return $this->redirectToRoute('products');
    }

    /**
     * @Route("product/{slug}", name="show_product")
     */
    public function productDetails($slug, ProductRepository $productRepository)
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
     * @Route("category/{slug}", name="product_category")
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