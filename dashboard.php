<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

require_once "includes/auth_check.php";
require_once "config/database.php";

$total_products = 0;
$total_categories = 0;
$low_stock = 0;
$out_of_stock = 0;
$activities = [];

try {

    /* ==============================
       DASHBOARD STATISTICS
    ============================== */

    $stmt = $pdo->query("SELECT COUNT(*) FROM products");
    $total_products = $stmt->fetchColumn();

    $stmt = $pdo->query("SELECT COUNT(*) FROM categories");
    $total_categories = $stmt->fetchColumn();

    $stmt = $pdo->query(
        "SELECT COUNT(*) 
         FROM products
         WHERE quantity > 0 AND quantity <= 5"
    );
    $low_stock = $stmt->fetchColumn();

    $stmt = $pdo->query(
        "SELECT COUNT(*) 
         FROM products
         WHERE quantity = 0"
    );
    $out_of_stock = $stmt->fetchColumn();


    /* ==============================
       RECENT INVENTORY ACTIVITY
    ============================== */

    /*
       Get the latest 5 activities.

       We are using user_id from the session
       so each logged-in user sees their own activity.
    */

    if (isset($_SESSION["user_id"])) {

        $activityStmt = $pdo->prepare(
            "SELECT activity_type, description, created_at
             FROM inventory_activity
             WHERE user_id = ?
             ORDER BY created_at DESC
             LIMIT 5"
        );

        $activityStmt->execute([
            $_SESSION["user_id"]
        ]);

        $activities = $activityStmt->fetchAll(PDO::FETCH_ASSOC);

    }

} catch (PDOException $e) {

    $database_error = $e->getMessage();

}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Dashboard | Twisted Threads</title>

    <link rel="stylesheet"
          href="assets/css/style.css">

</head>


<body>

<div class="dashboard-layout">


    <!-- ==============================
         SIDEBAR
    ============================== -->

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
               class="nav-link active">

                <span>▦</span>

                Dashboard

            </a>


            <a href="products.php"
               class="nav-link">

                <span>◈</span>

                Products

            </a>


            <a href="categories.php"
               class="nav-link">

                <span>▤</span>

                Categories

            </a>


            <a href="inventory.php"
               class="nav-link">

                <span>◫</span>

                Inventory

            </a>


            <a href="activity.php"
               class="nav-link">

                <span>◷</span>

                Activity

            </a>

        </nav>


        <div class="sidebar-bottom">

            <p>
                Logged in as
            </p>

            <strong>

                <?php
                echo htmlspecialchars(
                    $_SESSION["user_name"]
                );
                ?>

            </strong>


            <a href="logout.php"
               class="logout-link">

                Logout

            </a>

        </div>

    </aside>



    <!-- ==============================
         MAIN CONTENT
    ============================== -->

    <main class="main-content">


        <!-- TOP SECTION -->

        <div class="top-section">

            <div>

                <p class="page-label">
                    TWISTED THREADS
                </p>

                <h1>
                    Inventory Dashboard
                </h1>

                <p class="page-description">

                    Keep track of your handmade creations
                    and manage your stock in one place.

                </p>

            </div>


            <a href="add_product.php"
               class="add-product-btn">

                + Add New Product

            </a>

        </div>



        <!-- ==============================
             STATISTICS
        ============================== -->

        <section class="stats-grid">


            <div class="stat-card">

                <div class="stat-icon">
                    ◈
                </div>

                <div>

                    <p>
                        Total Products
                    </p>

                    <h2>

                        <?php
                        echo $total_products;
                        ?>

                    </h2>

                </div>

            </div>



            <div class="stat-card">

                <div class="stat-icon">
                    ▤
                </div>

                <div>

                    <p>
                        Categories
                    </p>

                    <h2>

                        <?php
                        echo $total_categories;
                        ?>

                    </h2>

                </div>

            </div>



            <div class="stat-card warning-card">

                <div class="stat-icon">
                    !
                </div>

                <div>

                    <p>
                        Low Stock
                    </p>

                    <h2>

                        <?php
                        echo $low_stock;
                        ?>

                    </h2>

                </div>

            </div>



            <div class="stat-card danger-card">

                <div class="stat-icon">
                    ×
                </div>

                <div>

                    <p>
                        Out of Stock
                    </p>

                    <h2>

                        <?php
                        echo $out_of_stock;
                        ?>

                    </h2>

                </div>

            </div>


        </section>



        <!-- ==============================
             QUICK ACTIONS
        ============================== -->

        <section class="dashboard-section">


            <div class="section-heading">

                <div>

                    <p class="page-label">
                        QUICK ACTIONS
                    </p>

                    <h2>
                        Manage your inventory
                    </h2>

                </div>

            </div>



            <div class="quick-actions">


                <a href="add_product.php"
                   class="action-box">

                    <div class="action-symbol">
                        +
                    </div>

                    <h3>
                        Add Product
                    </h3>

                    <p>

                        Add a new handmade item
                        to your inventory.

                    </p>

                </a>



                <a href="products.php"
                   class="action-box">

                    <div class="action-symbol">
                        ◈
                    </div>

                    <h3>
                        View Products
                    </h3>

                    <p>

                        View, edit and manage
                        all your crochet products.

                    </p>

                </a>



                <a href="categories.php"
                   class="action-box">

                    <div class="action-symbol">
                        ▤
                    </div>

                    <h3>
                        Categories
                    </h3>

                    <p>

                        Organize your products
                        into different categories.

                    </p>

                </a>


            </div>

        </section>



        <!-- ==============================
             RECENT ACTIVITY
        ============================== -->

        <section class="dashboard-section">


            <div class="section-heading">

                <div>

                    <p class="page-label">
                        RECENT UPDATES
                    </p>

                    <h2>
                        Inventory activity
                    </h2>

                </div>

            </div>



            <div class="activity-box">


                <?php if (!empty($activities)): ?>


                    <?php foreach ($activities as $activity): ?>


                        <div class="activity-item">


                            <div class="activity-icon">

                                <?php

                                /*
                                   Different symbols
                                   for different activities.
                                */

                                if (
                                    stripos(
                                        $activity["activity_type"],
                                        "add"
                                    ) !== false
                                ) {

                                    echo "+";

                                } elseif (
                                    stripos(
                                        $activity["activity_type"],
                                        "delete"
                                    ) !== false
                                ) {

                                    echo "×";

                                } elseif (
                                    stripos(
                                        $activity["activity_type"],
                                        "update"
                                    ) !== false ||
                                    stripos(
                                        $activity["activity_type"],
                                        "edit"
                                    ) !== false
                                ) {

                                    echo "✎";

                                } else {

                                    echo "◷";

                                }

                                ?>

                            </div>



                            <div class="activity-content">


                                <strong>

                                    <?php
                                    echo htmlspecialchars(
                                        $activity["activity_type"]
                                    );
                                    ?>

                                </strong>


                                <p>

                                    <?php
                                    echo htmlspecialchars(
                                        $activity["description"]
                                    );
                                    ?>

                                </p>


                                <small>

                                    <?php

                                    echo date(
                                        "M d, Y • h:i A",
                                        strtotime(
                                            $activity["created_at"]
                                        )
                                    );

                                    ?>

                                </small>


                            </div>


                        </div>


                    <?php endforeach; ?>


                <?php else: ?>


                    <p class="empty-activity">

                        Your recent inventory activity
                        will appear here.

                    </p>


                <?php endif; ?>


            </div>


        </section>


    </main>


</div>

</body>

</html>
