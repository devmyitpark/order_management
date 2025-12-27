<?php
include('db.php');

if(isset($_POST['submit'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];

    try {

        $check_stmt = $conn->prepare("SELECT email FROM customers WHERE email = ?");
        $check_stmt->execute([$email]);

        if($check_stmt->rowCount() > 0) {
            echo "<script>alert('Error: This email is already registered!');</script>";
        } else {

            $sql = "INSERT INTO customers (name, email, phone) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$name, $email, $phone]);

            echo "<script>alert('Customer added successfully!'); window.location='index.php';</script>";
        }
    } catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>

<h2>Add New Customer</h2>
<form method="POST">
    Name: <input type="text" name="name" required><br><br>

    Email: <input type="email" name="email" required><br><br>

    Phone: <input type="text" name="phone" required><br><br>

    <button type="submit" name="submit">Add Customer</button>
    <a href="index.php">Back to List</a>
</form>
