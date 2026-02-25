<?php
session_start();
require_once __DIR__ . '/../config/db.php'; 

if (!isset($_SESSION['user'])) {
    die("You must log in to view your profile.");
}

$idPerfil = $_SESSION['user']['id'];

// Avatar upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['avatar'])) {
    $file = $_FILES['avatar'];
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if ($file['error'] === UPLOAD_ERR_OK) {
        if (in_array($file['type'], $allowedTypes)) {
            $newName = uniqid('avatar_') . "." . $ext;
            $uploadDir = __DIR__ . '/../assets/PerfilUp/'; 
            $uploadPath = $uploadDir . $newName;

            if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
                $urlPath = "/Swaply/assets/PerfilUp/" . $newName;  
                $stmt = $pdo->prepare("UPDATE usuarios SET avatar = :avatar WHERE id = :id");
                $stmt->execute(['avatar' => $urlPath, 'id' => $idPerfil]);
                $_SESSION['user']['avatar'] = $urlPath;

                echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
                      <script>
                        Swal.fire({
                          icon: 'success',
                          title: 'Profile picture updated!',
                          text: 'Your avatar was uploaded successfully.',
                          confirmButtonColor: '#3B82F6'
                        }).then(() => { window.location.href='profile.php'; });
                      </script>";
                exit;
            }
        } else {
            echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
                  <script>
                    Swal.fire({
                      icon: 'error',
                      title: 'Invalid file type',
                      text: 'Only JPG, PNG, GIF, and WEBP files are allowed.',
                      confirmButtonColor: '#d33'
                    }).then(() => { window.history.back(); });
                  </script>";
            exit;
        }
    }
}

// User info
$query = $pdo->prepare("SELECT id, nombre, apellido, email, telefono, reputacion, fecha_registro, avatar
                        FROM usuarios 
                        WHERE id = :id LIMIT 1");
$query->execute(['id' => $idPerfil]);
$usuario = $query->fetch(PDO::FETCH_ASSOC);

if (!$usuario) {
    die("User not found");
}

// Products for this user only
$productos = $pdo->prepare("
    SELECT DISTINCT ON (p.id) p.id, p.nombre, p.precio, p.zona_referencia, 
           COALESCE(i.image_url, '/placeholder.svg?height=200&width=200') AS image_url
    FROM productos p
    LEFT JOIN product_images i ON p.id = i.product_id
    WHERE p.id_usuario = :id_usuario
    ORDER BY p.id, i.id ASC
");
$productos->execute(['id_usuario' => $idPerfil]);
$listaProductos = $productos->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?php echo htmlspecialchars($usuario['nombre']); ?>'s Profile - Swaply</title>
  <script src="https://cdn.tailwindcss.com"></script>
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
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50 font-sans">

  <div class="max-w-5xl mx-auto mt-10 bg-white shadow-lg rounded-xl p-8">
    <div class="flex items-center space-x-6">
      <!-- Avatar upload -->
      <form method="POST" enctype="multipart/form-data" class="relative group w-24 h-24">
        <img 
          src="<?php echo htmlspecialchars($usuario['avatar'] ?? '/placeholder.svg?height=96&width=96'); ?>" 
          alt="Avatar" 
          class="w-24 h-24 rounded-full object-cover border-2 border-gray-300"
        >
        <label for="avatarUpload" 
               class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center text-white text-sm rounded-full opacity-0 group-hover:opacity-100 cursor-pointer">
          Select Photo
        </label>
        <input type="file" id="avatarUpload" name="avatar" class="hidden" accept="image/*" onchange="this.form.submit()">
      </form>

      <div>
        <h1 class="text-2xl font-bold text-swaply-blue">
          <?php echo htmlspecialchars($usuario['nombre'] . " " . $usuario['apellido']); ?>
        </h1>
        <p class="text-gray-500">Member since: <?php echo date("M d, Y", strtotime($usuario['fecha_registro'])); ?></p>
        <p class="text-yellow-500 font-semibold">Reputation: <?php echo number_format($usuario['reputacion'],1); ?> ⭐</p>
      </div>
    </div>

    <!-- Contact Info -->
    <div class="mt-6">
      <h2 class="text-xl font-semibold text-swaply-blue mb-3">Contact Information</h2>

      <!-- Email -->
      <div class="flex items-center space-x-2 mb-2">
        <p class="text-gray-700 font-medium">Email:</p>
        <span id="emailHidden" class="text-gray-500">********</span>
        <span id="emailReal" class="text-gray-700 hidden"><?php echo htmlspecialchars($usuario['email']); ?></span>
        <button onclick="toggleField('email')" type="button" class="ml-2 text-gray-500 hover:text-swaply-blue">
          <i id="icon-email" class="fas fa-eye"></i>
        </button>
      </div>

      <!-- Phone -->
      <div class="flex items-center space-x-2">
        <p class="text-gray-700 font-medium">Phone:</p>
        <span id="phoneHidden" class="text-gray-500">********</span>
        <span id="phoneReal" class="text-gray-700 hidden"><?php echo htmlspecialchars($usuario['telefono']); ?></span>
        <button onclick="toggleField('phone')" type="button" class="ml-2 text-gray-500 hover:text-swaply-blue">
          <i id="icon-phone" class="fas fa-eye"></i>
        </button>
      </div>
    </div>

    <!-- Buttons -->
    <div class="mt-6 flex space-x-4">
      <a href="editar_perfil.php" class="px-4 py-2 bg-swaply-green text-white rounded-lg shadow hover:bg-green-600">Edit Profile</a>
      <a href="mis_productos.php" class="px-4 py-2 bg-swaply-blue text-white rounded-lg shadow hover:bg-blue-900">My Products</a>
      <a href="logout.php" class="px-4 py-2 bg-red-600 text-white rounded-lg shadow hover:bg-red-800">Logout</a>
    </div>

    <!-- Products -->
    <div class="mt-10">
      <h2 class="text-xl font-semibold text-swaply-blue mb-4">Published Products</h2>
      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
        <?php if (count($listaProductos) > 0): ?>
          <?php foreach ($listaProductos as $p): ?>
            <div class="bg-swaply-light-blue rounded-lg shadow hover:shadow-lg p-4">
              <img src="<?php echo htmlspecialchars($p['image_url']); ?>" alt="Product" class="w-full h-40 object-cover rounded">
              <h3 class="mt-2 text-lg font-bold text-swaply-blue"><?php echo htmlspecialchars($p['nombre']); ?></h3>
              <p class="text-gray-600">$<?php echo number_format($p['precio'],2); ?></p>
              <p class="text-sm text-gray-500">Location: <?php echo htmlspecialchars($p['zona_referencia']); ?></p>
              <a href="producto.php?id=<?php echo $p['id']; ?>" class="mt-2 inline-block px-3 py-1 bg-swaply-green text-white rounded hover:bg-green-700">View Details</a>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <p class="text-gray-500">You have not published any products yet.</p>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <script>
    function toggleField(field) {
      const hidden = document.getElementById(field + "Hidden");
      const real = document.getElementById(field + "Real");
      const icon = document.getElementById("icon-" + field);

      if (real.classList.contains("hidden")) {
        hidden.classList.add("hidden");
        real.classList.remove("hidden");
        icon.classList.remove("fa-eye");
        icon.classList.add("fa-eye-slash");
      } else {
        hidden.classList.remove("hidden");
        real.classList.add("hidden");
        icon.classList.remove("fa-eye-slash");
        icon.classList.add("fa-eye");
      }
    }
  </script>
</body>
</html>
