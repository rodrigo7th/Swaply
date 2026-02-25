<?php
session_start();
require_once("../config/db.php");

if (isset($_SESSION['user'])) {
    header("Location: ../index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $identifier = trim($_POST['login_identifier']);
    $password   = trim($_POST['password']);

    try {
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = :identifier OR dui = :identifier LIMIT 1");
        $stmt->execute(['identifier' => $identifier]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            echo "
            <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
            <script>
                Swal.fire({
                    icon: 'error',
                    title: 'User not found',
                    text: 'No account found with this Email or DUI.',
                    confirmButtonColor: '#d33'
                }).then(() => { window.history.back(); });
            </script>";
            exit;
        }

        if (!password_verify($password, $user['password'])) {
            echo "
            <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
            <script>
                Swal.fire({
                    icon: 'error',
                    title: 'Incorrect Password',
                    text: 'The password you entered is invalid.',
                    confirmButtonColor: '#d33'
                }).then(() => { window.history.back(); });
            </script>";
            exit;
        }

       
        $_SESSION['user'] = [
            'id'         => $user['id'],
            'first_name' => $user['nombre'],
            'last_name'  => $user['apellido'],
            'email'      => $user['email'],
            'avatar'     => $user['avatar'] ?? null
        ];

     
        header("Location: ../index.php");
        exit;

    } catch (PDOException $e) {
        echo "
        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Database Error',
                text: '" . addslashes($e->getMessage()) . "',
                confirmButtonColor: '#d33'
            }).then(() => { window.history.back(); });
        </script>";
        exit;
    }
}
?>






<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Swaply</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --background: #f0f4f8;
            --foreground: #4b5563;
            --card: #ffffff;
            --primary: #4f83cc;
            --primary-foreground: #ffffff;
            --secondary: #a2d5ab;
            --border: #d1d5db;
            --input: #ffffff;
        }
        
        .bg-background { background-color: var(--background); }
        .bg-card { background-color: var(--card); }
        .bg-primary { background-color: var(--primary); }
        .text-foreground { color: var(--foreground); }
        .text-primary { color: var(--primary); }
        .text-primary-foreground { color: var(--primary-foreground); }
        .border-border { border-color: var(--border); }
        .bg-input { background-color: var(--input); }
    </style>
</head>
<body class="bg-background min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <!-- Header -->
        <div class="text-center">
            <a href="../index.php" class="text-3xl font-bold text-primary">
                <i class="fas fa-exchange-alt mr-2"></i>Swaply
            </a>
            <h2 class="mt-6 text-3xl font-bold text-foreground">Welcome back</h2>
            <p class="mt-2 text-sm text-gray-600">
                Sign in to continue swapping
            </p>
        </div>

        <!-- Login Form -->
        <div class="bg-card rounded-xl shadow-lg border border-border p-8">
            <form class="space-y-6" action="login.php" method="POST">

                <div>
                    <label for="login_identifier" class="block text-sm font-medium text-foreground mb-2">
                        Email or DUI
                    </label>
                    <input 
                        id="login_identifier" 
                        name="login_identifier" 
                        type="text" 
                        required 
                        class="w-full px-3 py-2 bg-input border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                        placeholder="john@example.com or 12345678-9"
                    >
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-foreground mb-2">
                        Password
                    </label>
                    <div class="relative">
                        <input 
                            id="password" 
                            name="password" 
                            type="password" 
                            required 
                            class="w-full px-3 py-2 bg-input border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent pr-10"
                            placeholder="••••••••"
                        >
                        <button 
                            type="button" 
                            class="absolute inset-y-0 right-0 pr-3 flex items-center"
                            onclick="togglePassword()"
                        >
                            <i id="password-icon" class="fas fa-eye text-gray-400 hover:text-gray-600"></i>
                        </button>
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input 
                            id="remember_me" 
                            name="remember_me" 
                            type="checkbox" 
                            class="h-4 w-4 text-primary focus:ring-primary border-border rounded"
                        >
                        <label for="remember_me" class="ml-2 block text-sm text-foreground">
                            Remember me
                        </label>
                    </div>

                    <div class="text-sm">
                        <a href="#" class="text-primary hover:underline">
                            Forgot your password?
                        </a>
                    </div>
                </div>

                <div>
                    <button 
                        type="submit" 
                        class="w-full bg-primary text-primary-foreground py-3 px-4 rounded-lg font-semibold hover:opacity-90 transition-opacity focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2"
                    >
                        Sign In
                    </button>
                </div>
            </form>

            <!-- Social Login Options -->
            <div class="mt-6">
                <div class="relative">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-border"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-2 bg-card text-gray-500">Or continue with</span>
                    </div>
                </div>

                <div class="mt-6 grid grid-cols-2 gap-3">
                    <button class="w-full inline-flex justify-center py-2 px-4 border border-border rounded-lg shadow-sm bg-input text-sm font-medium text-foreground hover:bg-gray-50 transition-colors">
                        <i class="fab fa-google text-red-500 mr-2"></i>
                        Google
                    </button>
                    <button class="w-full inline-flex justify-center py-2 px-4 border border-border rounded-lg shadow-sm bg-input text-sm font-medium text-foreground hover:bg-gray-50 transition-colors">
                        <i class="fab fa-facebook text-blue-600 mr-2"></i>
                        Facebook
                    </button>
                </div>
            </div>

            <div class="mt-6 text-center">
                <p class="text-sm text-gray-600">
                    Don't have an account? 
                    <a href="register.php" class="text-primary hover:underline font-medium">
                        Sign up here
                    </a>
                </p>
            </div>
        </div>

        <!-- Back to Home -->
        <div class="text-center">
            <a href="../index.php" class="text-sm text-gray-600 hover:text-primary transition-colors">
                <i class="fas fa-arrow-left mr-1"></i>
                Back to Home
            </a>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const passwordIcon = document.getElementById('password-icon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                passwordIcon.classList.remove('fa-eye');
                passwordIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                passwordIcon.classList.remove('fa-eye-slash');
                passwordIcon.classList.add('fa-eye');
            }
        }

        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const identifier = document.getElementById('login_identifier').value;
            const password = document.getElementById('password').value;
            
            if (!identifier || !password) {
                e.preventDefault();
                alert('Please fill in all required fields.');
                return;
            }
        });
    </script>
</body>
</html>
