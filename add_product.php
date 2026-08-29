<?php

error_reporting(E_ALL);
ini_set("display_errors", 1);

require_once "includes/auth_check.php";
require_once "config/database.php";

$page_title = "Add Product | Twisted Threads";

$error = "";
$success = "";

/* Get all categories */
$categories = $pdo->query(
    "SELECT * FROM categories ORDER BY category_name ASC"
)->fetchAll(PDO::FETCH_ASSOC);


/* Process form */
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    /*
    Check if POST data was lost because the uploaded file
    exceeded PHP's post_max_size limit.
    */
    if (empty($_POST) && empty($_FILES)) {

        $error = "The uploaded file is too large for the server. Please choose a smaller image or increase the PHP upload limit.";

    } else {

        /* Safely get form values */
        $product_name = trim($_POST["product_name"] ?? "");
        $category_id = $_POST["category_id"] ?? "";
        $description = trim($_POST["description"] ?? "");
        $quantity = $_POST["quantity"] ?? "";
        $price = $_POST["price"] ?? "";

        $image_name = null;


        /* Validate required fields */
        if (
            empty($product_name) ||
            empty($category_id) ||
            $quantity === "" ||
            $price === ""
        ) {

            $error = "Please fill in all required fields.";

        } elseif (!is_numeric($category_id) || $category_id <= 0) {

            $error = "Please select a valid category.";

        } elseif (!is_numeric($quantity) || $quantity < 0) {

            $error = "Please enter a valid quantity.";

        } elseif (!is_numeric($price) || $price < 0) {

            $error = "Please enter a valid price.";

        } else {

            /* Process image upload */
            if (
                isset($_FILES["image"]) &&
                $_FILES["image"]["error"] !== UPLOAD_ERR_NO_FILE
            ) {

                $upload_error = $_FILES["image"]["error"];

                /* Handle PHP upload errors */
                if ($upload_error === UPLOAD_ERR_INI_SIZE) {

                    $error = "The selected image is too large for the server upload limit.";

                } elseif ($upload_error === UPLOAD_ERR_FORM_SIZE) {

                    $error = "The selected image is too large.";

                } elseif ($upload_error !== UPLOAD_ERR_OK) {

                    $error = "There was a problem uploading the image.";

                } else {

                    /* Maximum image size: 5MB */
                    $max_file_size = 5 * 1024 * 1024;

                    if ($_FILES["image"]["size"] > $max_file_size) {

                        $error = "Image size must be less than 5MB.";

                    } else {

                        /* Check real file MIME type */
                        $finfo = finfo_open(FILEINFO_MIME_TYPE);
                        $file_type = finfo_file(
                            $finfo,
                            $_FILES["image"]["tmp_name"]
                        );
                        finfo_close($finfo);

                        $allowed_types = [
                            "image/jpeg",
                            "image/png",
                            "image/webp"
                        ];

                        if (!in_array($file_type, $allowed_types, true)) {

                            $error = "Only JPG, JPEG, PNG and WEBP images are allowed.";

                        } else {

                            /* Get correct extension */
                            $extension = pathinfo(
                                $_FILES["image"]["name"],
                                PATHINFO_EXTENSION
                            );

                            $extension = strtolower($extension);

                            /* Create uploads folder if it does not exist */
                            $upload_directory = __DIR__ . "/uploads/";

                            if (!is_dir($upload_directory)) {
                                mkdir(
                                    $upload_directory,
                                    0777,
                                    true
                                );
                            }

                            /* Create unique image name */
                            $image_name =
                                time() . "_" .
                                uniqid() . "." .
                                $extension;

                            $upload_path =
                                $upload_directory .
                                $image_name;

                            /* Upload image */
                            if (
                                !move_uploaded_file(
                                    $_FILES["image"]["tmp_name"],
                                    $upload_path
                                )
                            ) {

                                $error = "Failed to upload the product image.";
                                $image_name = null;
                            }
                        }
                    }
                }
            }


            /* Insert product into database */
            if (empty($error)) {

                try {

                    $stmt = $pdo->prepare(
                        "INSERT INTO products
                        (product_name, category_id, description, quantity, price, image)
                        VALUES (?, ?, ?, ?, ?, ?)"
                    );

                    $stmt->execute([
                        $product_name,
                        $category_id,
                        $description,
                        $quantity,
                        $price,
                        $image_name
                    ]);


                    /* Save activity */
                    $activity = $pdo->prepare(
                        "INSERT INTO inventory_activity
                        (user_id, activity_type, description)
                        VALUES (?, ?, ?)"
                    );

                    $activity->execute([
                        $_SESSION["user_id"],
                        "Product Added",
                        $product_name . " was added to inventory."
                    ]);


                    /* Redirect after successful insertion */
                    header("Location: products.php?success=added");
                    exit();

                } catch (PDOException $e) {

                    $error = "Unable to add the product. Please check your database setup.";

                    /*
                    Uncomment this temporarily if you need
                    to see the actual database error:

                    $error .= " " . $e->getMessage();
                    */
                }
            }
        }
    }
}


require_once "includes/header.php";
?>


<div class="page-header">

    <div>

        <p class="page-label">PRODUCT MANAGEMENT</p>

        <h1>Add New Product</h1>

        <p class="page-description">
            Add a handmade Twisted Threads product to your inventory.
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

            <!-- Product Name -->
            <div class="input-group">

                <label>Product Name *</label>

                <input
                    type="text"
                    name="product_name"
                    placeholder="Example: Crochet Sunflower"
                    value="<?php echo htmlspecialchars($_POST["product_name"] ?? ""); ?>"
                    required
                >

            </div>


            <!-- Category -->
            <div class="input-group">

                <label>Category *</label>

                <select
                    name="category_id"
                    required
                >

                    <option value="">
                        Select Category
                    </option>

                    <?php foreach ($categories as $category): ?>

                        <option
                            value="<?php echo $category["id"]; ?>"
                            <?php
                            if (
                                isset($_POST["category_id"]) &&
                                $_POST["category_id"] == $category["id"]
                            ) {
                                echo "selected";
                            }
                            ?>
                        >

                            <?php
                            echo htmlspecialchars(
                                $category["category_name"]
                            );
                            ?>

                        </option>

                    <?php endforeach; ?>

                </select>

            </div>


            <!-- Quantity -->
            <div class="input-group">

                <label>Quantity *</label>

                <input
                    type="number"
                    name="quantity"
                    min="0"
                    placeholder="0"
                    value="<?php echo htmlspecialchars($_POST["quantity"] ?? ""); ?>"
                    required
                >

            </div>


            <!-- Price -->
            <div class="input-group">

                <label>Price (PKR) *</label>

                <input
                    type="number"
                    name="price"
                    min="0"
                    step="0.01"
                    placeholder="0.00"
                    value="<?php echo htmlspecialchars($_POST["price"] ?? ""); ?>"
                    required
                >

            </div>

        </div>


        <!-- Description -->
        <div class="input-group">

            <label>Product Description</label>

            <textarea
                name="description"
                placeholder="Write a short description about the product..."
                rows="5"
            ><?php echo htmlspecialchars($_POST["description"] ?? ""); ?></textarea>

        </div>


        <!-- Product Image -->
        <div class="input-group">

            <label>Product Image</label>

            <input
                type="file"
                name="image"
                accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
            >

            <small>
                JPG, PNG or WEBP. Maximum size: 5MB.
            </small>

        </div>


        <!-- Submit Button -->
        <button
            type="submit"
            class="main-button form-submit"
        >

            Add Product

        </button>

    </form>

</div>


<?php require_once "includes/footer.php"; ?>