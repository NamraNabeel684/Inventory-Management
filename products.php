<?php

require_once "includes/auth_check.php";
require_once "config/database.php";

$page_title = "Products | Twisted Threads";

$search = trim($_GET["search"] ?? "");
$category_filter = $_GET["category"] ?? "";

$categories = $pdo->query(
    "SELECT * FROM categories ORDER BY category_name ASC"
)->fetchAll(PDO::FETCH_ASSOC);


$sql = "
    SELECT
        products.*,
        categories.category_name
    FROM products
    JOIN categories
        ON products.category_id = categories.id
    WHERE 1=1
";

$params = [];


if (!empty($search)) {

    $sql .= "
        AND (
            products.product_name LIKE ?
            OR products.description LIKE ?
        )
    ";

    $params[] = "%$search%";
    $params[] = "%$search%";
}


if (!empty($category_filter)) {

    $sql .= "
        AND products.category_id = ?
    ";

    $params[] = $category_filter;
}


$sql .= "
    ORDER BY products.created_at DESC
";


$stmt = $pdo->prepare($sql);

$stmt->execute($params);

$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

require_once "includes/header.php";
?>


<div class="page-header">

    <div>

        <p class="page-label">
            PRODUCT MANAGEMENT
        </p>

        <h1>Products</h1>

        <p class="page-description">
            View and manage all your crochet products.
        </p>

    </div>


    <a href="add_product.php"
       class="add-product-btn">
        + Add New Product
    </a>

</div>


<?php if (isset($_GET["success"]) && $_GET["success"] === "added"): ?>

    <div class="success-message">
        Product added successfully!
    </div>

<?php endif; ?>


<?php if (isset($_GET["success"]) && $_GET["success"] === "updated"): ?>

    <div class="success-message">
        Product updated successfully!
    </div>

<?php endif; ?>


<?php if (isset($_GET["success"]) && $_GET["success"] === "deleted"): ?>

    <div class="success-message">
        Product deleted successfully!
    </div>

<?php endif; ?>


<div class="filter-card">

    <form method="GET"
          class="filter-form">


        <input type="text"
               name="search"
               placeholder="Search products..."
               value="<?php echo htmlspecialchars($search); ?>">


        <select name="category">

            <option value="">
                All Categories
            </option>

            <?php foreach ($categories as $category): ?>

                <option value="<?php echo $category["id"]; ?>"
                    <?php echo $category_filter == $category["id"] ? "selected" : ""; ?>>

                    <?php echo htmlspecialchars(
                        $category["category_name"]
                    ); ?>

                </option>

            <?php endforeach; ?>

        </select>


        <button type="submit"
                class="filter-button">
            Search
        </button>


        <a href="products.php"
           class="clear-button">
            Clear
        </a>

    </form>

</div>


<div class="table-card">

    <div class="table-responsive">

        <table>

            <thead>

                <tr>
                    <th>Image</th>
                    <th>Product</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Stock Status</th>
                    <th>Actions</th>
                </tr>

            </thead>


            <tbody>

                <?php if (count($products) > 0): ?>

                    <?php foreach ($products as $product): ?>

                        <tr>

                            <td>

                                <?php if (!empty($product["image"])): ?>

                                    <img src="uploads/<?php echo htmlspecialchars($product["image"]); ?>"
                                         class="product-thumbnail"
                                         alt="Product Image">

                                <?php else: ?>

                                    <div class="no-image">
                                        🧶
                                    </div>

                                <?php endif; ?>

                            </td>


                            <td>

                                <strong>
                                    <?php echo htmlspecialchars(
                                        $product["product_name"]
                                    ); ?>
                                </strong>

                            </td>


                            <td>
                                <?php echo htmlspecialchars(
                                    $product["category_name"]
                                ); ?>
                            </td>


                            <td>
                                Rs. <?php echo number_format(
                                    $product["price"],
                                    2
                                ); ?>
                            </td>


                            <td>
                                <?php echo $product["quantity"]; ?>
                            </td>


                            <td>

                                <?php if ($product["quantity"] == 0): ?>

                                    <span class="status-badge out-stock">
                                        Out of Stock
                                    </span>

                                <?php elseif ($product["quantity"] <= 5): ?>

                                    <span class="status-badge low-stock">
                                        Low Stock
                                    </span>

                                <?php else: ?>

                                    <span class="status-badge in-stock">
                                        In Stock
                                    </span>

                                <?php endif; ?>

                            </td>


                            <td class="action-buttons">

                                <a href="edit_product.php?id=<?php echo $product["id"]; ?>"
                                   class="edit-button">
                                    Edit
                                </a>


                                <a href="delete_product.php?id=<?php echo $product["id"]; ?>"
                                   class="delete-button"
                                   onclick="return confirmDelete('Are you sure you want to delete this product?')">
                                    Delete
                                </a>

                            </td>

                        </tr>

                    <?php endforeach; ?>


                <?php else: ?>

                    <tr>

                        <td colspan="7"
                            class="empty-table">

                            No products found.

                        </td>

                    </tr>

                <?php endif; ?>

            </tbody>

        </table>

    </div>

</div>


<?php require_once "includes/footer.php"; ?>