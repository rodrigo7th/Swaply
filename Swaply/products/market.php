<?php
session_start();
require_once(__DIR__ . "/../Config/db.php");

try {
    $pdo = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

$categoriesQuery = "SELECT id, nombre FROM categorias ORDER BY nombre";
$categoriesStmt = $pdo->prepare($categoriesQuery);
$categoriesStmt->execute();
$categories = $categoriesStmt->fetchAll(PDO::FETCH_ASSOC);

$productsQuery = "
    SELECT 
        p.id,
        p.nombre,
        p.descripcion,
        p.precio,
        p.zona_referencia,
        c.nombre as categoria,
        p.fecha_publicacion,
        string_agg(pi.image_url, ', ') as imagenes
    FROM productos p
    LEFT JOIN categorias c ON p.id_categoria = c.id
    LEFT JOIN product_images pi ON p.id = pi.product_id
    GROUP BY p.id, p.nombre, p.descripcion, p.precio, p.zona_referencia, c.nombre, p.fecha_publicacion
    ORDER BY p.fecha_publicacion DESC
";

$productsStmt = $pdo->prepare($productsQuery);
$productsStmt->execute();
$products = $productsStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Marketplace - Swaply</title>


</head>
<link href="../assets/css/market.css" rel="stylesheet">
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

<body class="bg-gray-50">
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
                    <a href="wishlist.php" class="flex items-center space-x-2 text-gray-600 hover:text-swaply-blue font-medium transition-colors">
                        <i class="fas fa-th-large text-sm"></i>
                        <span>Wishlist</span>
                    </a>
                    <a href="deals.php" class="flex items-center space-x-2 text-gray-600 hover:text-swaply-blue font-medium transition-colors">
                        <i class="fas fa-tags text-sm"></i>
                        <span>Memberships</span>
                    </a>
                </nav>

                <div class="flex items-center space-x-4">
                    <?php if (isset($_SESSION['user'])): ?>

                        <div class="flex items-center space-x-3">

                            <div class="relative">
                                <button class="p-2 text-gray-600 hover:text-swaply-blue hover:bg-gray-50 rounded-lg transition-colors">
                                    <i class="fas fa-bell text-lg"></i>
                            </div>


                            <div class="relative">
                                <button class="p-2 text-gray-600 hover:text-swaply-blue hover:bg-gray-50 rounded-lg transition-colors">
                                    <i class="fas fa-envelope text-lg"></i>
                                </button>
                            </div>

                            <a href="add.php" class="bg-swaply-green hover:bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors flex items-center space-x-2">
                                <i class="fas fa-plus text-sm"></i>
                                <span class="hidden lg:inline">Add Item</span>
                            </a>


                            <div class="relative group">
                                <button class="flex items-center space-x-2 hover:bg-gray-50 rounded-lg px-3 py-2 transition-colors">
                                    <img
                                        src="<?php echo isset($_SESSION['user']['avatar']) && !empty($_SESSION['user']['avatar']) ? $_SESSION['user']['avatar'] : '/placeholder.svg?height=32&width=32'; ?>"
                                        alt="User Avatar"
                                        class="w-8 h-8 rounded-full object-cover border-2 border-gray-200">
                                    <span class="hidden md:block text-sm font-medium text-gray-700">
                                        <?php echo htmlspecialchars($_SESSION['user']['first_name'] ?? 'User'); ?>
                                    </span>
                                    <i class="fas fa-chevron-down text-gray-400 text-xs"></i>
                                </button>


                                <div class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-lg border border-gray-200 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                                    <div class="py-2">

                                        <div class="px-4 py-2 border-b border-gray-100">
                                            <p class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($_SESSION['user']['first_name'] . ' ' . $_SESSION['user']['last_name'] ?? 'User'); ?></p>
                                            <p class="text-xs text-gray-500">Reputation: ⭐ 4.8 (127 swaps)</p>
                                        </div>


                                        <a href="../users/profile.php" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                            <i class="fas fa-user mr-3 text-gray-400"></i>
                                            My Profile
                                        </a>
                                        <a href="../users/dashboard.php" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                            <i class="fas fa-tachometer-alt mr-3 text-gray-400"></i>
                                            Dashboard
                                        </a>
                                        <a href="../users/settings.php" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                            <i class="fas fa-cog mr-3 text-gray-400"></i>
                                            Settings
                                        </a>

                                        <hr class="my-2 border-gray-200">


                                        <a href="my-items.php" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                            <i class="fas fa-box mr-3 text-gray-400"></i>
                                            My Items
                                        </a>
                                        <a href="favorites.php" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                            <i class="fas fa-star mr-3 text-gray-400"></i>
                                            Favorites
                                        </a>

                                        <hr class="my-2 border-gray-200">


                                        <a href="swap-history.php" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                            <i class="fas fa-history mr-3 text-gray-400"></i>
                                            Swap History
                                        </a>
                                        <a href="reviews.php" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                            <i class="fas fa-comment-alt mr-3 text-gray-400"></i>
                                            Reviews
                                        </a>
                                        <a href="wallet.php" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                            <i class="fas fa-wallet mr-3 text-gray-400"></i>
                                            Swap Credits
                                        </a>

                                        <hr class="my-2 border-gray-200">


                                        <a href="help.php" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                            <i class="fas fa-question-circle mr-3 text-gray-400"></i>
                                            Help & Support
                                        </a>
                                        <a href="logout.php" class="flex items-center px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                            <i class="fas fa-sign-out-alt mr-3 text-red-400"></i>
                                            Logout
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>

                        <div class="flex items-center space-x-3">
                            <a
                                href="users/login.php"
                                class="text-gray-600 hover:text-swaply-blue font-medium text-sm transition-colors">
                                Login
                            </a>
                            <a
                                href="users/register.php"
                                class="bg-swaply-green hover:bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-swaply-green focus:ring-offset-2">
                                Register
                            </a>
                        </div>
                    <?php endif; ?>


                    <button class="md:hidden p-2 text-gray-600 hover:text-swaply-blue hover:bg-gray-50 rounded-lg transition-colors" onclick="toggleMobileMenu()">
                        <i class="fas fa-bars text-lg"></i>
                    </button>
                </div>
            </div>


            <div id="mobileMenu" class="md:hidden hidden border-t border-gray-200 py-4">
                <nav class="flex flex-col space-y-3">
                    <a href="../marketplace" class="flex items-center space-x-2 text-gray-600 hover:text-swaply-blue font-medium transition-colors px-2 py-1">
                        <i class="fas fa-store text-sm"></i>
                        <span>Marketplace</span>
                    </a>
                    <a href="categories.php" class="flex items-center space-x-2 text-gray-600 hover:text-swaply-blue font-medium transition-colors px-2 py-1">
                        <i class="fas fa-th-large text-sm"></i>
                        <span>Categories</span>
                    </a>
                    <a href="trending.php" class="flex items-center space-x-2 text-gray-600 hover:text-swaply-blue font-medium transition-colors px-2 py-1">
                        <i class="fas fa-fire text-sm"></i>
                        <span>Trending</span>
                    </a>
                    <a href="deals.php" class="flex items-center space-x-2 text-gray-600 hover:text-swaply-blue font-medium transition-colors px-2 py-1">
                        <i class="fas fa-tags text-sm"></i>
                        <span>Best Deals</span>
                    </a>
                </nav>
            </div>
        </div>
    </header>


    <script>
        function toggleMobileMenu() {
            const mobileMenu = document.getElementById('mobileMenu');
            mobileMenu.classList.toggle('hidden');
        }
    </script>

    <body>
        <div class="container">
            <div class="header">
                <h1>Swaply Marketplace</h1>
                <p>Discover thousands of unique products from our trading community</p>

            </div>

            <div class="search-section">
                <div class="search-bar">
                    <input type="text" class="search-input" placeholder="Search products, brands, categories..." id="searchInput">
                    <button class="search-btn" onclick="searchProducts()">
                        🔍 Search
                    </button>
                    <div class="categories-dropdown">
                        <button class="categories-btn" onclick="toggleCategoriesDropdown()">
                            Categories ▼
                        </button>
                        <div class="categories-dropdown-menu" id="categoriesDropdown">
                            <div class="dropdown-category active" onclick="selectCategory('all', 'All Categories')">All Categories</div>
                            <?php
                            foreach ($categories as $category):
                            ?>
                                <div class="dropdown-category" onclick="selectCategory('<?php echo strtolower(str_replace(' ', '-', $category['nombre'])); ?>', '<?php echo htmlspecialchars($category['nombre']); ?>')" data-category-id="<?php echo $category['id']; ?>">
                                    <?php echo htmlspecialchars($category['nombre']); ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="results-info">
                <span id="resultsCount">Showing 24 products</span>
            </div>

            <div class="products-grid" id="productsGrid">

            </div>
        </div>

        <script>
            const products = <?php echo json_encode(array_map(function ($product) {
                                    $images = !empty($product['imagenes']) ? explode(',', $product['imagenes']) : [];
                                    return [
                                        'id' => $product['id'],
                                        'title' => $product['nombre'],
                                        'description' => $product['descripcion'],
                                        'price' => floatval($product['precio']),
                                        'location' => $product['zona_referencia'],
                                        'category' => strtolower(str_replace(' ', '-', $product['categoria'])),
                                        'categoryName' => $product['categoria'],
                                        'images' => $images,
                                        'date' => $product['fecha_publicacion']
                                    ];
                                }, $products)); ?>;

            const categories = <?php echo json_encode($categories); ?>;

            let filteredProducts = [...products];
            let currentCategory = 'all';
            let currentCategoryName = 'All Categories';

            function renderProducts(productsToRender = filteredProducts) {
                const grid = document.getElementById('productsGrid');
                const resultsCount = document.getElementById('resultsCount');

                resultsCount.textContent = `Showing ${productsToRender.length} products`;

                grid.innerHTML = productsToRender.map(product => {
                    let imageHtml = '';
                    if (product.images && product.images.length > 0) {
                        imageHtml = `<img src="${product.images[0]}" alt="${product.title}" onerror="this.parentElement.innerHTML='📦'; this.parentElement.classList.add('no-image');">`;
                    } else {
                        imageHtml = '📦';
                    }

                    return `
                    <div class="product-card" onclick="viewProduct(${product.id})">
                        <div class="product-image ${product.images && product.images.length > 0 ? '' : 'no-image'}">
                            ${imageHtml}
                        </div>
                        <div class="product-info">
                            <div class="product-title">${product.title}</div>
                            <div class="product-price">$${product.price}</div>
                            <div class="product-condition">Available</div>
                            <div class="product-rating">
                            </div>
                            <div class="product-location">📍 ${product.location}</div>
                            <div style="color: #6b7280; font-size: 0.8rem; margin-top: 0.5rem;">
                                Category: ${product.categoryName}
                            </div>
                        </div>
                    </div>
                `;
                }).join('');
            }

            function toggleCategoriesDropdown() {
                const dropdown = document.getElementById('categoriesDropdown');
                dropdown.classList.toggle('show');
            }

            function selectCategory(category, categoryName) {
                currentCategory = category;
                currentCategoryName = categoryName;

                document.querySelectorAll('.dropdown-category').forEach(item => {
                    item.classList.remove('active');
                });
                event.target.classList.add('active');

                document.querySelector('.categories-btn').innerHTML = `${categoryName} ▼`;

                if (category === 'all') {
                    filteredProducts = [...products];
                } else {
                    filteredProducts = products.filter(product => product.category === category);
                }

                document.getElementById('categoriesDropdown').classList.remove('show');

                renderProducts();
            }

            function searchProducts() {
                const searchTerm = document.getElementById('searchInput').value.toLowerCase();

                if (searchTerm === '') {
                    filteredProducts = currentCategory === 'all' ? [...products] : products.filter(p => p.category === currentCategory);
                } else {
                    const baseProducts = currentCategory === 'all' ? products : products.filter(p => p.category === currentCategory);
                    filteredProducts = baseProducts.filter(product =>
                        product.title.toLowerCase().includes(searchTerm) ||
                        product.description.toLowerCase().includes(searchTerm) ||
                        product.location.toLowerCase().includes(searchTerm) ||
                        product.categoryName.toLowerCase().includes(searchTerm)
                    );
                }

                renderProducts();
            }

            function viewProduct(productId) {
                window.location.href = "view.php?id=" + productId;
            }
            document.getElementById('searchInput').addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    searchProducts();
                }
            });

            document.addEventListener('click', function(e) {
                if (!e.target.closest('.categories-dropdown')) {
                    document.getElementById('categoriesDropdown').classList.remove('show');
                }
            });

            renderProducts();
        </script>
    </body>

</html>