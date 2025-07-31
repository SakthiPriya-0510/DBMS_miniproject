<?php
include 'db.php';

if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment;filename=customers_report.csv');

    $output = fopen("php://output", "w");
    fputcsv($output, ['ID', 'Name', 'Phone', 'Email']);

    $result = $conn->query("SELECT * FROM customers");
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, [$row['customer_id'], $row['name'], $row['phone'], $row['email']]);
    }

    fclose($output);
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Customers</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
    <h2>Customer List</h2>

    <a href="?export=csv" class="btn btn-success mb-3">Download CSV Report</a>

    <form method="POST" class="mb-3">
        <input name="name" placeholder="Customer Name" class="form-control mb-2" required>
        <input name="phone" placeholder="Phone" class="form-control mb-2" required>
        <input name="email" type="email" placeholder="Email" class="form-control mb-2" required>
        <button name="add" class="btn btn-primary">Add Customer</button>
    </form>

    <?php
    if (isset($_POST['add'])) {
        $stmt = $conn->prepare("INSERT INTO customers (name, phone, email) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $_POST['name'], $_POST['phone'], $_POST['email']);
        $stmt->execute();
    }

    $result = $conn->query("SELECT * FROM customers");
    echo "<table class='table'><tr><th>ID</th><th>Name</th><th>Phone</th><th>Email</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>{$row['customer_id']}</td>
                <td>{$row['name']}</td>
                <td>{$row['phone']}</td>
                <td>{$row['email']}</td>
              </tr>";
    }
    echo "</table>";
    ?>
</body>
</html>
