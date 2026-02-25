<?php
session_start();
require_once("../config/db.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre   = trim($_POST['first_name']);
    $apellido = trim($_POST['last_name']);
    $edad     = intval($_POST['age']);
    $dui      = trim($_POST['dui']);
    $email    = trim($_POST['email']);
    $telefono = trim($_POST['phone']);
    $password = trim($_POST['password']);

    // Helper para mostrar alert y terminar script
    function showAlert($icon, $title, $text, $redirect = "back") {
        $redirectAction = $redirect === "back"
            ? "window.history.back();"
            : "window.location.href='{$redirect}';";

        echo "
        <!DOCTYPE html>
        <html lang='en'>
        <head>
            <meta charset='UTF-8'>
            <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        </head>
        <body>
            <script>
                Swal.fire({
                    icon: '{$icon}',
                    title: '{$title}',
                    text: '{$text}',
                    confirmButtonColor: '#3085d6'
                }).then(() => { {$redirectAction} });
            </script>
        </body>
        </html>";
        exit;
    }

    // Validaciones
    if (!preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/", $nombre) || strlen($nombre) > 50 ||
        !preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/", $apellido) || strlen($apellido) > 50) {
        showAlert("error", "Invalid Name", "Names must contain only letters (max 50 characters).");
    }

    if ($edad < 18 || $edad > 120) {
        showAlert("error", "Invalid Age", "Age must be between 18 and 120.");
    }

    if (!preg_match("/^[0-9]{8}-[0-9]{1}$/", $dui)) {
        showAlert("error", "Invalid DUI", "DUI must follow the format ########-#");
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        showAlert("error", "Invalid Email", "Please enter a valid email address.");
    }

    if (!preg_match("/^\+503[0-9]{8}$/", $telefono)) {
        showAlert("error", "Invalid Phone", "Phone must be in format +503XXXXXXXX (8 digits).");
    }

    try {
        // Comprobar si ya existe
        $check = $pdo->prepare("SELECT id FROM usuarios WHERE email = :email OR dui = :dui");
        $check->execute(['email' => $email, 'dui' => $dui]);

        if ($check->rowCount() > 0) {
            showAlert("warning", "User already exists", "A user with this email or DUI already exists.");
        } else {
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

            $sql = "INSERT INTO usuarios (nombre, apellido, edad, dui, email, telefono, password, reputacion, fecha_registro) 
                    VALUES (:nombre, :apellido, :edad, :dui, :email, :telefono, :password, 0, NOW())";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                'nombre'   => $nombre,
                'apellido' => $apellido,
                'edad'     => $edad,
                'dui'      => $dui,
                'email'    => $email,
                'telefono' => $telefono,
                'password' => $hashedPassword
            ]);

            showAlert("success", "Registration successful!", "You can now login.", "login.php");
        }
    } catch (PDOException $e) {
        showAlert("error", "Database Error", addslashes($e->getMessage()));
    }
}
?>







<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Swaply</title>
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
            <h2 class="mt-6 text-3xl font-bold text-foreground">Create your account</h2>
            <p class="mt-2 text-sm text-gray-600">
                Join our community of swappers
            </p>
        </div>

       
        <div class="bg-card rounded-xl shadow-lg border border-border p-8">
            <form class="space-y-6" action="register.php" method="POST">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="first_name" class="block text-sm font-medium text-foreground mb-2">
                            First Name
                        </label>
                        <input 
    id="first_name" 
    name="first_name" 
    type="text" 
    required 
    maxlength="50"
    class="w-full px-3 py-2 bg-input border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
    placeholder="John">
                        
                    </div>
                    <div>
                        <label for="last_name" class="block text-sm font-medium text-foreground mb-2">
                            Last Name
                        </label>
                       <input 
    id="last_name" 
    name="last_name" 
    type="text" 
    required 
    maxlength="50"
    class="w-full px-3 py-2 bg-input border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
    placeholder="Doe">

                        
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="age" class="block text-sm font-medium text-foreground mb-2">
                            Age
                        </label>
                        <input 
                            id="age" 
                            name="age" 
                            type="number" 
                            min="18" 
                            max="120" 
                            required 
                            class="w-full px-3 py-2 bg-input border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                            placeholder="25"
                        >
                    </div>
                    <div>
    <label for="dui" class="block text-sm font-medium text-foreground mb-2">
        DUI
    </label>
    <input 
    id="dui" 
    name="dui" 
    type="text" 
    required 
    maxlength="10" 
    pattern="[0-9]{8}-[0-9]{1}"
    class="w-full px-3 py-2 bg-input border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
    placeholder="12345678-9">

    
</div>
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-foreground mb-2">
                        Email Address
                    </label>
                    <input 
                        id="email" 
                        name="email" 
                        type="email" 
                        required 
                        class="w-full px-3 py-2 bg-input border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                        placeholder="john@example.com"
                    >
                </div>

                <div>
    <label for="phone" class="block text-sm font-medium text-foreground mb-2">
        Phone Number
    </label>
    <input 
        id="phone" 
        name="phone" 
        type="text" 
        required 
        maxlength="15"
        class="w-full px-3 py-2 bg-input border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
        placeholder="503-00000000"
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
                            minlength="8"
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
                    <p class="mt-1 text-xs text-gray-500">
                        Must be at least 8 characters long
                    </p>
                </div>

                <div class="flex items-center">
                    <input 
                        id="terms" 
                        name="terms" 
                        type="checkbox" 
                        required 
                        class="h-4 w-4 text-primary focus:ring-primary border-border rounded"
                    >
                    <label for="terms" class="ml-2 block text-sm text-foreground">
                        I agree to the 
                        <a href="terms.php" class="text-primary hover:underline">Terms of Service</a> 
                        and 
                        <a href="#" class="text-primary hover:underline">Privacy Policy</a>
                    </label>
                </div>

                <div>
                    <button 
                        type="submit" 
                        class="w-full bg-primary text-primary-foreground py-3 px-4 rounded-lg font-semibold hover:opacity-90 transition-opacity focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2"
                    >
                        Create Account
                    </button>
                </div>
            </form>

            <div class="mt-6 text-center">
                <p class="text-sm text-gray-600">
                    Already have an account? 
                    <a href="login.php" class="text-primary hover:underline font-medium">
                        Sign in here
                    </a>
                </p>
            </div>
        </div>


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

    document.addEventListener("DOMContentLoaded", () => {
        const phoneInput = document.getElementById("phone");

    
        phoneInput.value = "+503";

        phoneInput.addEventListener("input", function () {

            let value = this.value.replace(/[^0-9]/g, "");
            
   
            if (!value.startsWith("503")) {
                value = "503" + value.replace(/^503/, "");
            }

            
            if (value.length > 11) { 
                value = value.substring(0, 11);
            }

            this.value = "+" + value;
        });
    });


    document.querySelector('form').addEventListener('submit', function(e) {
        const password = document.getElementById('password').value;
        const terms = document.getElementById('terms').checked;
        const phone = document.getElementById("phone").value;

        let errorMessage = "";

        if (password.length < 8) {
            errorMessage = "Password must be at least 8 characters long.";
        } else if (!terms) {
            errorMessage = "You must accept the Terms of Service and Privacy Policy.";
        } else if (!/^\+503[0-9]{8}$/.test(phone)) {
            errorMessage = "Phone must be in format +503XXXXXXXX (8 digits).";
        }

        if (errorMessage !== "") {
            e.preventDefault();
            Swal.fire({
                icon: "warning",
                title: "Validation Error",
                text: errorMessage,
                confirmButtonColor: "#d33"
            });
        }
    });

    document.addEventListener("DOMContentLoaded", () => {
    const nameInputs = [document.getElementById("first_name"), document.getElementById("last_name")];
    const duiInput = document.getElementById("dui");

    // Bloquear caracteres raros en nombres
    nameInputs.forEach(input => {
        input.addEventListener("input", function() {
            this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, ""); // solo letras y espacios
            if (this.value.length > 50) {
                this.value = this.value.substring(0, 50);
            }
        });
    });

    // DUI con formato #######-#
    duiInput.addEventListener("input", function() {
        let value = this.value.replace(/[^0-9]/g, "");
        if (value.length > 8) {
            value = value.substring(0, 8) + "-" + value.substring(8, 9);
        }
        this.value = value;
    });
});

</script>

</body>
</html>
