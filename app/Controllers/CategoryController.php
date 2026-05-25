<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Category;

class CategoryController extends Controller
{
    private Category $categoryModel;

    public function __construct()
    {
        parent::__construct();
        $this->categoryModel = new Category();
    }

    public function index(): void
    {
        $categories = $this->categoryModel->getCategoriesWithProductCount();

        $this->view('categories/index', [
            'title' => 'Categories',
            'categories' => $categories,
        ]);
    }

    public function create(): void
    {
        $this->view('categories/create', [
            'title' => 'Add Category',
        ]);
    }

    public function store(): void
    {
        $this->requireCsrf();

        $name = trim($this->input('name'));
        $description = trim($this->input('description'));

        if (empty($name)) {
            $this->setFlash('error', 'Category name is required');
            $this->redirect('categories/create');
        }

        try {
            $this->categoryModel->create([
                'name' => $name,
                'description' => $description,
            ]);

            $this->setFlash('success', 'Category created successfully');
            $this->redirect('categories');
        } catch (\Exception $e) {
            $this->setFlash('error', 'Category name already exists');
            $this->redirect('categories/create');
        }
    }

    public function edit(string $id): void
    {
        $category = $this->categoryModel->find((int)$id);

        if (!$category) {
            $this->setFlash('error', 'Category not found');
            $this->redirect('categories');
        }

        $this->view('categories/edit', [
            'title' => 'Edit Category',
            'category' => $category,
        ]);
    }

    public function update(string $id): void
    {
        $this->requireCsrf();

        $name = trim($this->input('name'));
        $description = trim($this->input('description'));

        if (empty($name)) {
            $this->setFlash('error', 'Category name is required');
            $this->redirect("categories/edit/{$id}");
        }

        try {
            $this->categoryModel->update((int)$id, [
                'name' => $name,
                'description' => $description,
            ]);

            $this->setFlash('success', 'Category updated successfully');
            $this->redirect('categories');
        } catch (\Exception $e) {
            $this->setFlash('error', 'Category name already exists');
            $this->redirect("categories/edit/{$id}");
        }
    }

    public function delete(string $id): void
    {
        $this->requireCsrf();

        $id = (int)$id;

        if ($this->categoryModel->isInUse($id)) {
            $this->setFlash('error', 'Cannot delete category that has products');
            $this->redirect('categories');
        }

        $this->categoryModel->delete($id);
        $this->setFlash('success', 'Category deleted successfully');
        $this->redirect('categories');
    }
}
