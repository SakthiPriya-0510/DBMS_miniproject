<?php
include 'db.php';

if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment;filename=appointments_report.csv');

    $output = fopen("php://output", "w");
    fputcsv($output, ['ID', 'Customer', 'Service', 'Date', 'Time']);

    $result = $conn->query("SELECT a.appointment_id, c.name AS customer_name, s.service_name, a.appointment_date, a.appointment_time
                            FROM appointments a
                            JOIN customers c ON a.customer_id = c.customer_id
                            JOIN services s ON a.service_id = s.service_id");
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, [$row['appointment_id'], $row['customer_name'], $row['service_name'], $row['appointment_date'], $row['appointment_time']]);
    }

    fclose($output);
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Appointments</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
    <h2>Appointments</h2>

    <a href="?export=csv" class="btn btn-success mb-3">Download CSV Report</a>

    <form method="POST" class="mb-3">
        <select name="customer_id" class="form-control mb-2" required>
            <option value="">Select Customer</option>
            <?php
            $customers = $conn->query("SELECT * FROM customers");
            while ($c = $customers->fetch_assoc()) {
                echo "<option value='{$c['customer_id']}'>{$c['name']}</option>";
            }
            ?>
        </select>
        <select name="service_id" class="form-control mb-2" required>
            <option value="">Select Service</option>
            <?php
            $services = $conn->query("SELECT * FROM services");
            while ($s = $services->fetch_assoc()) {
                echo "<option value='{$s['service_id']}'>{$s['service_name']}</option>";
            }
            ?>
        </select>
        <input type="date" name="date" class="form-control mb-2" required>
        <input type="time" name="time" class="form-control mb-2" required>
        <button name="add" class="btn btn-primary">Add Appointment</button>
    </form>

    <?php
    if (isset($_POST['add'])) {
        $stmt = $conn->prepare("INSERT INTO appointments (customer_id, service_id, appointment_date, appointment_time) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiss", $_POST['customer_id'], $_POST['service_id'], $_POST['date'], $_POST['time']);
        $stmt->execute();
    }

    $result = $conn->query("SELECT a.appointment_id, c.name AS customer_name, s.service_name, a.appointment_date, a.appointment_time
                            FROM appointments a
                            JOIN customers c ON a.customer_id = c.customer_id
                            JOIN services s ON a.service_id = s.service_id");

    echo "<table class='table'><tr><th>ID</th><th>Customer</th><th>Service</th><th>Date</th><th>Time</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>{$row['appointment_id']}</td>
                <td>{$row['customer_name']}</td>
                <td>{$row['service_name']}</td>
                <td>{$row['appointment_date']}</td>
                <td>{$row['appointment_time']}</td>
              </tr>";
    }
    echo "</table>";
    ?>
</body>
</html>
