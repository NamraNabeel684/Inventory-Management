<?php

require_once "includes/auth_check.php";
require_once "config/database.php";

$page_title = "Inventory | Twisted Threads";


if (
    $_SERVER["REQUEST_METHOD"] === "POST"
) {

    $product_id = (int) $_POST["product_id"];
    $quantity = (int) $_POST["quantity"];


    if ($quantity >= 0) {

        $stmt = $pdo->prepare(
            "UPDATE products
            SET quantity = ?
            WHERE id = ?"
        );

        $stmt->execute([
            $quantity,
            $product_id
        ]);


        $activity = $pdo->prepare(
            "INSERT INTO inventory_activity
            (user_id, activity_type, description)
            VALUES (?, ?, ?)"
        );

        $activity->execute([
            $_SESSION["user_id"],
            "Stock Updated",
            "Product stock quantity was updated."
        ]);
    }


    header("Location: inventory.php?success=updated");
    exit();
}


$products = $pdo->query(
    "SELECT
        products.*,
        categories.category_name
    FROM products
    JOIN categories
        ON products.category_id = categories.id
    ORDER BY products.quantity ASC"
)->fetchAll(PDO::FETCH_ASSOC);


require_once "includes/header.php";
?>


<div class="page-header">

    <div>

        <p class="page-label">
            STOCK MANAGEMENT
        </p>

        <h1>Inventory</h1>

        <p class="page-description">
            Monitor and update your available crochet product stock.
        </p>

    </div>

</div>


<?php if (isset($_GET["success"])): ?>

    <div class="success-message">
        Inventory updated successfully!
    </div>

<?php endif; ?>


<div class="table-card">

    <div class="table-responsive">

        <table>

            <thead>

                <tr>
                    <th>Product</th>
                    <th>Category</th>
                    <th>Current Stock</th>
                    <th>Status</th>
                    <th>Update Quantity</th>
                </tr>

            </thead>


            <tbody>

                <?php foreach ($products as $product): ?>

                    <tr>

                        <td>
                            <?php echo htmlspecialchars(
                                $product["product_name"]
                            ); ?>
                        </td>


                        <td>
                            <?php echo htmlspecialchars(
                                $product["category_name"]
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


                        <td>

                            <form method="POST"
                                  class="stock-form">

                                <input type="hidden"
                                       name="product_id"
                                       value="<?php echo $product["id"]; ?>">

                                <input type="number"
                                       name="quantity"
                                       min="0"
                                       value="<?php echo $product["quantity"]; ?>">

                                <button type="submit"
                                        class="small-button">
                                    Update
                                </button>

                            </form>

                        </td>

                    </tr>

                <?php endforeach; ?>

            </tbody>

        </table>

    </div>

</div>


<?php require_once "includes/footer.php"; ?>