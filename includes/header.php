<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$current_page = basename($_SERVER["PHP_SELF"]);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title><?php echo $page_title ?? "Twisted Threads"; ?></title>

    <link rel="stylesheet"
          href="assets/css/style.css">
</head>

<body>

<div class="dashboard-layout">

    <aside class="sidebar">

        <div class="sidebar-brand">

            <img src="assets/images/logo.jpg"
                 alt="Twisted Threads Logo"
                 class="sidebar-logo">

            <div>
                <h2>Twisted</h2>
                <span>THREADS</span>
            </div>

        </div>


        <nav class="sidebar-nav">

            <a href="dashboard.php"
               class="nav-link <?php echo $current_page == 'dashboard.php' ? 'active' : ''; ?>">
                <span>▦</span>
                Dashboard
            </a>


            <a href="products.php"
               class="nav-link <?php echo in_array($current_page, ['products.php', 'add_product.php', 'edit_product.php']) ? 'active' : ''; ?>">
                <span>◇</span>
                Products
            </a>


            <a href="categories.php"
               class="nav-link <?php echo $current_page == 'categories.php' ? 'active' : ''; ?>">
                <span>▤</span>
                Categories
            </a>


            <a href="inventory.php"
               class="nav-link <?php echo $current_page == 'inventory.php' ? 'active' : ''; ?>">
                <span>▥</span>
                Inventory
            </a>


            <a href="activity.php"
               class="nav-link <?php echo $current_page == 'activity.php' ? 'active' : ''; ?>">
                <span>◷</span>
                Activity
            </a>

        </nav>


        <div class="sidebar-bottom">

            <p>Logged in as</p>

            <strong>
                <?php echo htmlspecialchars(
                    $_SESSION["user_name"] ?? "Admin"
                ); ?>
            </strong>

            <a href="logout.php"
               class="logout-link">
                Logout
            </a>

        </div>

    </aside>


    <main class="main-content">