<?php
namespace App\Controller;
use CakeCore\Controller;
use PDO;
class AuthController extends Controller {
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            $pdo = \CakeCore\Database::connect();
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['role'] = $user['role'] ?? 'user';
                $this->redirect('/products');
            }
            $_SESSION['flash_error'] = 'Invalid credentials';
            $this->redirect('/login');
        }
        $this->render('Auth/login', ['title' => 'Login']);
    }
    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'] ?? '';
            $email = $_POST['email'] ?? '';
            $password = password_hash($_POST['password'] ?? '', PASSWORD_BCRYPT);
            $role = 'user';
            $pdo = \CakeCore\Database::connect();
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
            if ($stmt->execute([$name, $email, $password, $role])) {
                $_SESSION['user_id'] = $pdo->lastInsertId();
                $_SESSION['user_name'] = $name;
                $_SESSION['role'] = $role;
                $this->redirect('/products');
            }
            $_SESSION['flash_error'] = 'Registration failed';
            $this->redirect('/register');
        }
        $this->render('Auth/register', ['title' => 'Register']);
    }
    public function logout() { session_destroy(); $this->redirect('/login'); }
}