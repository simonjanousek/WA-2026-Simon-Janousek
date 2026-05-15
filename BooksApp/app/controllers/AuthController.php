<?php

class AuthController {

    // Konstruktor zajistí start session pro všechny akce v tomto controlleru
    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    // 1. Zobrazení registračního formuláře
    public function register() {
        require_once __DIR__ . '/../views/auth/register.php';
    }

    // 2. Zpracování dat z registrace
    public function storeUser() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            $username = htmlspecialchars($_POST['username'] ?? '');
            $email = htmlspecialchars($_POST['email'] ?? '');
            $firstName = htmlspecialchars($_POST['first_name'] ?? '');
            $lastName = htmlspecialchars($_POST['last_name'] ?? '');
            $nickname = htmlspecialchars($_POST['nickname'] ?? '');
            $password = $_POST['password'] ?? '';
            $passwordConfirm = $_POST['password_confirm'] ?? '';

            if (empty($username) || empty($email) || empty($password)) {
                $this->addErrorMessage('Vyplňte prosím všechna povinná pole.');
                header('Location: index.php?url=auth/register');
                exit;
            }

            if ($password !== $passwordConfirm) {
                $this->addErrorMessage('Zadaná hesla se neshodují.');
                header('Location: index.php?url=auth/register');
                exit;
            }

            require_once __DIR__ . '/../models/Database.php';
            require_once __DIR__ . '/../models/User.php';
            
            $db = (new Database())->getConnection();
            $userModel = new User($db);

            if ($userModel->register($username, $email, $password, $firstName, $lastName, $nickname)) {
                $this->addSuccessMessage('Registrace byla úspěšná. Nyní se můžete přihlásit.');
                header('Location: index.php?url=auth/login');
                exit;
            } else {
                $this->addErrorMessage('Uživatel s tímto e-mailem již existuje.');
                header('Location: index.php?url=auth/register');
                exit;
            }
        }
    }

    // 3. Zobrazení přihlašovacího formuláře
    public function login() {
        require_once __DIR__ . '/../views/auth/login.php';
    }

    // 4. Zpracování přihlášení
    public function authenticate() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = htmlspecialchars($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            require_once __DIR__ . '/../models/Database.php';
            require_once __DIR__ . '/../models/User.php';
            
            $db = (new Database())->getConnection();
            $userModel = new User($db);
            $user = $userModel->findByEmail($email);

            // Ověření uživatele a hesla (sloučeno do jedné logické podmínky)
            if ($user && password_verify($password, $user['password'])) {
                
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['is_admin'] = $user['is_admin'] ?? 0; // Uložení admin práv
                $_SESSION['user_name'] = !empty($user['nickname']) ? $user['nickname'] : $user['username'];

                $this->addSuccessMessage('Vítejte zpět, ' . $_SESSION['user_name'] . '!');
                
                session_write_close(); 
                header('Location: index.php');
                exit;
                
            } else {
                $this->addErrorMessage('Nesprávný e-mail nebo heslo.');
                header('Location: index.php?url=auth/login');
                exit;
            }
        }
    }

    // 5. Odhlášení uživatele
    public function logout() {
        $this->addSuccessMessage('Byli jste úspěšně odhlášeni.');

        unset($_SESSION['user_id']);
        unset($_SESSION['user_name']);
        unset($_SESSION['is_admin']);
        
        session_write_close();
        header('Location: index.php');
        exit;
    }

    // Pomocné metody pro notifikace
    protected function addSuccessMessage($message) {
        $_SESSION['messages']['success'][] = $message;
    }

    protected function addNoticeMessage($message) {
        $_SESSION['messages']['notice'][] = $message;
    }

    protected function addErrorMessage($message) {
        $_SESSION['messages']['error'][] = $message;
    }
} // Konec třídy AuthController