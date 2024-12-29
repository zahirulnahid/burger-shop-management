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

// Fetch menu items grouped by category
$sql = "
    SELECT 
        menu_category.name AS category_name, 
        menu_items.id, 
        menu_items.name, 
        menu_items.price, 
        menu_items.image 
    FROM menu_items 
    INNER JOIN menu_category 
    ON menu_items.category_id = menu_category.id 
    ORDER BY menu_category.id, menu_items.name";
$result = $conn->query($sql);

$menu = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $menu[$row['category_name']][] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>The Gourmet Burgers</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&family=Kalam:wght@700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #ffeede;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .navbar {
            width: 100%;
            background-color: #ffa401;
            color: #fff;
            padding: 1rem;
            text-align: center;
            font-size: 1.5rem;
            font-family: 'Kalam', cursive;
        }

.main-frame {
    width: 100%;
    padding: 1rem;
    display: flex;
    flex-direction: column;
    gap: 1rem;
    margin-bottom: 100px; /* Add bottom margin to prevent overlap with the footer */
}

        .category {
            margin-top: 2rem;
        }

        .category-title {
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 1rem;
            text-transform: uppercase;
            color: #ffa401;
            border-bottom: 2px solid #ffa401;
        }

        .food-card {
            display: flex;
            align-items: center;
            background-color: #ffffffb2;
            border-radius: 8px;
            padding: 1rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 1rem;
        }

        .food-card img {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 1rem;
        }

        .food-details {
            flex: 1;
        }

        .food-name {
            font-size: 1.2rem;
            font-weight: 600;
            color: #333;
        }

        .food-price {
            font-size: 1rem;
            color: #777;
            margin-top: 0.5rem;
        }

        .quantity {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .quantity button {
            width: 32px;
            height: 32px;
            background-color: #bdf1cd;
            border: none;
            border-radius: 4px;
            font-size: 1.2rem;
            cursor: pointer;
        }

        .quantity-value {
            font-size: 1rem;
            font-weight: 600;
        }



.footer {
    position: fixed;
    bottom: 0;
    width: 100%;
    background-color: #ffa401;
    text-align: center;
    padding: 1rem;
    z-index: 10; /* Ensure it floats above other elements */
}

.footer button {
    background-color: #180580;
    color: white; /* Font color */
    font-size: 1.2rem;
    font-weight: bold;
    border: none;
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    cursor: pointer;
}

.total-amount {
    color: #fff;
    font-size: 1rem;
    margin-top: 0.5rem;
}

    </style>
</head>
<body>
<div class="navbar" style="display: flex; align-items: center; justify-content: center; padding: 10px; position: relative;">
  <a href="orderlist.php" style="position: absolute; left: 10px; text-decoration: none;">
    <button style="background-color: #ff9800; border: none; color: white; padding: 5px 10px; border-radius: 5px; cursor: pointer;">
      Orders
    </button>
  </a>
  <span>The <span style="color:black">Gourmet</span> Burgers</span>
</div>




    <div class="main-frame">
        <?php foreach ($menu as $category_name => $items): ?>
            <div class="category">
                <div class="category-title"><?php echo htmlspecialchars($category_name); ?></div>
                <?php foreach ($items as $item): ?>
                    <div class="food-card">
                        <img src="img/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                        <div class="food-details">
                            <div class="food-name"><?php echo htmlspecialchars($item['name']); ?></div>
                            <div class="food-price">৳ <?php echo number_format($item['price'], 2); ?></div>
                        </div>
                        <div class="quantity">
                            <button class="decrement">-</button>
                            <span class="quantity-value">0</span>
                            <button class="increment">+</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    </div>
    <div class="footer">
        <div class="total-amount">Total: ৳ 0</div>
        <button onclick="confirmOrder()">Confirm Order</button>
    </div>

<script>
    // Function to calculate the total amount
    function updateTotal() {
        let total = 0;
        document.querySelectorAll('.food-card').forEach((card) => {
            const price = parseFloat(card.querySelector('.food-price').textContent.replace('৳', '').trim());
            const quantity = parseInt(card.querySelector('.quantity-value').textContent);
            total += price * quantity;
        });
        document.querySelector('.total-amount').textContent = `Total: ৳ ${total.toFixed(2)}`;
    }

    // Increment button logic
    document.querySelectorAll('.increment').forEach((btn) => {
        btn.addEventListener('click', (e) => {
            const quantity = e.target.previousElementSibling;
            quantity.textContent = parseInt(quantity.textContent) + 1;
            updateTotal(); // Update total after increment
        });
    });

    // Decrement button logic
    document.querySelectorAll('.decrement').forEach((btn) => {
        btn.addEventListener('click', (e) => {
            const quantity = e.target.nextElementSibling;
            if (parseInt(quantity.textContent) > 0) { // Ensure it doesn't go below 0
                quantity.textContent = parseInt(quantity.textContent) - 1;
                updateTotal(); // Update total after decrement
            }
        });
    });

    // Initialize total to 0
    updateTotal();
</script>
<script>
    function confirmOrder() {
        const order = [];
        let totalAmount = 0;

        document.querySelectorAll('.food-card').forEach((card) => {
            const id = card.getAttribute('data-id');
            const name = card.querySelector('.food-name').textContent.trim();
            const price = parseFloat(card.querySelector('.food-price').textContent.replace('৳', '').trim());
            const quantity = parseInt(card.querySelector('.quantity-value').textContent);

            if (quantity > 0) {
                totalAmount += price * quantity;
                order.push({ id, name, price, quantity });
            }
        });

        if (order.length === 0) {
            alert("No items selected!");
            return;
        }

        // Pass order details to the invoice page
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = 'invoice.php';

        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'order';
        input.value = JSON.stringify(order);

        const totalInput = document.createElement('input');
        totalInput.type = 'hidden';
        totalInput.name = 'totalAmount';
        totalInput.value = totalAmount;

        form.appendChild(input);
        form.appendChild(totalInput);

        document.body.appendChild(form);
        form.submit();
    }
</script>
    </script>
</body>
</html>
