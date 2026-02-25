<?php
session_start();
require_once __DIR__ . '/../Config/db.php';

// VERIFICAR QUE EL USUARIO ESTÉ LOGUEADO
if (!isset($_SESSION['user'])) {
    header('Location: /SWAPLY/users/login.php');
    exit;
}

$userId = $_SESSION['user']['id'];

// Handle AJAX actions for delete
if (isset($_POST['action'])) {
    header('Content-Type: application/json');

    if ($_POST['action'] === 'delete') {
        $productId = (int)$_POST['product_id'];

        // Verificar que el producto pertenece al usuario
        $check = $pdo->prepare("SELECT id FROM productos WHERE id = :id AND user_id = :user_id");
        $check->execute(['id' => $productId, 'user_id' => $userId]);

        if ($check->fetch()) {
            $stmt = $pdo->prepare("DELETE FROM productos WHERE id = :id AND user_id = :user_id");
            $success = $stmt->execute(['id' => $productId, 'user_id' => $userId]);
            echo json_encode(['success' => $success]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Producto no encontrado o no te pertenece']);
        }
        exit;
    }
}

// Obtener SOLO los productos del usuario actual
$query = "SELECT p.*, c.nombre as category_name,
          (SELECT COUNT(*) FROM wishlist WHERE product_id = p.id) as wishlist_count
          FROM productos p 
          LEFT JOIN categorias c ON p.id_categoria = c.id 
          WHERE p.user_id = :user_id
          ORDER BY p.fecha_publicacion DESC";
$stmt = $pdo->prepare($query);
$stmt->execute(['user_id' => $userId]);
$myItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Estadísticas
$totalItems = count($myItems);
$totalViews = array_sum(array_column($myItems, 'vistas') ?: [0]);
$activeItems = count(array_filter($myItems, function ($item) {
    return $item['precio'] > 0;
}));
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>My Items - Swaply</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="/SWAPLY/assets/css/my-items.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>

    <!-- HEADER -->
    <div class="myitems-header">
        <div class="header-content">
            <a href="/SWAPLY/index.php" class="header-logo">
                <i class="fas fa-exchange-alt"></i> Swaply
            </a>
            <div class="header-user">
                <span class="user-greeting">
                    <i class="fas fa-user-circle"></i> 
                    <?= htmlspecialchars($_SESSION['user']['first_name'] . ' ' . $_SESSION['user']['last_name']) ?>
                </span>
            </div>
        </div>
    </div>

    <div class="myitems-container">

        <!-- BREADCRUMB -->
        <div class="myitems-breadcrumb">
            <a href="/SWAPLY/index.php">Home</a> <i class="fas fa-chevron-right"></i>
            <span>My Items</span>
        </div>

        <!-- TITLE AND STATISTICS -->
        <div class="myitems-title-section">
            <div class="title-left">
                <h1>
                    <i class="fas fa-store" style="color: #6ab0fe;"></i>
                    My Items
                    <?php if ($totalItems > 0): ?>
                        <span class="item-count-badge"><?= $totalItems ?></span>
                    <?php endif; ?>
                </h1>
                <div class="myitems-stats">
                    <span class="stat-item"><i class="fas fa-box"></i> <span id="totalItems"><?= $totalItems ?></span> items</span>
                    <span class="stat-item"><i class="fas fa-eye"></i> <?= number_format($totalViews) ?> total views</span>
                    <span class="stat-item"><i class="fas fa-check-circle" style="color: #4caf50;"></i> <?= $activeItems ?> active</span>
                </div>
            </div>
            <div class="title-right">
                <a href="/SWAPLY/products/add.php" class="btn-add-new">
                    <i class="fas fa-plus"></i> Add new item
                </a>
            </div>
        </div>

        <!-- QUICK FILTERS (only if there are items) -->
        <?php if (!empty($myItems)): ?>
            <div class="myitems-filters">
                <button class="filter-btn active" onclick="filterItems('all')">All items</button>
                <button class="filter-btn" onclick="filterItems('active')">Active</button>
                <button class="filter-btn" onclick="filterItems('sold')">Sold</button>
            </div>
        <?php endif; ?>

        <!-- MAIN CONTENT -->
        <div id="myitemsContent">
            <?php if (!empty($myItems)): ?>
                <!-- ITEMS GRID -->
                <div class="myitems-grid" id="myitemsGrid">
                    <?php foreach ($myItems as $item): ?>
                        <?php 
                        $itemImage = $item['imagen'] ?? null;
                        $imagePath = __DIR__ . "/../products/uploads/" . $itemImage;
                        $imageExists = $itemImage && file_exists($imagePath) && !is_dir($imagePath);
                        ?>
                        <div class="myitems-card" data-id="<?= $item['id'] ?>"
                            data-price="<?= $item['precio'] ?>"
                            data-views="<?= $item['vistas'] ?? 0 ?>"
                            data-date="<?= strtotime($item['fecha_publicacion']) ?>"
                            data-status="<?= ($item['precio'] > 0) ? 'active' : 'sold' ?>">

                            <div class="myitems-card-image">
                                <a href="/SWAPLY/products/view.php?id=<?= $item['id'] ?>">
                                    <?php if ($imageExists): ?>
                                        <img src="/SWAPLY/products/uploads/<?= htmlspecialchars($itemImage) ?>" alt="<?= htmlspecialchars($item['nombre']) ?>">
                                    <?php else: ?>
                                        <div class="no-image">
                                            <i class="fas fa-box-open fa-3x"></i>
                                        </div>
                                    <?php endif; ?>
                                </a>

                                <!-- Status badge -->
                                <?php if ($item['precio'] > 0): ?>
                                    <span class="status-badge active">ACTIVE</span>
                                <?php else: ?>
                                    <span class="status-badge sold">SOLD</span>
                                <?php endif; ?>

                                <!-- Quick actions dropdown -->
                                <div class="item-actions-dropdown">
                                    <button class="actions-toggle" onclick="toggleActions(<?= $item['id'] ?>, event)">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <div class="actions-menu" id="actions-<?= $item['id'] ?>">
                                        <a href="/SWAPLY/products/edit.php?id=<?= $item['id'] ?>" class="action-item">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <a href="/SWAPLY/products/view.php?id=<?= $item['id'] ?>" class="action-item">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                        <button class="action-item delete" onclick="deleteItem(<?= $item['id'] ?>, event)">
                                            <i class="fas fa-trash-alt"></i> Delete
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="myitems-card-info">
                                <h3 class="myitems-product-title">
                                    <a href="/SWAPLY/products/view.php?id=<?= $item['id'] ?>"><?= htmlspecialchars($item['nombre']) ?></a>
                                </h3>

                                <div class="myitems-product-price">
                                    $<?= number_format($item['precio'], 2) ?>
                                </div>

                                <div class="myitems-product-meta">
                                    <span class="meta-item">
                                        <i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($item['zona_referencia'] ?? 'No location') ?>
                                    </span>
                                    <span class="meta-item">
                                        <i class="far fa-calendar"></i> <?= date('M d, Y', strtotime($item['fecha_publicacion'])) ?>
                                    </span>
                                </div>

                                <div class="myitems-stats-row">
                                    <span class="stat">
                                        <i class="fas fa-eye"></i> <?= number_format($item['vistas'] ?? 0) ?> views
                                    </span>
                                    <span class="stat">
                                        <i class="fas fa-heart" style="color: #ff4d6d;"></i> <?= $item['wishlist_count'] ?? 0 ?>
                                    </span>
                                </div>

                                <div class="myitems-card-actions">
                                    <button class="btn-mark-sold" onclick="markAsSold(<?= $item['id'] ?>)">
                                        <i class="fas fa-check-circle"></i> Mark as sold
                                    </button>
                                    <button class="btn-promote" onclick="promoteItem(<?= $item['id'] ?>)">
                                        <i class="fas fa-rocket"></i> Promote
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <!-- NO ITEMS - Show empty state -->
                <div class="empty-myitems">
                    <div class="empty-icon">
                        <i class="fas fa-store-alt"></i>
                    </div>
                    <h2>You haven't listed any items yet</h2>
                    <p>Start selling by adding your first product</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- BOTÓN FLOTANTE PARA VOLVER ARRIBA -->
        <button id="scrollToTop" class="scroll-top-btn" onclick="scrollToTop()">
            <i class="fas fa-arrow-up"></i>
        </button>

    </div>

    <!-- FOOTER -->
    <div class="myitems-footer">
        <div class="footer-content">
            <p>&copy; 2026 Swaply Marketplace. All rights reserved.</p>
            <div class="footer-links">
                <a href="/SWAPLY/terms.php">Terms</a>
                <a href="/SWAPLY/privacy.php">Privacy</a>
                <a href="/SWAPLY/help.php">Help</a>
            </div>
        </div>
    </div>

    <!-- DELETE CONFIRMATION MODAL -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeDeleteModal()">&times;</span>
            <div class="modal-icon">
                <i class="fas fa-exclamation-triangle" style="color: #ff4d6d; font-size: 3rem;"></i>
            </div>
            <h3>Delete item?</h3>
            <p>Are you sure you want to delete this item? This action cannot be undone.</p>
            <div class="modal-actions">
                <button class="modal-btn cancel" onclick="closeDeleteModal()">Cancel</button>
                <button class="modal-btn delete" id="confirmDeleteBtn">Delete</button>
            </div>
        </div>
    </div>

    <script>
        // Global variables
        let currentFilter = 'all';
        let currentSort = 'recent';
        let itemToDelete = null;

        // Toggle actions menu
        function toggleActions(itemId, event) {
            event.stopPropagation();
            const menu = document.getElementById(`actions-${itemId}`);
            menu.classList.toggle('show');

            // Close other open menus
            document.querySelectorAll('.actions-menu.show').forEach(m => {
                if (m.id !== `actions-${itemId}`) {
                    m.classList.remove('show');
                }
            });
        }

        // Close menus when clicking outside
        document.addEventListener('click', function() {
            document.querySelectorAll('.actions-menu.show').forEach(menu => {
                menu.classList.remove('show');
            });
        });

        // Function to delete item with confirmation
        function deleteItem(productId, event) {
            event.stopPropagation();
            itemToDelete = productId;
            document.getElementById('deleteModal').style.display = 'block';
        }

        // Confirm delete
        document.getElementById('confirmDeleteBtn')?.addEventListener('click', function() {
            if (!itemToDelete) return;

            fetch('/SWAPLY/users/my-items.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'action=delete&product_id=' + itemToDelete
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const card = document.querySelector(`.myitems-card[data-id="${itemToDelete}"]`);
                        if (card) {
                            card.style.animation = 'fadeOut 0.3s ease';
                            setTimeout(() => {
                                card.remove();
                                updateTotalItems();

                                // If no items left, reload to show empty state
                                if (document.querySelectorAll('.myitems-card').length === 0) {
                                    location.reload();
                                }
                            }, 300);
                        }
                        showNotification('Item deleted successfully', 'success');
                        closeDeleteModal();
                    } else {
                        showNotification('Error deleting item', 'error');
                    }
                });
        });

        function closeDeleteModal() {
            document.getElementById('deleteModal').style.display = 'none';
            itemToDelete = null;
        }

        // Function to mark as sold
        function markAsSold(productId) {
            if (!confirm('Mark this item as sold? It will be moved to "Sold" section.')) return;
            showNotification('Item marked as sold', 'success');
        }

        // Function to promote item
        function promoteItem(productId) {
            showNotification('Promotion feature coming soon!', 'info');
        }

        // Function to filter items
        function filterItems(filter) {
            currentFilter = filter;
            document.querySelectorAll('.filter-btn').forEach(btn => btn.classList.remove('active'));
            event.target.classList.add('active');

            const cards = document.querySelectorAll('.myitems-card');
            cards.forEach(card => {
                if (filter === 'all') {
                    card.style.display = 'block';
                } else if (filter === 'active') {
                    card.style.display = card.dataset.status === 'active' ? 'block' : 'none';
                } else if (filter === 'sold') {
                    card.style.display = card.dataset.status === 'sold' ? 'block' : 'none';
                }
            });
        }

        // Function to sort items
        function sortItems(sortBy) {
            currentSort = sortBy;
            const grid = document.getElementById('myitemsGrid');
            const cards = Array.from(document.querySelectorAll('.myitems-card'));

            cards.sort((a, b) => {
                if (sortBy === 'price_asc') {
                    return parseFloat(a.dataset.price) - parseFloat(b.dataset.price);
                } else if (sortBy === 'price_desc') {
                    return parseFloat(b.dataset.price) - parseFloat(a.dataset.price);
                } else if (sortBy === 'recent') {
                    return parseInt(b.dataset.date) - parseInt(a.dataset.date);
                } else if (sortBy === 'views') {
                    return parseInt(b.dataset.views) - parseInt(a.dataset.views);
                }
            });

            cards.forEach(card => grid.appendChild(card));
        }

        // Function to update total items counter
        function updateTotalItems() {
            const totalSpan = document.getElementById('totalItems');
            if (totalSpan) {
                const currentCount = document.querySelectorAll('.myitems-card').length;
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
            const modal = document.getElementById('deleteModal');
            if (event.target == modal) {
                modal.style.display = 'none';
                itemToDelete = null;
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
        `;
        document.head.appendChild(style);
    </script>

</body>

</html>