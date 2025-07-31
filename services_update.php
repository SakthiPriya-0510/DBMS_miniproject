<?php
include 'db.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $result = $conn->query("SELECT * FROM services WHERE service_id = $id");
    $service = $result->fetch_assoc();
}

if (isset($_POST['update'])) {
    $stmt = $conn->prepare("UPDATE services SET service_name=?, price=?, duration_minutes=?, service_details=? WHERE service_id=?");
    $stmt->bind_param("sdisi", $_POST['name'], $_POST['price'], $_POST['duration'], $_POST['details'], $_POST['id']);
    $stmt->execute();
    header("Location: services.php");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Update Service</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
    <h2>Update Service</h2>
    <form method="POST">
        <input type="hidden" name="id" value="<?php echo $service['service_id']; ?>">
        <input name="name" value="<?php echo $service['service_name']; ?>" class="form-control mb-2" required>
        <input name="price" type="number" step="0.01" value="<?php echo $service['price']; ?>" class="form-control mb-2" required>
        <input name="duration" type="number" value="<?php echo $service['duration_minutes']; ?>" class="form-control mb-2" required>
        <textarea name="details" class="form-control mb-2" required><?php echo $service['service_details']; ?></textarea>
        <button name="update" class="btn btn-primary">Update Service</button>
        <a href="services.php" class="btn btn-secondary">Back</a>
    </form>
</body>
</html>