<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CategoryController extends AbstractController
{
    /**
     * @Route("admin/categories", name="categories")
     */
    public function viewCategories (CategoryRepository $categoryRepository)
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
     * @Route("admin/category/create", name="create_category")
     */
    public function createCategories (CategoryRepository $categoryRepository)
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
