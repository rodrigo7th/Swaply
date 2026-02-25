<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Swaply - Swap Your Stuff, Build Your Community</title>
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
            --accent: #a2d5ab;
            --border: #d1d5db;
        }
        
        .bg-background { background-color: var(--background); }
        .bg-card { background-color: var(--card); }
        .bg-primary { background-color: var(--primary); }
        .bg-secondary { background-color: var(--secondary); }
        .bg-accent { background-color: var(--accent); }
        .text-foreground { color: var(--foreground); }
        .text-primary { color: var(--primary); }
        .text-primary-foreground { color: var(--primary-foreground); }
        .border-border { border-color: var(--border); }
        
        .hero-gradient {
            background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
        }
    </style>
</head>
<body class="bg-background text-foreground">

<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - Swaply' : 'Swaply - Swap, Trade, Thrive'; ?></title>
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
    

<?php
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Swaply - Swap Your Stuff, Build Your Community</title>
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
            --accent: #a2d5ab;
            --border: #d1d5db;
        }
        
        .bg-background { background-color: var(--background); }
        .bg-card { background-color: var(--card); }
        .bg-primary { background-color: var(--primary); }
        .bg-secondary { background-color: var(--secondary); }
        .bg-accent { background-color: var(--accent); }
        .text-foreground { color: var(--foreground); }
        .text-primary { color: var(--primary); }
        .text-primary-foreground { color: var(--primary-foreground); }
        .border-border { border-color: var(--border); }
        
        .hero-gradient {
            background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
        }
    </style>
</head>
<body class="bg-background text-foreground">

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Swaply - Swap, Trade, Thrive</title>
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
    

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Swaply - Swap, Trade, Thrive</title>
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
                    <a href="index.php" class="flex items-center space-x-2 hover:opacity-80 transition-opacity">
                        <div class="w-8 h-8 bg-gradient-to-r from-swaply-blue to-swaply-green rounded-lg flex items-center justify-center">
                            <i class="fas fa-exchange-alt text-white text-sm"></i>
                        </div>
                        <span class="text-xl font-bold text-gray-900">Swaply</span>
                    </a>
                </div>

                
                <nav class="hidden md:flex items-center space-x-8">
                    <a href="products/market.php" class="flex items-center space-x-2 text-gray-600 hover:text-swaply-blue font-medium transition-colors">
                        <i class="fas fa-store text-sm"></i>
                        <span>Marketplace</span>
                    </a>
                    <a href="products/wishlist.php" class="flex items-center space-x-2 text-gray-600 hover:text-swaply-blue font-medium transition-colors">
                        <i class="fas fa-th-large text-sm"></i>
                        <span>Wishlist</span>
                    </a>
                    <a href="Membership.php" class="flex items-center space-x-2 text-gray-600 hover:text-swaply-blue font-medium transition-colors">
                        <i class="fas fa-tags text-sm"></i>
                        <span>Membership</span>
                    </a>
                </nav>

              
                <div class="flex items-center space-x-4">
                                        
                        <div class="flex items-center space-x-3">
                      
                            <div class="relative">
    <a href="messages/noti.php" class="p-2 text-gray-600 hover:text-swaply-blue hover:bg-gray-50 rounded-lg transition-colors">
        <i class="fas fa-bell text-lg"></i>
    </a>
</div>

<div class="relative">
    <a href="messages/inbox.php" class="p-2 text-gray-600 hover:text-swaply-blue hover:bg-gray-50 rounded-lg transition-colors">
        <i class="fas fa-envelope text-lg"></i>
    </a>
</div>


<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<div class="flex items-center space-x-4">
    <a href="products/add.php" class="bg-swaply-green hover:bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors flex items-center space-x-2">
        <i class="fas fa-plus text-sm"></i>
        <span class="hidden lg:inline">Add Item</span>
    </a>

    <?php if (isset($_SESSION['user'])): ?>
        <!-- Usuario logueado -->
        <div class="relative group">
            <button class="flex items-center space-x-2 hover:bg-gray-50 rounded-lg px-3 py-2 transition-colors">
                <img 
                    src="<?php echo $_SESSION['user']['avatar'] ?? '/placeholder.svg?height=32&width=32'; ?>" 
                    alt="User Avatar" 
                    class="w-8 h-8 rounded-full object-cover border-2 border-gray-200"
                >
                <span class="hidden md:block text-sm font-medium text-gray-700">
                    <?php echo htmlspecialchars($_SESSION['user']['first_name']); ?>
                </span>
                <i class="fas fa-chevron-down text-gray-400 text-xs"></i>
            </button>

            <!-- Dropdown -->
            <div class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-lg border border-gray-200 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                <div class="py-2">
                    <div class="px-4 py-2 border-b border-gray-100">
                        <p class="text-sm font-medium text-gray-900">
                            <?php echo htmlspecialchars($_SESSION['user']['first_name'] . " " . $_SESSION['user']['last_name']); ?>
                        </p>
                        <p class="text-xs text-gray-500"><?php echo htmlspecialchars($_SESSION['user']['email']); ?></p>
                    </div>

                    <a href="users/profile.php" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                        <i class="fas fa-user mr-3 text-gray-400"></i>
                        My Profile
                    </a>
                    <a href="users/dashboard.php" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                        <i class="fas fa-tachometer-alt mr-3 text-gray-400"></i>
                        Dashboard
                    </a>
                    <a href="users/settings.php" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                        <i class="fas fa-cog mr-3 text-gray-400"></i>
                        Settings
                    </a>

                    <hr class="my-2 border-gray-200">

                    <a href="users/my-items.php" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                        <i class="fas fa-box mr-3 text-gray-400"></i>
                        My Items
                    </a>
                    <a href="favorites.php" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                        <i class="fas fa-star mr-3 text-gray-400"></i>
                        Favorites
                    </a>
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
                    <a href="users/logout.php" class="flex items-center px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors">
                        <i class="fas fa-sign-out-alt mr-3 text-red-400"></i>
                        Logout
                    </a>
                </div>
            </div>
        </div>
    <?php else: ?>
        <!-- Invitado -->
        <a href="users/login.php" class="bg-swaply-blue hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
            Sign In
        </a>
        <a href="users/register.php" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded-lg text-sm font-medium transition-colors">
            Register
        </a>
    <?php endif; ?>
</div>

                    
                    
                    <button class="md:hidden p-2 text-gray-600 hover:text-swaply-blue hover:bg-gray-50 rounded-lg transition-colors" onclick="toggleMobileMenu()">
                        <i class="fas fa-bars text-lg"></i>
                    </button>
                </div>
            </div>

           
            <div id="mobileMenu" class="md:hidden hidden border-t border-gray-200 py-4">
                <nav class="flex flex-col space-y-3">
                    <a href="marketplace.php" class="flex items-center space-x-2 text-gray-600 hover:text-swaply-blue font-medium transition-colors px-2 py-1">
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


 
    <section class="hero-gradient text-white py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-5xl md:text-6xl font-bold mb-6">
                Swap Your Stuff,<br>Build Your Community
            </h1>
            <p class="text-xl md:text-2xl mb-8 opacity-90 max-w-3xl mx-auto">
                Turn your unused items into treasures for others. Join thousands of swappers building reputation through sustainable trading.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="#how-it-works" class="border-2 border-white text-white px-8 py-4 rounded-lg text-lg font-semibold hover:bg-white hover:text-primary transition-all">
                    Learn How
                </a>
            </div>
        </div>
    </section>

  
    <section id="how-it-works" class="py-20 bg-card">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-foreground mb-4">How Swaply Works</h2>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                    Three simple steps to start swapping and building your reputation in our community
                </p>
            </div>
            
            <div class="grid md:grid-cols-3 gap-8">
                <div class="text-center p-8 bg-background rounded-xl border border-border">
                    <div class="bg-primary text-primary-foreground w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-6 text-2xl">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <h3 class="text-2xl font-semibold mb-4 text-foreground">1. Sign Up</h3>
                    <p class="text-gray-600">
                        Create your free account and set up your profile. Tell us about yourself and what you're interested in swapping.
                    </p>
                </div>
                
                <div class="text-center p-8 bg-background rounded-xl border border-border">
                    <div class="bg-secondary text-foreground w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-6 text-2xl">
                        <i class="fas fa-camera"></i>
                    </div>
                    <h3 class="text-2xl font-semibold mb-4 text-foreground">2. Post Products</h3>
                    <p class="text-gray-600">
                        Upload photos and descriptions of items you want to swap. Set your preferences for what you'd like in return.
                    </p>
                </div>
                
                <div class="text-center p-8 bg-background rounded-xl border border-border">
                    <div class="bg-accent text-foreground w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-6 text-2xl">
                        <i class="fas fa-star"></i>
                    </div>
                    <h3 class="text-2xl font-semibold mb-4 text-foreground">3. Swap & Build Reputation</h3>
                    <p class="text-gray-600">
                        Connect with other swappers, make trades, and build your reputation through successful exchanges.
                    </p>
                </div>
            </div>
        </div>
    </section>

  

ç
    <section id="community" class="py-20 bg-secondary">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-4xl font-bold text-foreground mb-4">Why People Trust Swaply</h2>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto mb-12">
                Secure, transparent, and community-driven. Swaply is your safe place to trade and give your items a new life.
            </p>
            <div class="grid md:grid-cols-3 gap-8">
                <div class="bg-card p-8 rounded-xl shadow-lg border border-border">
                    <i class="fas fa-lock text-swaply-blue text-3xl mb-4"></i>
                    <h4 class="font-semibold text-foreground mb-2">Secure Trades</h4>
                    <p class="text-gray-600">Your reputation ensures safe and fair transactions.</p>
                </div>
                <div class="bg-card p-8 rounded-xl shadow-lg border border-border">
                    <i class="fas fa-recycle text-swaply-green text-3xl mb-4"></i>
                    <h4 class="font-semibold text-foreground mb-2">Eco-Friendly</h4>
                    <p class="text-gray-600">Swapping reduces waste and promotes sustainability.</p>
                </div>
                <div class="bg-card p-8 rounded-xl shadow-lg border border-border">
                    <i class="fas fa-users text-yellow-500 text-3xl mb-4"></i>
                    <h4 class="font-semibold text-foreground mb-2">Community</h4>
                    <p class="text-gray-600">Connect with thousands of trusted swappers worldwide.</p>
                </div>
            </div>
        </div>
    </section>

  
    <footer class="bg-primary text-primary-foreground py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-4 gap-8">
                <div>
                    <div class="text-2xl font-bold mb-4">
                        <i class="fas fa-exchange-alt mr-2"></i>Swaply
                    </div>
                    <p class="opacity-90">
                        Building communities through sustainable swapping and sharing.
                    </p>
                </div>
                
                <div>
                    <h3 class="text-lg font-semibold mb-4">Company</h3>
                    <ul class="space-y-2 opacity-90">
                        <li><a href="#" class="hover:opacity-100 transition-opacity">About Us</a></li>
                        <li><a href="#" class="hover:opacity-100 transition-opacity">How It Works</a></li>
                        <li><a href="#" class="hover:opacity-100 transition-opacity">Community</a></li>
                        <li><a href="#" class="hover:opacity-100 transition-opacity">Blog</a></li>
                    </ul>
                </div>
                
                <div>
                    <h3 class="text-lg font-semibold mb-4">Support</h3>
                    <ul class="space-y-2 opacity-90">
                        <li><a href="#" class="hover:opacity-100 transition-opacity">Contact Us</a></li>
                        <li><a href="#" class="hover:opacity-100 transition-opacity">Help Center</a></li>
                        <li><a href="#" class="hover:opacity-100 transition-opacity">Safety Tips</a></li>
                        <li><a href="#" class="hover:opacity-100 transition-opacity">Report Issue</a></li>
                    </ul>
                </div>
                
                <div>
                    <h3 class="text-lg font-semibold mb-4">Legal</h3>
                    <ul class="space-y-2 opacity-90">
                        <li><a href="#" class="hover:opacity-100 transition-opacity">Terms of Service</a></li>
                        <li><a href="#" class="hover:opacity-100 transition-opacity">Privacy Policy</a></li>
                        <li><a href="#" class="hover:opacity-100 transition-opacity">Cookie Policy</a></li>
                        <li><a href="#" class="hover:opacity-100 transition-opacity">Guidelines</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="border-t border-white border-opacity-20 mt-8 pt-8 text-center opacity-90">
                <p>&copy; 2024 Swaply. All rights reserved. Made with ❤️ for sustainable communities.</p>
            </div>
        </div>
    </footer>

    <script>

        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });
    </script>
</body>
</html>