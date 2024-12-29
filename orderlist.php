<?php
// Database connection
$servername = "localhost";
$username = "amartabl_coachin";
$password = "Bangladesh@1971";
$database = "amartabl_gourmet_burgers";

$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch all orders
$sqlOrders = "SELECT id, total_amount, created_at FROM orders ORDER BY created_at DESC";
$resultOrders = $conn->query($sqlOrders);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Orders with Details</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f8f8;
            margin: 0;
            padding: 0;
        }

        .navbar {
            width: 100%;
            background-color: #ffa401;
            color: #fff;
            padding: 1rem;
            text-align: center;
            font-size: 1.5rem;
            font-weight: bold;
        }

        .order-container {
            width: 90%;
            margin: 2rem auto;
            background-color: #fff;
            border-radius: 8px;
            padding: 1.5rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .order-header {
            font-size: 1.2rem;
            margin-bottom: 1rem;
            font-weight: bold;
            color: #333;
        }

        .order-details {
            margin-bottom: 1rem;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }

        th, td {
            padding: 0.75rem;
            text-align: left;
            border: 1px solid #ddd;
        }

        th {
            background-color: #ffa401;
            color: #fff;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        .empty-message {
            text-align: center;
            margin: 2rem;
            color: #555;
            font-size: 1.2rem;
        }

        .print-button {
            margin-top: 1rem;
        }

        .print-button button {
            background-color: #180580;
            color: #ffa401;
            font-size: 1rem;
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 8px;
            cursor: pointer;
        }

        .print-button button:hover {
            background-color: #ffa401;
            color: #fff;
        }
    </style>
</head>
<body>
    <div class="navbar">All Orders with Details</div>

    <?php if ($resultOrders->num_rows > 0): ?>
        <?php while ($order = $resultOrders->fetch_assoc()): ?>
            <div class="order-container">
                <div class="order-header">
                    Order ID: <?php echo $order['id']; ?> | 
                    Total Amount: ৳<?php echo number_format($order['total_amount'], 2); ?> | 
                    Date: <?php echo date("d M Y, h:i A", strtotime($order['created_at'])); ?>
                </div>

                <!-- Fetch order items -->
                <?php
                $orderId = $order['id'];
                $sqlItems = "SELECT name, quantity, price, total FROM order_items WHERE order_id = $orderId";
                $resultItems = $conn->query($sqlItems);
                ?>
                <?php if ($resultItems->num_rows > 0): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Item Name</th>
                                <th>Quantity</th>
                                <th>Price (৳)</th>
                                <th>Total (৳)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($item = $resultItems->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($item['name']); ?></td>
                                    <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                                    <td><?php echo number_format($item['price'], 2); ?></td>
                                    <td><?php echo number_format($item['total'], 2); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No items found for this order.</p>
                <?php endif; ?>

                <!-- Print Receipt Button -->
                <div class="print-button">
                    <form method="get" action="my.bluetoothprint.scheme://https://gourmetburgers.amartable.com/response.php">
                        <button type="submit" name="invoiceID" value="<?php echo $order['id']; ?>">Print Receipt</button>
                    </form>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p class="empty-message">No orders found.</p>
    <?php endif; ?>
</body>
</html>
