<?php

require_once "includes/auth_check.php";
require_once "config/database.php";

$page_title = "Edit Product | Twisted Threads";

if (!isset($_GET["id"])) {
    header("Location: products.php");
    exit();
}

$id = (int) $_GET["id"];


$stmt = $pdo->prepare(
    "SELECT * FROM products WHERE id = ?"
);

$stmt->execute([$id]);

$product = $stmt->fetch(PDO::FETCH_ASSOC);


if (!$product) {
    header("Location: products.php");
    exit();
}


$categories = $pdo->query(
    "SELECT * FROM categories ORDER BY category_name ASC"
)->fetchAll(PDO::FETCH_ASSOC);

$error = "";


if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $product_name = trim($_POST["product_name"]);
    $category_id = $_POST["category_id"];
    $description = trim($_POST["description"]);
    $quantity = $_POST["quantity"];
    $price = $_POST["price"];

    $image_name = $product["image"];


    if (
        empty($product_name) ||
        empty($category_id) ||
        $quantity === "" ||
        $price === ""
    ) {

        $error = "Please fill in all required fields.";

    } else {

        if (
            isset($_FILES["image"]) &&
            $_FILES["image"]["error"] === 0
        ) {

            $allowed_types = [
                "image/jpeg",
                "image/png",
                "image/jpg",
                "image/webp"
            ];

            if (
                in_array(
                    $_FILES["image"]["type"],
                    $allowed_types
                )
            ) {

                $extension = pathinfo(
                    $_FILES["image"]["name"],
                    PATHINFO_EXTENSION
                );

                $new_image =
                    time() . "_" .
                    uniqid() . "." .
                    $extension;

                if (
                    move_uploaded_file(
                        $_FILES["image"]["tmp_name"],
                        "uploads/" . $new_image
                    )
                ) {

                    if (
                        !empty($product["image"]) &&
                        file_exists(
                            "uploads/" . $product["image"]
                        )
                    ) {

                        unlink(
                            "uploads/" . $product["image"]
                        );
                    }

                    $image_name = $new_image;
                }
            }
        }


        $update = $pdo->prepare(
            "UPDATE products
            SET
                product_name = ?,
                category_id = ?,
                description = ?,
                quantity = ?,
                price = ?,
                image = ?
            WHERE id = ?"
        );


        $update->execute([
            $product_name,
            $category_id,
            $description,
            $quantity,
            $price,
            $image_name,
            $id
        ]);


        $activity = $pdo->prepare(
            "INSERT INTO inventory_activity
            (user_id, activity_type, description)
            VALUES (?, ?, ?)"
        );

        $activity->execute([
            $_SESSION["user_id"],
            "Product Updated",
            $product_name . " was updated."
        ]);


        header("Location: products.php?success=updated");
        exit();
    }
}

require_once "includes/header.php";
?>


<div class="page-header">

    <div>

        <p class="page-label">
            PRODUCT MANAGEMENT
        </p>

        <h1>Edit Product</h1>

        <p class="page-description">
            Update your crochet product details.
        </p>

    </div>


    <a href="products.php"
       class="secondary-button">
        ← Back to Products
    </a>

</div>


<div class="form-card">

    <?php if (!empty($error)): ?>

        <div class="error-message">
            <?php echo htmlspecialchars($error); ?>
        </div>

    <?php endif; ?>


    <form method="POST"
          enctype="multipart/form-data"
          class="product-form">


        <div class="form-grid">

            <div class="input-group">

                <label>Product Name *</label>

                <input type="text"
                       name="product_name"
                       value="<?php echo htmlspecialchars($product["product_name"]); ?>"
                       required>

            </div>


            <div class="input-group">

                <label>Category *</label>

                <select name="category_id"
                        required>

                    <?php foreach ($categories as $category): ?>

                        <option value="<?php echo $category["id"]; ?>"
                            <?php echo $product["category_id"] == $category["id"] ? "selected" : ""; ?>>

                            <?php echo htmlspecialchars(
                                $category["category_name"]
                            ); ?>

                        </option>

                    <?php endforeach; ?>

                </select>

            </div>


            <div class="input-group">

                <label>Quantity *</label>

                <input type="number"
                       name="quantity"
                       min="0"
                       value="<?php echo $product["quantity"]; ?>"
                       required>

            </div>


            <div class="input-group">

                <label>Price (PKR) *</label>

                <input type="number"
                       name="price"
                       min="0"
                       step="0.01"
                       value="<?php echo $product["price"]; ?>"
                       required>

            </div>

        </div>


        <div class="input-group">

            <label>Description</label>

            <textarea name="description"
                      rows="5"><?php echo htmlspecialchars($product["description"] ?? ""); ?></textarea>

        </div>


        <?php if (!empty($product["image"])): ?>

            <div class="current-image">

                <p>Current Image</p>

                <img src="uploads/<?php echo htmlspecialchars($product["image"]); ?>"
                     alt="Current Product Image">

            </div>

        <?php endif; ?>


        <div class="input-group">

            <label>Change Product Image</label>

            <input type="file"
                   name="image"
                   accept=".jpg,.jpeg,.png,.webp">

        </div>


        <button type="submit"
                class="main-button form-submit">

            Update Product

        </button>

    </form>

</div>


<?php require_once "includes/footer.php"; ?>