<?php
$order_id = $_GET["invoiceID"];

// Database connection
$servername = "localhost";
$username = "amartabl_coachin";
$password = "Bangladesh@1971";
$database = "amartabl_gourmet_burgers";

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} else {
    // Connection successful, proceed with the script
}

// Fetch saved data to display
$sql = "SELECT * FROM order_items WHERE order_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $order_id);
$stmt->execute();

// Bind result variables
$stmt->bind_result($item_id, $order_id, $name, $quantity, $price, $total);

$rows = [];
while ($stmt->fetch()) {
    $rows[] = [
        'item_id' => $item_id,
        'order_id' => $order_id,
        'name' => $name,
        'quantity' => $quantity,
        'price' => $price,
        'total' => $total
    ];
}
$stmt->close();

// Close the connection
$conn->close();

// Extract order data
$order_data = [];
foreach ($rows as $row) {
    $order_data[$row['order_id']][] = $row;
}

// Prepare data for printing
$printData = array();







// Logo (You can use a placeholder for now)
//sending image entry	
$obj2 = new stdClass();	
$obj2->type = 1;//image
$obj2->path  = "https://gourmetburgers.amartable.com/asset/logo.jpg"; // Replace with image handling logic
$obj2->align = 2;//0 if left, 1 if center, 2 if right; set left align for big size images
array_push($printData,$obj2);



// Address (Replace with your actual address)
$obj01 = new stdClass();
$obj01->type = 0; // Text
$obj01->content = "Naveed's Comedy Club. \nHouse 2, Road 90, \nGulshan-2, Dhaka-1212,Bangladesh";
$obj01->align = 1; // Center
array_push($printData, $obj01);

// Empty Line
$obj1 = new stdClass();
$obj1->type = 0; // Text
$obj1->content = " ";
array_push($printData, $obj1);

// Order Summary (Text)
$obj2 = new stdClass();
$obj2->type = 0; // Text
$obj2->content = "Order Summary";
$obj2->bold = 1; // Bold
$obj2->align = 1; // Center
$obj2->format = 3; // Double Width
array_push($printData, $obj2);

// Order ID
$obj3 = new stdClass();
$obj3->type = 0; // Text
$obj3->content = "Order ID: " . $order_id;
array_push($printData, $obj3);
date_default_timezone_set('Asia/Dhaka'); 
// Timestamp (Get current timestamp)
$obj4 = new stdClass();
$obj4->type = 0; // Text
$obj4->content = "Order Time: " . date("Y-m-d H:i:s"); 
array_push($printData, $obj4);

// Empty Line
array_push($printData, $obj1);
$total=0;
// Order Details (Table-like format)
foreach ($order_data as $order_id => $items) {
    foreach ($items as $item) {
        // Item Name
        $obj5 = new stdClass();
        $obj5->type = 0;
        $obj5->content = $item['name'];
        array_push($printData, $obj5);

        // Quantity and Price
        $obj6 = new stdClass();
        $obj6->type = 0;
        $obj6->content = "Qty: " . $item['quantity'] . "  Price: " . number_format($item['price'], 2);
        $total+=($item['quantity']*$item['price']);
        $obj6->content.="\n________________________________";
        array_push($printData, $obj6);

        // Empty Line
        array_push($printData, $obj1);
    }
}

// Total Amount
$obj7 = new stdClass();
$obj7->type = 0;
$obj7->content = "Total Amount: " . number_format($total, 2); // Assuming total_amount is in the first item of $items
$obj7->bold = 1;
array_push($printData, $obj7);


// Total Amount
$obj7 = new stdClass();
$obj7->type = 0;
$obj7->content = "\n______________________\nwww.amartable.com"; // Assuming total_amount is in the first item of $items
$obj7->bold = 1;
array_push($printData, $obj7);

// Output JSON
echo json_encode($printData, JSON_FORCE_OBJECT);
?>