<?php
session_start();
require_once '../Config/db.php';

// VERIFICAR QUE EL USUARIO ESTÉ LOGUEADO
if (!isset($_SESSION['user'])) {
    header('Location: /SWAPLY/users/login.php');
    exit;
}

$userId = $_SESSION['user']['id'];

// Handle AJAX actions
if (isset($_POST['action'])) {
    header('Content-Type: application/json');

    if ($_POST['action'] === 'remove') {
        $productId = (int)$_POST['product_id'];
        $stmt = $pdo->prepare("DELETE FROM wishlist WHERE user_id = :user_id AND product_id = :product_id");
        $success = $stmt->execute(['user_id' => $userId, 'product_id' => $productId]);
        echo json_encode(['success' => $success]);
        exit;
    }

    if ($_POST['action'] === 'clear') {
        $stmt = $pdo->prepare("DELETE FROM wishlist WHERE user_id = :user_id");
        $success = $stmt->execute(['user_id' => $userId]);
        echo json_encode(['success' => $success]);
        exit;
    }

    if ($_POST['action'] === 'add') {
        $productId = (int)$_POST['product_id'];
        // Check if already exists
        $check = $pdo->prepare("SELECT id FROM wishlist WHERE user_id = :user_id AND product_id = :product_id");
        $check->execute(['user_id' => $userId, 'product_id' => $productId]);

        if (!$check->fetch()) {
            $stmt = $pdo->prepare("INSERT INTO wishlist (user_id, product_id, fecha_agregado) VALUES (:user_id, :product_id, NOW())");
            $success = $stmt->execute(['user_id' => $userId, 'product_id' => $productId]);
            echo json_encode(['success' => $success, 'added' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Already in your wishlist']);
        }
        exit;
    }
}

// Get wishlist products for the logged user - INCLUYE IMÁGENES
$query = "SELECT p.*, c.nombre as category_name,
          w.fecha_agregado as added_date,
          (SELECT image_url FROM product_images WHERE product_id = p.id ORDER BY id ASC LIMIT 1) as primera_imagen
          FROM productos p 
          LEFT JOIN categorias c ON p.id_categoria = c.id 
          INNER JOIN wishlist w ON p.id = w.product_id AND w.user_id = :user_id
          ORDER BY w.fecha_agregado DESC";
$stmt = $pdo->prepare($query);
$stmt->execute(['user_id' => $userId]);
$wishlistItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Count total items
$totalItems = count($wishlistItems);

// Get suggested products (if wishlist is empty or to show after) - INCLUYE IMÁGENES
$suggestedItems = [];
if (empty($wishlistItems)) {
    $suggestedQuery = "SELECT p.*, c.nombre as category_name,
                      (SELECT image_url FROM product_images WHERE product_id = p.id ORDER BY id ASC LIMIT 1) as primera_imagen
                       FROM productos p 
                       LEFT JOIN categorias c ON p.id_categoria = c.id 
                       ORDER BY p.fecha_publicacion DESC 
                       LIMIT 8";
    $suggestedStmt = $pdo->prepare($suggestedQuery);
    $suggestedStmt->execute();
    $suggestedItems = $suggestedStmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    // If there are items, show related products (same category)
    $categories = array_unique(array_column($wishlistItems, 'id_categoria'));
    if (!empty($categories)) {
        $placeholders = implode(',', array_fill(0, count($categories), '?'));
        $suggestedQuery = "SELECT p.*, c.nombre as category_name,
                          (SELECT image_url FROM product_images WHERE product_id = p.id ORDER BY id ASC LIMIT 1) as primera_imagen
                           FROM productos p 
                           LEFT JOIN categorias c ON p.id_categoria = c.id 
                           WHERE p.id_categoria IN ($placeholders) 
                           AND p.id NOT IN (SELECT product_id FROM wishlist WHERE user_id = ?)
                           ORDER BY p.fecha_publicacion DESC 
                           LIMIT 4";
        $params = array_merge($categories, [$userId]);
        $suggestedStmt = $pdo->prepare($suggestedQuery);
        $suggestedStmt->execute($params);
        $suggestedItems = $suggestedStmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>My Wishlist - Swaply</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="../assets/css/wishlist.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>

    <!-- HEADER -->
    <div class="wishlist-header">
        <div class="header-content">
            <a href="../index.php" class="header-logo">
                <i class="fas fa-exchange-alt"></i> Swaply
            </a>
            <div class="header-icons">
            </div>
        </div>
    </div>

    <div class="wishlist-container">

        <!-- BREADCRUMB -->
        <div class="wishlist-breadcrumb">
            <a href="../index.php">Home</a> <i class="fas fa-chevron-right"></i>
            <span>My Wishlist</span>
        </div>

        <!-- TITLE AND STATISTICS -->
        <div class="wishlist-title-section">
            <div class="title-left">
                <h1>
                    <i class="fas fa-heart" style="color: #ff4d6d;"></i>
                    My Wishlist
                    <?php if ($totalItems > 0): ?>
                        <span class="item-count-badge"><?= $totalItems ?></span>
                    <?php endif; ?>
                </h1>
                <p class="wishlist-stats">
                    <span class="stat-item"><i class="fas fa-box"></i> <span id="totalItems"><?= $totalItems ?></span> items</span>
                </p>
            </div>
            <div class="title-right">
                <?php if (!empty($wishlistItems)): ?>

                    
                    
                    <button class="btn-clear-wishlist" onclick="clearWishlist()">
                        <i class="fas fa-trash-alt"></i> Clear all
                    </button>
                <?php endif; ?>
            </div>
        </div>

        <!-- MAIN CONTENT -->
        <div id="wishlistContent">
            <?php if (!empty($wishlistItems)): ?>
                <!-- WISHLIST PRODUCTS GRID -->
                <div class="wishlist-grid" id="wishlistGrid">
                    <?php foreach ($wishlistItems as $item): ?>
                        <?php
                        // Usar la imagen de product_images si existe, si no usar la imagen principal
                        $imageToShow = $item['primera_imagen'] ?? $item['imagen'] ?? null;
                        $imagePath = __DIR__ . "/../products/" . $imageToShow;
                        $imageExists = $imageToShow && file_exists($imagePath) && !is_dir($imagePath);
                        ?>
                        <div class="wishlist-card" data-id="<?= $item['id'] ?>" data-price="<?= $item['precio'] ?>" data-date="<?= isset($item['added_date']) ? strtotime($item['added_date']) : time() ?>">
                            <div class="wishlist-card-image">
                                <a href="../products/view.php?id=<?= $item['id'] ?>">
                                    <?php if ($imageExists): ?>
                                        <img src="../products/<?= htmlspecialchars($imageToShow) ?>" alt="<?= htmlspecialchars($item['nombre']) ?>">
                                    <?php else: ?>
                                        <div class="no-image">
                                            <i class="fas fa-box-open fa-3x"></i>
                                        </div>
                                    <?php endif; ?>
                                </a>

                                <!-- Remove button -->
                                <button class="btn-remove-wishlist" onclick="removeFromWishlist(<?= $item['id'] ?>, event)">
                                    <i class="fas fa-times"></i>
                                </button>

                                <!-- New badge -->
                                <?php if (time() - strtotime($item['fecha_publicacion']) < 7 * 24 * 60 * 60): ?>
                                    <span class="badge-new">NEW</span>
                                <?php endif; ?>
                            </div>

                            <div class="wishlist-card-info">
                                <h3 class="wishlist-product-title">
                                    <a href="../products/view.php?id=<?= $item['id'] ?>"><?= htmlspecialchars($item['nombre']) ?></a>
                                </h3>

                                <div class="wishlist-product-price">
                                    $<?= number_format($item['precio'], 2) ?>
                                </div>

                                <div class="wishlist-product-meta">
                                    <span class="meta-item">
                                        <i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($item['zona_referencia']) ?>
                                    </span>
                                    <span class="meta-item">
                                        <i class="far fa-clock"></i>
                                        <?php if (isset($item['added_date'])): ?>
                                            <?= date('m/d/Y', strtotime($item['added_date'])) ?>
                                        <?php else: ?>
                                            <?= date('m/d/Y', strtotime($item['fecha_publicacion'])) ?>
                                        <?php endif; ?>
                                    </span>
                                </div>

                                <div class="stock-status in-stock">
                                    <i class="fas fa-check-circle"></i> Available
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <!-- EMPTY WISHLIST - Show suggestions -->
                <div class="empty-wishlist">
                    <div class="empty-icon">
                        <i class="fas fa-heart-broken"></i>
                    </div>
                    <h2>Your wishlist is empty</h2>
                    <p>Save items you're interested in and they'll appear here</p>
                    <a href="../index.php" class="btn-browse-products">
                        <i class="fas fa-compass"></i> Browse products
                    </a>
                </div>

                <?php if (!empty($suggestedItems)): ?>
                    <div class="suggested-section">
                        <h3><i class="fas fa-lightbulb"></i> You might also like</h3>
                        <div class="suggested-grid">
                            <?php foreach ($suggestedItems as $suggested): ?>
                                <?php
                                $suggestedImage = $suggested['primera_imagen'] ?? $suggested['imagen'] ?? null;
                                $suggestedPath = __DIR__ . "/../products/" . $suggestedImage;
                                $suggestedExists = $suggestedImage && file_exists($suggestedPath) && !is_dir($suggestedPath);
                                ?>
                                <div class="suggested-card">
                                    <div class="suggested-image">
                                        <a href="../products/view.php?id=<?= $suggested['id'] ?>">
                                            <?php if ($suggestedExists): ?>
                                                <img src="../products/<?= htmlspecialchars($suggestedImage) ?>" alt="<?= htmlspecialchars($suggested['nombre']) ?>">
                                            <?php else: ?>
                                                <i class="fas fa-box-open fa-3x"></i>
                                            <?php endif; ?>
                                        </a>
                                        <button class="btn-add-to-wishlist-suggested" onclick="addToWishlist(<?= $suggested['id'] ?>)">
                                            <i class="far fa-heart"></i>
                                        </button>
                                    </div>
                                    <div class="suggested-info">
                                        <h4><a href="../products/view.php?id=<?= $suggested['id'] ?>"><?= htmlspecialchars($suggested['nombre']) ?></a></h4>
                                        <p class="suggested-price">$<?= number_format($suggested['precio'], 2) ?></p>
                                        <p class="suggested-location">
                                            <i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($suggested['zona_referencia']) ?>
                                        </p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>

        <!-- FLOATING SCROLL TO TOP BUTTON -->
        <button id="scrollToTop" class="scroll-top-btn" onclick="scrollToTop()">
            <i class="fas fa-arrow-up"></i>
        </button>

    </div>

    <!-- FOOTER -->
    <div class="wishlist-footer">
        <div class="footer-content">
            <p>&copy; 2026 Swaply Marketplace. All rights reserved.</p>
        </div>
    </div>

    <!-- SHARE MODAL -->
    <div id="shareModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeShareModal()">&times;</span>
            <h3><i class="fas fa-share-alt"></i> Share wishlist</h3>
            <p>Share this list with your friends:</p>
            <div class="share-link-container">
                <input type="text" id="shareLink" value="<?= 'http://' . $_SERVER['HTTP_HOST'] . '/SWAPLY/products/wishlist.php' ?>" readonly>
                <button onclick="copyShareLink()">Copy</button>
            </div>
            <div class="share-social">
                <button class="social-btn facebook" onclick="shareOnFacebook()"><i class="fab fa-facebook-f"></i> Facebook</button>
                <button class="social-btn twitter" onclick="shareOnTwitter()"><i class="fab fa-twitter"></i> Twitter</button>
                <button class="social-btn whatsapp" onclick="shareOnWhatsApp()"><i class="fab fa-whatsapp"></i> WhatsApp</button>
            </div>
        </div>
    </div>

    <script>
        // Global variables
        let currentFilter = 'all';
        let currentSort = 'recent';
        let itemToDelete = null;

        // Function to remove from wishlist with confirmation
        function removeFromWishlist(productId, event) {
            if (event) event.stopPropagation();

            if (!confirm('⚠️ Are you sure you want to remove this item from your wishlist?')) {
                return;
            }

            fetch('wishlist.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'action=remove&product_id=' + productId
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const card = document.querySelector(`.wishlist-card[data-id="${productId}"]`);
                        if (card) {
                            card.style.animation = 'fadeOut 0.3s ease';
                            setTimeout(() => {
                                card.remove();
                                updateTotalItems();

                                if (document.querySelectorAll('.wishlist-card').length === 0) {
                                    location.reload();
                                }
                            }, 300);
                        }
                        showNotification('Item removed from wishlist', 'success');
                    }
                });
        }

        // Function to clear entire wishlist with confirmation
        function clearWishlist() {
            if (!confirm('⚠️ Are you sure you want to REMOVE ALL items from your wishlist? This action cannot be undone.')) {
                return;
            }

            fetch('wishlist.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'action=clear'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    }
                });
        }

        // Function to add to wishlist from suggestions
        function addToWishlist(productId) {
            fetch('wishlist.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'action=add&product_id=' + productId
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification('✨ Added to your wishlist! Refreshing...', 'success');
                        setTimeout(() => {
                            location.reload();
                        }, 1500);
                    } else {
                        showNotification(data.message || 'Error adding item', 'error');
                    }
                });
        }

        // Function to update item counter
        function updateTotalItems() {
            const totalSpan = document.getElementById('totalItems');
            if (totalSpan) {
                const currentCount = document.querySelectorAll('.wishlist-card').length;
                totalSpan.textContent = currentCount;
            }
        }

        // Function to show notifications
        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `notification ${type}`;
            notification.innerHTML = `
                <i class="fas ${type === 'success' ? 'fa-check-circle' : type === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle'}"></i>
                <span>${message}</span>
            `;
            document.body.appendChild(notification);

            setTimeout(() => {
                notification.style.animation = 'slideOut 0.3s ease';
                setTimeout(() => notification.remove(), 300);
            }, 3000);
        }

        // Function to share
        function shareWishlist() {
            document.getElementById('shareModal').style.display = 'block';
        }

        function closeShareModal() {
            document.getElementById('shareModal').style.display = 'none';
        }

        function copyShareLink() {
            const link = document.getElementById('shareLink');
            link.select();
            document.execCommand('copy');
            showNotification('Link copied!', 'success');
        }

        function shareOnFacebook() {
            const url = encodeURIComponent(document.getElementById('shareLink').value);
            window.open(`https://www.facebook.com/sharer/sharer.php?u=${url}`, '_blank');
        }

        function shareOnTwitter() {
            const url = encodeURIComponent(document.getElementById('shareLink').value);
            const text = encodeURIComponent('Check out my wishlist on Swaply');
            window.open(`https://twitter.com/intent/tweet?text=${text}&url=${url}`, '_blank');
        }

        function shareOnWhatsApp() {
            const url = encodeURIComponent(document.getElementById('shareLink').value);
            const text = encodeURIComponent('Check out my wishlist on Swaply: ');
            window.open(`https://wa.me/?text=${text}${url}`, '_blank');
        }

        // Scroll to top button
        window.onscroll = function() {
            const btn = document.getElementById('scrollToTop');
            if (document.body.scrollTop > 500 || document.documentElement.scrollTop > 500) {
                btn.style.display = 'block';
            } else {
                btn.style.display = 'none';
            }
        };

        function scrollToTop() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('shareModal');
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }

        // Add fade out animation
        const style = document.createElement('style');
        style.textContent = `
            @keyframes fadeOut {
                from { opacity: 1; transform: scale(1); }
                to { opacity: 0; transform: scale(0.8); }
            }
            @keyframes slideOut {
                from { transform: translateX(0); opacity: 1; }
                to { transform: translateX(100%); opacity: 0; }
            }
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
        `;
        document.head.appendChild(style);
    </script>

</body>

</html>