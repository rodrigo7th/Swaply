<?php
session_start();
require_once(__DIR__ . "/../Config/db.php");

// VERIFICAR QUE EL USUARIO ESTÉ LOGUEADO
if (!isset($_SESSION['user'])) {
    header('Location: /SWAPLY/users/login.php');
    exit;
}

function showAlert($icon, $title, $text, $redirect = null)
{
    echo "
    <!DOCTYPE html>
    <html lang='en'>
    <head>
        <meta charset='UTF-8'>
        <title>Alert</title>
        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    </head>
    <body>
        <script>
            Swal.fire({
                icon: '$icon',
                title: '$title',
                text: '$text',
                confirmButtonColor: '#4f83cc'
            }).then(() => {
                " . ($redirect ? "window.location.href='$redirect';" : "window.history.back();") . "
            });
        </script>
    </body>
    </html>";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name        = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price       = $_POST['price'];
    $location    = trim($_POST['location']);
    $category    = $_POST['category'];

    // OBTENER EL ID DEL USUARIO LOGUEADO
    $userId = $_SESSION['user']['id'];

    function sanitize($str)
    {
        $str = strip_tags($str);
        $str = htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
        return substr($str, 0, 255);
    }

    if (empty($name) || empty($description) || empty($price) || empty($location) || empty($category)) {
        showAlert("error", "Missing Fields", "All fields are required.");
    }

    if (!preg_match("/^[a-zA-Z0-9\s]+$/", $name)) {
        showAlert("error", "Invalid Name", "Name must contain only letters and numbers.");
    }

    if (!is_numeric($price) || $price <= 0) {
        showAlert("error", "Invalid Price", "Price must be a positive number.");
    }

    if (!preg_match("/^[a-zA-Z0-9\s,.-]+$/", $location)) {
        showAlert("error", "Invalid Location", "Location must contain only letters and numbers.");
    }

    $name        = sanitize($name);
    $description = sanitize($description);
    $location    = sanitize($location);

    try {
        $pdo->beginTransaction();

        // CORREGIDO: Ahora incluye user_id en el INSERT
        $stmt = $pdo->prepare("INSERT INTO productos (nombre, descripcion, precio, zona_referencia, id_categoria, user_id) 
                               VALUES (:name, :description, :price, :location, :category, :user_id)");
        $stmt->execute([
            ':name' => $name,
            ':description' => $description,
            ':price' => $price,
            ':location' => $location,
            ':category' => $category,
            ':user_id' => $userId  // NUEVO: Guardamos el ID del usuario
        ]);

        $product_id = $pdo->lastInsertId();

        if (!empty($_FILES['images']['name'][0])) {
            $totalFiles = count($_FILES['images']['name']);
            if ($totalFiles < 2 || $totalFiles > 10) {
                throw new Exception("You must upload between 2 and 10 images.");
            }

            $uploadDir = __DIR__ . "/uploads/";
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            for ($i = 0; $i < $totalFiles; $i++) {
                $fileTmp  = $_FILES['images']['tmp_name'][$i];
                $fileName = basename($_FILES['images']['name'][$i]);
                $fileExt  = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

                $allowed = ['jpg', 'jpeg', 'png', 'gif'];
                if (!in_array($fileExt, $allowed)) {
                    throw new Exception("Invalid image format. Allowed: JPG, JPEG, PNG, GIF.");
                }

                $newName = uniqid("img_") . "." . $fileExt;
                $uploadPath = $uploadDir . $newName;
                $dbPath = "uploads/" . $newName;

                if (!move_uploaded_file($fileTmp, $uploadPath)) {
                    throw new Exception("Error uploading image.");
                }

                $imgStmt = $pdo->prepare("INSERT INTO product_images (product_id, image_url) VALUES (:pid, :url)");
                $imgStmt->execute([
                    ':pid' => $product_id,
                    ':url' => $dbPath
                ]);

                // Guardar la primera imagen como imagen principal del producto
                if ($i == 0) {
                    $updateStmt = $pdo->prepare("UPDATE productos SET imagen = :imagen WHERE id = :id");
                    $updateStmt->execute([':imagen' => $dbPath, ':id' => $product_id]);
                }
            }
        } else {
            throw new Exception("You must upload at least 2 images.");
        }

        $pdo->commit();
        // CORREGIDO: Redirigir a my-items.php en lugar de index.php
        showAlert("success", "Success", "Product added successfully.", "/SWAPLY/users/my-items.php");
    } catch (Exception $e) {
        $pdo->rollBack();
        showAlert("error", "Error", $e->getMessage());
    }
}

$categories_query = "SELECT * FROM categorias ORDER BY nombre";
$categories_stmt = $pdo->prepare($categories_query);
$categories_stmt->execute();
$categories = $categories_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product - Swaply</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/add.css" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'swaply-blue': '#3B82F6',
                        'swaply-green': '#10B981',
                        'swaply-light-blue': '#EBF4FF',
                        'swaply-light-green': '#ECFDF5'
                    }
                }
            }
        }
    </script>
</head>

<body>
    <header class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex-shrink-0">
                    <a href="../index.php" class="flex items-center space-x-2 hover:opacity-80 transition-opacity">
                        <div class="w-8 h-8 bg-gradient-to-r from-swaply-blue to-swaply-green rounded-lg flex items-center justify-center">
                            <i class="fas fa-exchange-alt text-white text-sm"></i>
                        </div>
                        <span class="text-xl font-bold text-gray-900">Swaply</span>
                    </a>
                </div>

                <nav class="hidden md:flex items-center space-x-8">
                    <a href="market.php" class="flex items-center space-x-2 text-gray-600 hover:text-swaply-blue font-medium transition-colors">
                        <i class="fas fa-store text-sm"></i>
                        <span>Marketplace</span>
                    </a>
                    <a href="/SWAPLY/users/my-items.php" class="flex items-center space-x-2 text-gray-600 hover:text-swaply-blue font-medium transition-colors">
                        <i class="fas fa-box text-sm"></i>
                        <span>My Items</span>
                    </a>
                </nav>

                <div class="flex items-center space-x-4">
                    <?php if (isset($_SESSION['user'])): ?>
                        <div class="flex items-center space-x-3">
                            <div class="relative group">
                                <button class="flex items-center space-x-2 hover:bg-gray-50 rounded-lg px-3 py-2 transition-colors">
                                    <div class="w-8 h-8 bg-swaply-blue rounded-full flex items-center justify-center text-white">
                                        <span class="text-sm font-bold">
                                            <?php echo strtoupper(substr($_SESSION['user']['first_name'] ?? 'U', 0, 1)); ?>
                                        </span>
                                    </div>
                                    <span class="hidden md:block text-sm font-medium text-gray-700">
                                        <?php echo htmlspecialchars($_SESSION['user']['first_name'] ?? 'User'); ?>
                                    </span>
                                    <i class="fas fa-chevron-down text-gray-400 text-xs"></i>
                                </button>

                                <div class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                                    <div class="py-2">
                                        <a href="/SWAPLY/users/my-items.php" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                            <i class="fas fa-box mr-3 text-gray-400"></i>
                                            My Items
                                        </a>
                                        <hr class="my-2 border-gray-200">
                                        <a href="/SWAPLY/users/logout.php" class="flex items-center px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                            <i class="fas fa-sign-out-alt mr-3 text-red-400"></i>
                                            Logout
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="flex items-center space-x-3">
                            <a href="/SWAPLY/users/login.php" class="text-gray-600 hover:text-swaply-blue font-medium text-sm transition-colors">
                                Login
                            </a>
                            <a href="/SWAPLY/users/register.php" class="bg-swaply-green hover:bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                Register
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>

    <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="form-container">
            <div class="gradient-header">
                <h1 class="text-4xl font-bold text-white mb-3">Add New Product</h1>
                <p class="text-xl text-white opacity-90">Turn your unused items into treasures for others</p>
            </div>

            <div class="p-8 lg:p-12">
                <form method="POST" enctype="multipart/form-data" class="space-y-8">
                    <div class="grid md:grid-cols-2 gap-8">
                        <div>
                            <label for="name" class="block text-sm font-semibold text-foreground mb-3">
                                Product Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="name" name="name" required maxlength="150"
                                class="form-input"
                                placeholder="e.g., iPhone 13 Pro Max">
                        </div>

                        <div>
                            <label for="price" class="block text-sm font-semibold text-foreground mb-3">
                                Price (USD) <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute left-4 top-4 text-muted font-semibold">$</span>
                                <input type="number" id="price" name="price" required min="0.01" step="0.01"
                                    class="form-input pl-10"
                                    placeholder="0.00">
                            </div>
                        </div>
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-semibold text-foreground mb-3">
                            Description <span class="text-red-500">*</span>
                        </label>
                        <textarea id="description" name="description" required maxlength="500" rows="5"
                            class="form-input resize-none"
                            placeholder="Describe your product in detail... What makes it special?"></textarea>
                        <p class="text-sm text-muted mt-2">Maximum 500 characters</p>
                    </div>

                    <div class="grid md:grid-cols-2 gap-8">
                        <div>
                            <label for="location" class="block text-sm font-semibold text-foreground mb-3">
                                Meeting Location <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="location" name="location" required maxlength="150"
                                class="form-input"
                                placeholder="e.g., San Salvador, Santa Ana">
                        </div>

                        <div>
                            <label for="category" class="block text-sm font-semibold text-foreground mb-3">
                                Category <span class="text-red-500">*</span>
                            </label>
                            <select id="category" name="category" required class="form-input">
                                <option value="">Select a category</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?php echo $category['id']; ?>">
                                        <?php echo htmlspecialchars($category['nombre']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-foreground mb-3">
                            Product Images <span class="text-red-500">*</span>
                        </label>
                        <p class="text-muted mb-6">Upload 2-10 high-quality images to showcase your product</p>

                        <div class="dropzone" id="dropzone">
                            <div class="text-center">
                                <div class="inline-flex items-center justify-center w-16 h-16 bg-primary bg-opacity-10 rounded-full mb-4">
                                    <i class="fas fa-cloud-upload-alt text-2xl text-primary"></i>
                                </div>
                                <h3 class="text-xl font-semibold text-foreground mb-2">Drag & drop images here</h3>
                                <p class="text-muted mb-6">or click to browse your files</p>
                                <div class="inline-flex items-center px-6 py-3 bg-primary text-white rounded-lg hover:opacity-90 transition-all font-semibold">
                                    <i class="fas fa-plus mr-2"></i>
                                    Choose Images
                                </div>
                            </div>
                        </div>

                        <input type="file" id="images" name="images[]" accept="image/*" multiple hidden>
                        <div class="preview-container" id="preview"></div>

                        <div class="mt-4 p-4 bg-blue-50 rounded-lg border border-blue-200">
                            <div class="flex items-start space-x-3">
                                <i class="fas fa-info-circle text-blue-500 mt-0.5"></i>
                                <div class="text-sm text-blue-700">
                                    <p class="font-medium mb-1">Image Guidelines:</p>
                                    <ul class="space-y-1 text-blue-600">
                                        <li>• Supported formats: JPG, JPEG, PNG, GIF</li>
                                        <li>• Maximum 10MB per image</li>
                                        <li>• Upload 2-10 images for best results</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-6 pt-8 border-t border-border">
                        <button type="submit" class="btn-primary flex-1 text-center">
                            <i class="fas fa-rocket mr-2"></i>Publish Product
                        </button>
                        <a href="/SWAPLY/index.php" class="btn-secondary flex-1 text-center">
                            <i class="fas fa-home mr-2"></i>Go to Home
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <script>
        const dropzone = document.getElementById("dropzone");
        const fileInput = document.getElementById("images");
        const preview = document.getElementById("preview");
        let filesList = [];

        dropzone.addEventListener("click", () => fileInput.click());

        dropzone.addEventListener("dragover", (e) => {
            e.preventDefault();
            dropzone.classList.add("dragover");
        });

        dropzone.addEventListener("dragleave", () => {
            dropzone.classList.remove("dragover");
        });

        dropzone.addEventListener("drop", (e) => {
            e.preventDefault();
            dropzone.classList.remove("dragover");
            handleFiles(e.dataTransfer.files);
        });

        fileInput.addEventListener("change", () => handleFiles(fileInput.files));

        function handleFiles(newFiles) {
            Array.from(newFiles).forEach(file => {
                if (filesList.length >= 10) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Maximum Reached',
                        text: 'You can upload maximum 10 images.',
                        confirmButtonColor: '#4f83cc'
                    });
                    return;
                }
                if (!file.type.startsWith("image/")) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Invalid File',
                        text: 'Please select only image files.',
                        confirmButtonColor: '#4f83cc'
                    });
                    return;
                }
                if (file.size > 10 * 1024 * 1024) {
                    Swal.fire({
                        icon: 'error',
                        title: 'File Too Large',
                        text: 'Each image must be less than 10MB.',
                        confirmButtonColor: '#4f83cc'
                    });
                    return;
                }
                filesList.push(file);
            });
            updatePreview();
        }

        function updatePreview() {
            preview.innerHTML = "";
            filesList.forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = e => {
                    const div = document.createElement("div");
                    div.className = "preview-item";
                    div.innerHTML = `
                        <img src="${e.target.result}" alt="Preview">
                        <button type="button" class="remove-btn" onclick="removeImage(${index})">
                            <i class="fas fa-times"></i>
                        </button>
                    `;
                    preview.appendChild(div);
                };
                reader.readAsDataURL(file);
            });

            const dataTransfer = new DataTransfer();
            filesList.forEach(f => dataTransfer.items.add(f));
            fileInput.files = dataTransfer.files;

            if (filesList.length > 0) {
                dropzone.innerHTML = `
                    <div class="text-center">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-green-100 rounded-full mb-4">
                            <i class="fas fa-images text-2xl text-green-600"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-foreground mb-2">${filesList.length} image(s) selected</h3>
                        <p class="text-muted mb-6">Click to add more or drag & drop additional images</p>
                        <div class="inline-flex items-center px-6 py-3 bg-primary text-white rounded-lg hover:opacity-90 transition-all font-semibold">
                            <i class="fas fa-plus mr-2"></i>
                            Add More Images
                        </div>
                    </div>
                `;
            }
        }

        function removeImage(index) {
            filesList.splice(index, 1);
            updatePreview();

            if (filesList.length === 0) {
                dropzone.innerHTML = `
                    <div class="text-center">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-primary bg-opacity-10 rounded-full mb-4">
                            <i class="fas fa-cloud-upload-alt text-2xl text-primary"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-foreground mb-2">Drag & drop images here</h3>
                        <p class="text-muted mb-6">or click to browse your files</p>
                        <div class="inline-flex items-center px-6 py-3 bg-primary text-white rounded-lg hover:opacity-90 transition-all font-semibold">
                            <i class="fas fa-plus mr-2"></i>
                            Choose Images
                        </div>
                    </div>
                `;
            }
        }

        document.querySelector("form").addEventListener("submit", (e) => {
            if (filesList.length < 2 || filesList.length > 10) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Images Required',
                    text: 'You must upload between 2 and 10 images.',
                    confirmButtonColor: '#4f83cc'
                });
            }
        });
    </script>
</body>

</html>