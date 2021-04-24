<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CategoryController extends AbstractController
{
    /**
     * @Route("admin/categories", name="categories")
     */
    public function viewCategories 
    (
        CategoryRepository $categoryRepository,
        Category $category = null,
        request $request,
        EntityManagerInterface $manager,
        SluggerInterface $slugger
    )
    {
        $categories = $categoryRepository->findAll();
        if(!$categories){
            throw $this->createNotFoundException("Category not found mehnnn");
        }

        //Create category form
        $category= new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $category->setSlug(strtolower($slugger->slug($category->getName())));

            $manager->persist($category);
            $manager->flush();
            $this->addFlash('success', 'Category' . ' ' .$category->getName() . ' '. 'created successfully');
            return $this->redirectToRoute('categories');
        }


        return $this->render('category/view_categories.html.twig', [
            'categories' => $categories,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("admin/category/{id}/edit", name="edit_category")
     */
    public function editCategories ($id, CategoryRepository $categoryRepository)
    {
        $categories = $categoryRepository->findAll();
        if(!$categories){
            throw $this->createNotFoundException("Category not found mehnnn");
        }
        return $this->render('category/view_categories.html.twig', [
            'categories' => $categories,
        ]);
    }

    /**
     * @Route("admin/category/{id}/delete", name="delete_category")
     */
    public function deleteCategories ($id, CategoryRepository $categoryRepository)
    {
        $categories = $categoryRepository->findAll();
        if(!$categories){
            throw $this->createNotFoundException("Category not found mehnnn");
        }
        return $this->render('category/view_categories.html.twig', [
            'categories' => $categories,
        ]);
    }
}
