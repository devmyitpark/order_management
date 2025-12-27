<?php include('db.php'); ?>
<h2>Customer Order Management</h2>

<br><br>
<table border="1" cellpadding="10">
    <tr>
        <th>ID</th>
        <th>Customer</th>
        <th>Category</th>
        <th>Amount</th>
        <th>Status</th>
        <th>Invoice</th>

        <th>Edit</th>
        <th>Delete</th>
    </tr>
    <?php
    try {
        //INNER JOIN
     $sql = "SELECT orders.*, customers.name, categories.category_name
        FROM orders
        JOIN customers ON orders.customer_id = customers.id
        JOIN categories ON orders.category_id = categories.id
        ";
        $stmt = $conn->prepare($sql);
        $stmt->execute();

        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>
                    <td>{$row['id']}</td>
                    <td>{$row['name']}</td>
                    <td>{$row['category_name']}</td>
                    <td>{$row['order_amount']}</td>
                    <td>{$row['order_status']}</td>
                    <td><a href='uploads/invoices/{$row['invoice_file']}' target='_blank'>View File</a></td>
                    <td>
                        <a href='edit_order.php?id={$row['id']}'>Edit</a>
                    </td>
                    <td>
                        <a href='delete_order.php?id={$row['id']}' onclick='return confirm(\"Are you sure?\")'>Delete</a>
                    </td>
                  </tr>";
        }
    } catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
    ?>
</table>
<h1>Features:</h1>
<a href="add_order.php">Add New Order</a> | <a href="add_customer.php">Add Customer</a>
