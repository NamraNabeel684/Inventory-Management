<?php

require_once "includes/auth_check.php";
require_once "config/database.php";


if (!isset($_GET["id"])) {

    header("Location: products.php");
    exit();
}


$id = (int) $_GET["id"];


$stmt = $pdo->prepare(
    "SELECT product_name, image
    FROM products
    WHERE id = ?"
);

$stmt->execute([$id]);

$product = $stmt->fetch(PDO::FETCH_ASSOC);


if ($product) {

    $delete = $pdo->prepare(
        "DELETE FROM products WHERE id = ?"
    );

    $delete->execute([$id]);


    if (
        !empty($product["image"]) &&
        file_exists("uploads/" . $product["image"])
    ) {

        unlink("uploads/" . $product["image"]);
    }


    $activity = $pdo->prepare(
        "INSERT INTO inventory_activity
        (user_id, activity_type, description)
        VALUES (?, ?, ?)"
    );

    $activity->execute([
        $_SESSION["user_id"],
        "Product Deleted",
        $product["product_name"] .
        " was deleted from inventory."
    ]);
}


header("Location: products.php?success=deleted");
exit();

?>