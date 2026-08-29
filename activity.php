<?php

require_once "includes/auth_check.php";
require_once "config/database.php";

$page_title = "Activity | Twisted Threads";


$activities = $pdo->query(
    "SELECT
        inventory_activity.*,
        users.full_name
    FROM inventory_activity
    LEFT JOIN users
        ON inventory_activity.user_id = users.id
    ORDER BY inventory_activity.created_at DESC"
)->fetchAll(PDO::FETCH_ASSOC);


require_once "includes/header.php";
?>


<div class="page-header">

    <div>

        <p class="page-label">
            INVENTORY HISTORY
        </p>

        <h1>Recent Activity</h1>

        <p class="page-description">
            View all important changes made to your inventory.
        </p>

    </div>

</div>


<div class="activity-list">

    <?php if (count($activities) > 0): ?>

        <?php foreach ($activities as $activity): ?>

            <div class="activity-item">

                <div class="activity-icon">
                    ◷
                </div>


                <div class="activity-details">

                    <h3>
                        <?php echo htmlspecialchars(
                            $activity["activity_type"]
                        ); ?>
                    </h3>


                    <p>
                        <?php echo htmlspecialchars(
                            $activity["description"]
                        ); ?>
                    </p>


                    <small>

                        By
                        <?php echo htmlspecialchars(
                            $activity["full_name"] ?? "Unknown User"
                        ); ?>

                        •
                        <?php echo date(
                            "d M Y, h:i A",
                            strtotime($activity["created_at"])
                        ); ?>

                    </small>

                </div>

            </div>

        <?php endforeach; ?>


    <?php else: ?>

        <div class="activity-box">

            <p class="empty-activity">
                No inventory activity yet.
            </p>

        </div>

    <?php endif; ?>

</div>


<?php require_once "includes/footer.php"; ?>