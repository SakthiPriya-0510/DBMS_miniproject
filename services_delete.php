<?php
include 'db.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $conn->query("DELETE FROM services WHERE service_id = $id");
}

header("Location: services.php");
exit;
?>