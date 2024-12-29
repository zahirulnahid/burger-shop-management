<?php
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['invoiceID'])) {
    $invoiceID = (int)$_GET['invoiceID'];
} else {
    die("Invalid access.");
}

// Database connection
$servername = "localhost";
$username = "amartabl_coachin";
$password = "Bangladesh@1971";
$database = "amartabl_gourmet_burgers";

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch order total amount
$sql = "SELECT total_amount FROM orders WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $invoiceID);
$stmt->execute();

// Bind result to a variable
$stmt->bind_result($totalAmount);
$stmt->fetch(); // Fetch the result
if (!$totalAmount) {
    die("Invoice not found.");
}
$stmt->close();


// Fetch order items
$sql = "SELECT * FROM order_items WHERE order_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $invoiceID);
$stmt->execute();

// Bind the results to variables
$stmt->bind_result($item_id, $order_id, $name, $quantity, $price, $total);

// Fetch the results into an array
$items = [];
while ($stmt->fetch()) {
    $items[] = [
        'item_id' => $item_id,
        'order_id' => $order_id,
        'name' => $name,
        'quantity' => $quantity,
        'price' => $price,
        'total' => $total
    ];
}
$stmt->close();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .header {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
        }
        .header img {
            height: 80px;
            margin-right: 15px;
        }
        .header .brand-name {
            font-size: 2rem;
            font-weight: bold;
            color: #333;
        }
        h1 {
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }
        th {
            background-color: #ffa401;
            color: white;
        }
        .total {
            font-size: 1.5rem;
            font-weight: bold;
            text-align: right;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <!-- Header Section with Logo and Brand Name -->
    <div class="header">
        <img src="asset/logo.jpg" alt="Brand Logo">
        <div class="brand-name">Gourmet Burgers</div>
    </div>

    <h1>Invoice</h1>
    <table>
        <thead>
            <tr>
                <th>Item</th>
                <th>Quantity</th>
                <th>Price (৳)</th>
                <th>Total (৳)</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $item): ?>
                <tr>
                    <td><?php echo htmlspecialchars($item['name']); ?></td>
                    <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                    <td><?php echo number_format($item['price'], 2); ?></td>
                    <td><?php echo number_format($item['total'], 2); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <div class="total">Total Amount: ৳ <?php echo number_format($totalAmount, 2); ?></div>
</body>
</html>
