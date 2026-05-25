<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;
use App\Models\Role;

class UserController extends Controller
{
    private User $userModel;
    private Role $roleModel;

    public function __construct()
    {
        parent::__construct();
        $this->userModel = new User();
        $this->roleModel = new Role();
    }

    public function index(): void
    {
        $page = (int)($this->input('page') ?? 1);
        $perPage = 15;
        $offset = ($page - 1) * $perPage;

        $search = trim($this->input('search') ?? '');
        $roleId = $this->input('role_id');
        $roleId = $roleId !== '' && $roleId !== null ? (int)$roleId : null;

        $conditions = [];
        if ($roleId !== null) {
            $conditions['role_id'] = $roleId;
        }

        if ($search !== '') {
            $users = $this->userModel->search($search, $perPage, $offset);
            $totalUsers = count($this->userModel->search($search, 1000, 0));
        } else {
            $users = $this->userModel->getUsersWithRole($conditions, 'u.id DESC', $perPage, $offset);
            $totalUsers = $this->userModel->count($conditions);
        }

        $totalPages = ceil($totalUsers / $perPage);
        $roles = $this->roleModel->getRoles();

        $this->view('users/index', [
            'title' => 'Users',
            'users' => $users,
            'roles' => $roles,
            'search' => $search,
            'roleId' => $roleId,
            'page' => $page,
            'totalPages' => $totalPages,
        ]);
    }

    public function create(): void
    {
        $roles = $this->roleModel->getRoles();

        $this->view('users/create', [
            'title' => 'Add User',
            'roles' => $roles,
        ]);
    }

    public function store(): void
    {
        $this->requireCsrf();

        $username = trim($this->input('username'));
        $password = $this->input('password');
        $passwordConfirm = $this->input('password_confirm');
        $fullName = trim($this->input('full_name'));
        $email = trim($this->input('email'));
        $roleId = (int)$this->input('role_id');

        // Validation
        if (empty($username) || empty($password) || empty($fullName)) {
            $this->setFlash('error', 'Username, password, and full name are required');
            $this->redirect('users/create');
        }

        if (strlen($password) < 6) {
            $this->setFlash('error', 'Password must be at least 6 characters');
            $this->redirect('users/create');
        }

        if ($password !== $passwordConfirm) {
            $this->setFlash('error', 'Passwords do not match');
            $this->redirect('users/create');
        }

        // Check if username exists
        $existing = $this->userModel->findByUsername($username);
        if ($existing) {
            $this->setFlash('error', 'Username already exists');
            $this->redirect('users/create');
        }

        try {
            $this->userModel->createUser([
                'username' => $username,
                'password' => $password,
                'full_name' => $fullName,
                'email' => $email ?: null,
                'role_id' => $roleId,
                'is_active' => 1,
            ]);

            $this->setFlash('success', 'User created successfully');
            $this->redirect('users');
        } catch (\Exception $e) {
            $this->setFlash('error', 'Failed to create user: ' . $e->getMessage());
            $this->redirect('users/create');
        }
    }

    public function edit(string $id): void
    {
        $user = $this->userModel->find((int)$id);

        if (!$user) {
            $this->setFlash('error', 'User not found');
            $this->redirect('users');
        }

        $roles = $this->roleModel->getRoles();

        // Remove password from view data
        unset($user['password']);

        $this->view('users/edit', [
            'title' => 'Edit User',
            'user' => $user,
            'roles' => $roles,
        ]);
    }

    public function update(string $id): void
    {
        $this->requireCsrf();

        $id = (int)$id;

        $username = trim($this->input('username'));
        $password = $this->input('password');
        $passwordConfirm = $this->input('password_confirm');
        $fullName = trim($this->input('full_name'));
        $email = trim($this->input('email'));
        $roleId = (int)$this->input('role_id');

        if (empty($username) || empty($fullName)) {
            $this->setFlash('error', 'Username and full name are required');
            $this->redirect("users/edit/{$id}");
        }

        // Validate password if provided
        if (!empty($password)) {
            if (strlen($password) < 6) {
                $this->setFlash('error', 'Password must be at least 6 characters');
                $this->redirect("users/edit/{$id}");
            }

            if ($password !== $passwordConfirm) {
                $this->setFlash('error', 'Passwords do not match');
                $this->redirect("users/edit/{$id}");
            }
        }

        $data = [
            'username' => $username,
            'full_name' => $fullName,
            'email' => $email ?: null,
            'role_id' => $roleId,
        ];

        if (!empty($password)) {
            $data['password'] = $password;
        }

        $this->userModel->updateUser($id, $data);
        $this->setFlash('success', 'User updated successfully');
        $this->redirect('users');
    }

    public function toggleActive(string $id): void
    {
        $this->requireCsrf();

        $id = (int)$id;

        // Prevent deactivating self
        if ($id === currentUser()['id']) {
            $this->setFlash('error', 'You cannot deactivate your own account');
            $this->redirect('users');
        }

        $this->userModel->toggleActive($id);
        $this->setFlash('success', 'User status updated');
        $this->redirect('users');
    }

    public function delete(string $id): void
    {
        $this->requireCsrf();

        $id = (int)$id;

        // Prevent deleting self
        if ($id === currentUser()['id']) {
            $this->setFlash('error', 'You cannot delete your own account');
            $this->redirect('users');
        }

        $this->userModel->delete($id);
        $this->setFlash('success', 'User deleted successfully');
        $this->redirect('users');
    }
}
