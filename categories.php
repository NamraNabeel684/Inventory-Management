<?php

require_once "includes/auth_check.php";
require_once "config/database.php";

$page_title = "Categories | Twisted Threads";

$error = "";


if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $category_name = trim(
        $_POST["category_name"]
    );


    if (empty($category_name)) {

        $error = "Please enter a category name.";

    } else {

        try {

            $stmt = $pdo->prepare(
                "INSERT INTO categories (category_name)
                VALUES (?)"
            );

            $stmt->execute([$category_name]);

            header(
                "Location: categories.php?success=added"
            );

            exit();

        } catch (PDOException $e) {

            $error =
                "This category already exists.";
        }
    }
}


if (
    isset($_GET["delete"])
) {

    $id = (int) $_GET["delete"];

    try {

        $delete = $pdo->prepare(
            "DELETE FROM categories WHERE id = ?"
        );

        $delete->execute([$id]);

        header(
            "Location: categories.php?success=deleted"
        );

        exit();

    } catch (PDOException $e) {

        $error =
            "This category cannot be deleted because products may be using it.";
    }
}


$categories = $pdo->query(
    "SELECT
        categories.*,
        COUNT(products.id) AS product_count
    FROM categories
    LEFT JOIN products
        ON categories.id = products.category_id
    GROUP BY categories.id
    ORDER BY categories.category_name ASC"
)->fetchAll(PDO::FETCH_ASSOC);


require_once "includes/header.php";
?>


<div class="page-header">

    <div>

        <p class="page-label">
            ORGANIZATION
        </p>

        <h1>Categories</h1>

        <p class="page-description">
            Organize your Twisted Threads products into categories.
        </p>

    </div>

</div>


<?php if (!empty($error)): ?>

    <div class="error-message">
        <?php echo htmlspecialchars($error); ?>
    </div>

<?php endif; ?>


<?php if (isset($_GET["success"])): ?>

    <div class="success-message">

        <?php
        echo $_GET["success"] === "added"
            ? "Category added successfully!"
            : "Category deleted successfully!";
        ?>

    </div>

<?php endif; ?>


<div class="categories-layout">


    <div class="form-card category-form-card">

        <p class="page-label">
            NEW CATEGORY
        </p>

        <h2>Add Category</h2>


        <form method="POST">

            <div class="input-group">

                <label>Category Name</label>

                <input type="text"
                       name="category_name"
                       placeholder="Example: Crochet Bags"
                       required>

            </div>


            <button type="submit"
                    class="main-button">

                Add Category

            </button>

        </form>

    </div>


    <div class="table-card">

        <div class="table-responsive">

            <table>

                <thead>

                    <tr>
                        <th>Category</th>
                        <th>Total Products</th>
                        <th>Action</th>
                    </tr>

                </thead>


                <tbody>

                    <?php foreach ($categories as $category): ?>

                        <tr>

                            <td>
                                <?php echo htmlspecialchars(
                                    $category["category_name"]
                                ); ?>
                            </td>


                            <td>
                                <?php echo $category["product_count"]; ?>
                            </td>


                            <td>

                                <a href="categories.php?delete=<?php echo $category["id"]; ?>"
                                   class="delete-button"
                                   onclick="return confirmDelete('Delete this category?')">

                                    Delete

                                </a>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                </tbody>

            </table>

        </div>

    </div>

</div>


<?php require_once "includes/footer.php"; ?>