<?php

function logActivity(
    $con,
    $action,
    $description,
    $actor_id = null,
    $actor_type = null,
    $recipient_id = null
)
{
    $stmt = $con->prepare("
        INSERT INTO activity_log
(
    actor_id,
    recipient_id,
    actor_type,
    action,
    description
)
VALUES (?, ?, ?, ?, ?)
    ");

    $stmt->bind_param(
    "iisss",
    $actor_id,
    $recipient_id,
    $actor_type,
    $action,
    $description
);
    $stmt->execute();

    $stmt->close();
}