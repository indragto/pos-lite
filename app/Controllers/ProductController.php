<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Product;
use App\Models\Category;

class ProductController extends Controller
{
    private Product $productModel;
    private Category $categoryModel;

    public function __construct()
    {
        parent::__construct();
        $this->productModel = new Product();
        $this->categoryModel = new Category();
    }

    public function index(): void
    {
        $page = (int)($this->input('page') ?? 1);
        $perPage = 15;
        $offset = ($page - 1) * $perPage;

        $search = trim($this->input('search') ?? '');
        $categoryId = $this->input('category_id');
        $categoryId = $categoryId !== null ? (int)$categoryId : null;

        $conditions = ['is_active' => 1];

        $products = $this->productModel->getProductsWithCategory(
            $conditions,
            $search,
            $categoryId,
            'p.id DESC',
            $perPage,
            $offset
        );

        $totalProducts = $this->productModel->count($conditions);
        $totalPages = ceil($totalProducts / $perPage);

        $categories = $this->categoryModel->getCategories();

        $this->view('products/index', [
            'title' => 'Products',
            'products' => $products,
            'categories' => $categories,
            'search' => $search,
            'categoryId' => $categoryId,
            'page' => $page,
            'totalPages' => $totalPages,
        ]);
    }

    public function create(): void
    {
        $categories = $this->categoryModel->getOptions();
        $autoSKU = generateSKU();

        $this->view('products/create', [
            'title' => 'Add Product',
            'categories' => $categories,
            'autoSKU' => $autoSKU,
        ]);
    }

    public function store(): void
    {
        $this->requireCsrf();

        $sku = trim($this->input('sku'));
        $name = trim($this->input('name'));
        $categoryId = $this->input('category_id');
        $price = $this->input('price');
        $cost = $this->input('cost');
        $stock = $this->input('stock');
        $isActive = $this->input('is_active') ? 1 : 0;

        // Validation
        if (empty($name) || empty($price)) {
            $this->setFlash('error', 'Product name and price are required');
            $this->redirect('products/create');
        }

        if ($this->productModel->skuExists($sku)) {
            $this->setFlash('error', 'SKU already exists');
            $this->redirect('products/create');
        }

        $data = [
            'sku' => $sku,
            'name' => $name,
            'category_id' => $categoryId ?: null,
            'price' => (float)$price,
            'cost' => $cost ? (float)$cost : null,
            'stock' => (int)$stock,
            'is_active' => $isActive,
        ];

        // Handle image upload
        $file = $this->file('image');
        if ($file && $file['error'] === UPLOAD_ERR_OK) {
            $imageName = $this->productModel->handleImageUpload($file);
            if ($imageName) {
                $data['image'] = $imageName;
            }
        }

        $this->productModel->create($data);
        $this->setFlash('success', 'Product created successfully');
        $this->redirect('products');
    }

    public function edit(string $id): void
    {
        $product = $this->productModel->getProductWithCategory((int)$id);

        if (!$product) {
            $this->setFlash('error', 'Product not found');
            $this->redirect('products');
        }

        $categories = $this->categoryModel->getOptions();

        $this->view('products/edit', [
            'title' => 'Edit Product',
            'product' => $product,
            'categories' => $categories,
        ]);
    }

    public function update(string $id): void
    {
        $this->requireCsrf();

        $id = (int)$id;

        $sku = trim($this->input('sku'));
        $name = trim($this->input('name'));
        $categoryId = $this->input('category_id');
        $price = $this->input('price');
        $cost = $this->input('cost');
        $stock = $this->input('stock');
        $isActive = $this->input('is_active') ? 1 : 0;

        if (empty($name) || empty($price)) {
            $this->setFlash('error', 'Product name and price are required');
            $this->redirect("products/edit/{$id}");
        }

        if ($this->productModel->skuExists($sku, $id)) {
            $this->setFlash('error', 'SKU already exists');
            $this->redirect("products/edit/{$id}");
        }

        $data = [
            'sku' => $sku,
            'name' => $name,
            'category_id' => $categoryId ?: null,
            'price' => (float)$price,
            'cost' => $cost ? (float)$cost : null,
            'stock' => (int)$stock,
            'is_active' => $isActive,
        ];

        // Handle image upload
        $product = $this->productModel->find($id);
        $file = $this->file('image');
        if ($file && $file['error'] === UPLOAD_ERR_OK) {
            $imageName = $this->productModel->handleImageUpload($file, $product['image'] ?? null);
            if ($imageName) {
                $data['image'] = $imageName;
            }
        }

        $this->productModel->update($id, $data);
        $this->setFlash('success', 'Product updated successfully');
        $this->redirect('products');
    }

    public function delete(string $id): void
    {
        $this->requireCsrf();

        $id = (int)$id;
        $product = $this->productModel->find($id);

        if ($product) {
            // Delete image if exists
            if (!empty($product['image']) && file_exists(PUBLIC_PATH . '/uploads/' . $product['image'])) {
                unlink(PUBLIC_PATH . '/uploads/' . $product['image']);
            }

            $this->productModel->delete($id);
        }

        $this->setFlash('success', 'Product deleted successfully');
        $this->redirect('products');
    }

    /**
     * AJAX search for POS
     */
    public function search(): void
    {
        $keyword = trim($this->input('q') ?? '');
        $categoryId = $this->input('category_id');
        $categoryId = $categoryId !== null ? (int)$categoryId : null;

        $products = $this->productModel->searchProducts($keyword, $categoryId);

        $this->json([
            'success' => true,
            'products' => $products,
        ]);
    }
}
