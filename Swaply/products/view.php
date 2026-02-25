<?php
session_start();
require_once '../Config/db.php';

// MANEJAR PETICIONES AJAX PRIMERO
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    // Verificar si el usuario está logueado
    if (!isset($_SESSION['user'])) {
        echo json_encode(['success' => false, 'message' => 'Por favor inicia sesión']);
        exit;
    }
    
    $userId = $_SESSION['user']['id'];
    $productId = (int)$_POST['product_id'];
    $action = $_POST['action'];
    
    try {
        if ($action === 'add') {
            // Verificar si ya existe
            $check = $pdo->prepare("SELECT id FROM wishlist WHERE user_id = :user_id AND product_id = :product_id");
            $check->execute(['user_id' => $userId, 'product_id' => $productId]);
            
            if (!$check->fetch()) {
                $stmt = $pdo->prepare("INSERT INTO wishlist (user_id, product_id, fecha_agregado) VALUES (:user_id, :product_id, NOW())");
                $success = $stmt->execute(['user_id' => $userId, 'product_id' => $productId]);
                echo json_encode(['success' => $success, 'action' => 'added']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Ya está en tu wishlist']);
            }
            exit;
        }
        
        if ($action === 'remove') {
            $stmt = $pdo->prepare("DELETE FROM wishlist WHERE user_id = :user_id AND product_id = :product_id");
            $success = $stmt->execute(['user_id' => $userId, 'product_id' => $productId]);
            echo json_encode(['success' => $success, 'action' => 'removed']);
            exit;
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error en la base de datos']);
        exit;
    }
}

// SI NO ES PETICIÓN AJAX, CONTINUAR CON LA PÁGINA NORMAL
if (!isset($_GET['id'])) {
    die("Product not found.");
}

$productId = (int)$_GET['id'];

// Verificar si el producto está en wishlist del usuario (si está logueado)
$inWishlist = false;
if (isset($_SESSION['user'])) {
    $userId = $_SESSION['user']['id'];
    $checkWishlist = $pdo->prepare("SELECT id FROM wishlist WHERE user_id = :user_id AND product_id = :product_id");
    $checkWishlist->execute(['user_id' => $userId, 'product_id' => $productId]);
    $inWishlist = $checkWishlist->fetch() ? true : false;
}

try {
    // Actualizar vistas
    $updateViews = $pdo->prepare("UPDATE productos SET vistas = COALESCE(vistas, 0) + 1 WHERE id = :id");
    $updateViews->execute(['id' => $productId]);
} catch (PDOException $e) {
    error_log("Could not update views: " . $e->getMessage());
}

// Obtener información del producto
$query = "SELECT p.*, c.nombre as category_name 
          FROM productos p 
          LEFT JOIN categorias c ON p.id_categoria = c.id 
          WHERE p.id = :id";
$stmt = $pdo->prepare($query);
$stmt->execute(['id' => $productId]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    die("Product not found.");
}

// Obtener TODAS las imágenes del producto desde product_images
$imagesQuery = "SELECT image_url FROM product_images WHERE product_id = :product_id ORDER BY id ASC";
$imagesStmt = $pdo->prepare($imagesQuery);
$imagesStmt->execute(['product_id' => $productId]);
$productImages = $imagesStmt->fetchAll(PDO::FETCH_ASSOC);

// Si no hay imágenes en product_images, usar la imagen principal del producto
if (empty($productImages) && !empty($product['imagen'])) {
    $productImages = [['image_url' => $product['imagen']]];
}

$formattedDate = date('Y-m-d', strtotime($product['fecha_publicacion']));

// Get related products (same category, excluding current)
$relatedQuery = "SELECT p.*, c.nombre as category_name 
                 FROM productos p 
                 LEFT JOIN categorias c ON p.id_categoria = c.id 
                 WHERE p.id_categoria = :category AND p.id != :id 
                 ORDER BY p.fecha_publicacion DESC 
                 LIMIT 4";
$relatedStmt = $pdo->prepare($relatedQuery);
$relatedStmt->execute(['category' => $product['id_categoria'], 'id' => $productId]);
$relatedProducts = $relatedStmt->fetchAll(PDO::FETCH_ASSOC);

// Google Maps configuration
$googleMapsApiKey = 'YOUR_GOOGLE_MAPS_API_KEY'; // Replace with your API key

// If no coordinates saved, geocode the address
if (empty($product['latitud']) || empty($product['longitud'])) {
    $address = urlencode($product['zona_referencia']);
    $geocodeUrl = "https://maps.googleapis.com/maps/api/geocode/json?address={$address}&key={$googleMapsApiKey}";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $geocodeUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $response = curl_exec($ch);
    curl_close($ch);
    
    $geocodeData = json_decode($response, true);
    
    if (!empty($geocodeData['results'][0])) {
        $lat = $geocodeData['results'][0]['geometry']['location']['lat'];
        $lng = $geocodeData['results'][0]['geometry']['location']['lng'];
        
        // Save coordinates to database
        $updateStmt = $pdo->prepare("UPDATE productos SET latitud = :lat, longitud = :lng WHERE id = :id");
        $updateStmt->execute(['lat' => $lat, 'lng' => $lng, 'id' => $productId]);
        
        $product['latitud'] = $lat;
        $product['longitud'] = $lng;
    }
}

$views = isset($product['vistas']) ? $product['vistas'] : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($product['nombre']) ?> - Swaply Marketplace</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="../assets/css/view.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Google Maps JavaScript API -->
    <script src="https://maps.googleapis.com/maps/api/js?key=<?= $googleMapsApiKey ?>&libraries=places"></script>
</head>
<body>

<!-- HEADER -->
<div class="marketplace-header">
    <div class="header-content">
        <a href="../index.php" class="header-logo">
            <i class="fas fa-exchange-alt"></i> Swaply
        </a>
        <!-- BACK TO HOME BUTTON -->
        <a href="../index.php" class="back-home-btn">
            <i class="fas fa-arrow-left"></i> Back to Home
        </a>
    </div>
</div>

<div class="marketplace-container">

    <!-- NAVIGATION -->
    <div class="marketplace-nav">
        <a href="#" class="nav-item active"><i class="fas fa-home"></i> Home</a>
        <a href="wishlist.php" class="nav-item"><i class="fas fa-heart"></i> Favorites</a>
    </div>

    <!-- BREADCRUMB -->
    <div class="breadcrumb">
        <a href="../index.php">Home</a> <i class="fas fa-chevron-right"></i>
        <a href="../market.php">Market</a> <i class="fas fa-chevron-right"></i>
        <span><?= htmlspecialchars($product['category_name'] ?? 'Product') ?></span>
    </div>

    <!-- MAIN CONTAINER -->
    <div class="product-main-container">

        <!-- LEFT COLUMN: IMAGE + MAP -->
        <div class="product-gallery-column">
            <!-- Main image display -->
            <div class="main-image-container" id="mainImageContainer">
                <?php 
                $mainImage = !empty($productImages) ? $productImages[0]['image_url'] : null;
                $imagePath = __DIR__ . "/" . $mainImage;
                if ($mainImage && file_exists($imagePath)): 
                ?>
                    <img src="<?= htmlspecialchars($mainImage) ?>" alt="Product" class="main-product-image" id="mainProductImage">
                <?php else: ?>
                    <div class="no-image-main">
                        <i class="fas fa-box-open fa-5x"></i>
                        <p>No image available</p>
                    </div>
                <?php endif; ?>
                
                <!-- Condition badge -->
                <span class="condition-badge">Like new</span>
                
                <!-- Image action buttons -->
                <div class="image-actions">
                    <button class="image-action-btn <?= $inWishlist ? 'active' : '' ?>" id="wishlistBtn" onclick="toggleWishlist(<?= $productId ?>)">
                        <i class="<?= $inWishlist ? 'fas' : 'far' ?> fa-heart"></i>
                    </button>
                    <button class="image-action-btn"><i class="fas fa-share-alt"></i></button>
                </div>
            </div>
            
            <!-- Thumbnails - Mostrar todas las imágenes -->
            <?php if (!empty($productImages) && count($productImages) > 1): ?>
            <div class="thumbnail-row">
                <?php foreach ($productImages as $index => $img): ?>
                    <?php 
                    $thumbPath = __DIR__ . "/" . $img['image_url'];
                    if (file_exists($thumbPath)): 
                    ?>
                    <div class="thumbnail <?= $index === 0 ? 'active' : '' ?>" onclick="changeMainImage('<?= htmlspecialchars($img['image_url']) ?>', this)">
                        <img src="<?= htmlspecialchars($img['image_url']) ?>" alt="Thumb">
                    </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <!-- LOCATION MAP -->
            <?php if (!empty($product['latitud']) && !empty($product['longitud'])): ?>
            <div class="location-section">
                <div class="location-header">
                    <i class="fas fa-map-pin" style="color: #e41e3f;"></i>
                    <h3>Location</h3>
                    <span class="location-distance">2.5 km away</span>
                </div>
                <p class="location-address"><?= htmlspecialchars($product['zona_referencia']) ?></p>
                <div id="map" class="location-map"></div>
                <script>
                    function initMap() {
                        const location = { 
                            lat: <?= $product['latitud'] ?>, 
                            lng: <?= $product['longitud'] ?> 
                        };
                        
                        const map = new google.maps.Map(document.getElementById('map'), {
                            zoom: 14,
                            center: location,
                            mapTypeId: 'roadmap',
                            mapTypeControl: false,
                            streetViewControl: false,
                            fullscreenControl: true
                        });
                        
                        const marker = new google.maps.Marker({
                            position: location,
                            map: map,
                            title: '<?= htmlspecialchars($product['zona_referencia']) ?>',
                            animation: google.maps.Animation.DROP
                        });
                    }
                    
                    window.onload = initMap;
                </script>
                <p class="map-note"><i class="fas fa-info-circle"></i> Location is approximate</p>
            </div>
            <?php endif; ?>
            
            <!-- SHARE AND REPORT -->
            <div class="share-report">
                <a href="#"><i class="fas fa-share-alt"></i> Share</a>
                <a href="#"><i class="far fa-flag"></i> Report</a>
            </div>
        </div>

        <!-- RIGHT COLUMN: INFORMATION -->
        <div class="product-info-column">
            
            <!-- Title -->
            <h1 class="product-title-market"><?= htmlspecialchars($product['nombre']) ?></h1>
            
            <!-- Price -->
            <div class="product-price-market">$<?= number_format($product['precio'], 2) ?></div>
            
            <!-- Quick info -->
            <div class="quick-info">
                <div class="info-item">
                    <i class="fas fa-tag"></i>
                    <span><?= htmlspecialchars($product['category_name'] ?? 'Uncategorized') ?></span>
                </div>
                <div class="info-item">
                    <i class="far fa-calendar"></i>
                    <span>Posted <?= $formattedDate ?></span>
                </div>
                <div class="info-item">
                    <i class="fas fa-eye"></i>
                    <span><?= number_format($views) ?> views</span>
                </div>
            </div>
            
            <!-- SELLER -->
            <div class="seller-card">
                <div class="seller-info">
                    <div class="seller-avatar">
                        <i class="fas fa-user-circle fa-3x"></i>
                    </div>
                    <div class="seller-details">
                        <h4>Seller</h4>
                        <p><i class="fas fa-star" style="color: #ffc107;"></i> 4.8 (45 ratings)</p>
                        <p class="member-since">Member since 2024</p>
                    </div>
                </div>
                <div class="seller-actions">
                    <button class="btn-seller-profile">View profile</button>
                </div>
            </div>
            
            <!-- DESCRIPTION -->
            <div class="description-section">
                <h3><i class="fas fa-align-left"></i> Description</h3>
                <div class="description-text">
                    <?php if (!empty(trim($product['descripcion']))): ?>
                        <p><?= nl2br(htmlspecialchars($product['descripcion'])) ?></p>
                    <?php else: ?>
                        <p class="no-description-market"><i class="fas fa-info-circle"></i> The seller did not add a description.</p>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- OFFER BUTTON -->
            <div class="main-actions">
                <button class="btn-make-offer" onclick="openOfferModal()">
                    <i class="fas fa-gavel"></i> Make an offer
                </button>
            </div>
            
            <!-- QUESTIONS AND ANSWERS -->
            <div class="questions-section">
                <h3><i class="far fa-question-circle"></i> Questions & Answers</h3>
                            
                <!-- Ask question box -->
                <div class="ask-question">
                    <input type="text" id="questionInput" placeholder="Ask a question to the seller...">
                    <button onclick="sendQuestion()">Ask</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- RELATED PRODUCTS -->
    <?php if (!empty($relatedProducts)): ?>
    <div class="related-products">
        <h2>Related Products</h2>
        <div class="related-grid">
            <?php foreach ($relatedProducts as $related): ?>
                <?php 
                // Obtener primera imagen del producto relacionado
                $relatedImgQuery = "SELECT image_url FROM product_images WHERE product_id = :product_id LIMIT 1";
                $relatedImgStmt = $pdo->prepare($relatedImgQuery);
                $relatedImgStmt->execute(['product_id' => $related['id']]);
                $relatedImg = $relatedImgStmt->fetch(PDO::FETCH_ASSOC);
                $relatedImage = $relatedImg ? $relatedImg['image_url'] : ($related['imagen'] ?? null);
                ?>
                <a href="view.php?id=<?= $related['id'] ?>" class="related-card">
                    <div class="related-image">
                        <?php if ($relatedImage && file_exists(__DIR__ . "/" . $relatedImage)): ?>
                            <img src="<?= htmlspecialchars($relatedImage) ?>" alt="<?= htmlspecialchars($related['nombre']) ?>">
                        <?php else: ?>
                            <i class="fas fa-box-open fa-3x"></i>
                        <?php endif; ?>
                    </div>
                    <div class="related-info">
                        <h4><?= htmlspecialchars($related['nombre']) ?></h4>
                        <p class="related-price">$<?= number_format($related['precio'], 2) ?></p>
                        <p class="related-location"><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($related['zona_referencia']) ?></p>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- FOOTER -->
    <div class="marketplace-footer">
        <p>&copy; 2026 Swaply Marketplace. All rights reserved.</p>
    </div>
    
</div>

<!-- MESSAGE MODAL -->
<div id="messageModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeMessageModal()">&times;</span>
        <h3><i class="far fa-envelope"></i> Send message to seller</h3>
        <textarea id="messageText" placeholder="Write your message..." rows="4"></textarea>
        <button onclick="sendMessage()" class="modal-btn">Send message</button>
    </div>
</div>

<!-- OFFER MODAL -->
<div id="offerModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeOfferModal()">&times;</span>
        <h3><i class="fas fa-gavel"></i> Make an offer</h3>
        <div class="offer-input-group">
            <label>Your offer ($)</label>
            <input type="number" id="offerAmount" placeholder="0.00" step="0.01" min="1">
        </div>
        <textarea id="offerMessage" placeholder="Message to seller (optional)" rows="3"></textarea>
        <button onclick="sendOffer()" class="modal-btn">Send offer</button>
    </div>
</div>

<style>
/* Additional styles for the back button */
.back-home-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background-color: #f0f2f5;
    color: #1a1a1a;
    padding: 8px 16px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 500;
    transition: background-color 0.3s;
    margin-left: auto;
}

.back-home-btn:hover {
    background-color: #e4e6e9;
    color: #1a1a1a;
}

.header-content {
    display: flex;
    align-items: center;
    justify-content: space-between;
    width: 100%;
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
}

/* Estilo para thumbnails clickeables */
.thumbnail {
    cursor: pointer;
    transition: opacity 0.3s;
}

.thumbnail:hover {
    opacity: 0.8;
}

.thumbnail.active {
    border: 2px solid #3B82F6;
    opacity: 1;
}

/* Estilo para el botón de wishlist activo */
.image-action-btn.active {
    background-color: #ff4d6d;
    color: white;
}

.image-action-btn.active i {
    color: white;
}

/* Notification styles */
.notification {
    position: fixed;
    top: 20px;
    right: 20px;
    padding: 15px 25px;
    border-radius: 8px;
    color: white;
    font-weight: 500;
    z-index: 9999;
    animation: slideIn 0.3s ease;
}

.notification.success {
    background-color: #4caf50;
}

.notification.error {
    background-color: #ff4d6d;
}

.notification.info {
    background-color: #6ab0fe;
}

@keyframes slideIn {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

@keyframes slideOut {
    from {
        transform: translateX(0);
        opacity: 1;
    }
    to {
        transform: translateX(100%);
        opacity: 0;
    }
}
</style>

<script>
// Función para cambiar la imagen principal al hacer clic en thumbnail
function changeMainImage(imageUrl, thumbnailElement) {
    // Cambiar la imagen principal
    document.getElementById('mainProductImage').src = imageUrl;
    
    // Quitar clase active de todos los thumbnails
    document.querySelectorAll('.thumbnail').forEach(thumb => {
        thumb.classList.remove('active');
    });
    
    // Agregar clase active al thumbnail clickeado
    thumbnailElement.classList.add('active');
}

// Función para toggle wishlist (agregar/quitar de favoritos)
function toggleWishlist(productId) {
    <?php if (!isset($_SESSION['user'])): ?>
        // Si no está logueado, redirigir al login
        if (confirm('Please login to add items to your wishlist')) {
            window.location.href = '/SWAPLY/users/login.php';
        }
        return;
    <?php endif; ?>
    
    const btn = document.getElementById('wishlistBtn');
    const icon = btn.querySelector('i');
    const isInWishlist = icon.classList.contains('fas');
    
    // Determinar acción
    const action = isInWishlist ? 'remove' : 'add';
    
    // Mostrar indicador de carga
    btn.disabled = true;
    
    fetch(window.location.href, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'action=' + action + '&product_id=' + productId
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            if (action === 'add') {
                icon.classList.remove('far');
                icon.classList.add('fas');
                btn.classList.add('active');
                showNotification('Added to wishlist', 'success');
            } else {
                icon.classList.remove('fas');
                icon.classList.add('far');
                btn.classList.remove('active');
                showNotification('Removed from wishlist', 'success');
            }
        } else {
            showNotification(data.message || 'Error updating wishlist', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error connecting to server', 'error');
    })
    .finally(() => {
        btn.disabled = false;
    });
}

// Function to show notifications
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.innerHTML = message;
    document.body.appendChild(notification);

    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// Modal functions
function openMessageModal() {
    document.getElementById('messageModal').style.display = 'block';
}

function closeMessageModal() {
    document.getElementById('messageModal').style.display = 'none';
}

function openOfferModal() {
    document.getElementById('offerModal').style.display = 'block';
}

function closeOfferModal() {
    document.getElementById('offerModal').style.display = 'none';
}

// Send message function
function sendMessage() {
    const message = document.getElementById('messageText').value;
    if (message.trim() === '') {
        alert('Please write a message');
        return;
    }
    alert('Message sent to seller');
    closeMessageModal();
    document.getElementById('messageText').value = '';
}

// Send offer function
function sendOffer() {
    const amount = document.getElementById('offerAmount').value;
    if (amount === '' || amount <= 0) {
        alert('Please enter a valid amount');
        return;
    }
    alert('Offer sent to seller for $' + amount);
    closeOfferModal();
    document.getElementById('offerAmount').value = '';
    document.getElementById('offerMessage').value = '';
}

// Send question function
function sendQuestion() {
    const question = document.getElementById('questionInput').value;
    if (question.trim() === '') {
        alert('Please write your question');
        return;
    }
    alert('Question sent to seller');
    document.getElementById('questionInput').value = '';
}

// Close modals when clicking outside
window.onclick = function(event) {
    const messageModal = document.getElementById('messageModal');
    const offerModal = document.getElementById('offerModal');
    if (event.target == messageModal) {
        messageModal.style.display = 'none';
    }
    if (event.target == offerModal) {
        offerModal.style.display = 'none';
    }
}
</script>

</body>
</html>