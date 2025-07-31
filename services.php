<?php
include 'db.php';

if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment;filename=services_report.csv');

    $output = fopen("php://output", "w");
    fputcsv($output, ['ID', 'Service Name', 'Price', 'Duration', 'Details']);

    $result = $conn->query("SELECT * FROM services");
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, [$row['service_id'], $row['service_name'], $row['price'], $row['duration_minutes'], $row['service_details']]);
    }

    fclose($output);
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Services</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
    <h2>Service List</h2>

    <!-- CSV Export Button -->
    <a href="?export=csv" class="btn btn-success mb-3">Download CSV Report</a>

    <!-- Service Add Form -->
    <form method="POST" class="mb-3">
        <input name="name" placeholder="Service Name" class="form-control mb-2" required>
        <input name="price" type="number" step="0.01" placeholder="Price" class="form-control mb-2" required>
        <input name="duration" type="number" placeholder="Duration (minutes)" class="form-control mb-2" required>
        <textarea name="details" placeholder="Service Details" class="form-control mb-2" required></textarea>
        <button name="add" class="btn btn-primary">Add Service</button>
    </form>

    <?php
    if (isset($_POST['add'])) {
        $stmt = $conn->prepare("INSERT INTO services (service_name, price, duration_minutes, service_details) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sdis", $_POST['name'], $_POST['price'], $_POST['duration'], $_POST['details']);
        $stmt->execute();
    }

    $result = $conn->query("SELECT * FROM services");

    echo "<table class='table'><tr><th>ID</th><th>Name</th><th>Price</th><th>Duration</th><th>Details</th><th>Actions</th></tr>";

    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>{$row['service_id']}</td>
                <td>{$row['service_name']}</td>
                <td>{$row['price']}</td>
                <td>{$row['duration_minutes']}</td>
                <td>{$row['service_details']}</td>
                <td>
                    <a href='services_update.php?id={$row['service_id']}' class='btn btn-sm btn-warning'>Update</a> 
                    <a href='services_delete.php?id={$row['service_id']}' class='btn btn-sm btn-danger' onclick=\"return confirm('Are you sure?')\">Delete</a>
                </td>
              </tr>";
    }

    echo "</table>";
    ?>
</body>
</html>
