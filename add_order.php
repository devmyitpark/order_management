
<?php
include('db.php');

if(isset($_POST['submit'])) {
    $customer_id = $_POST['customer_id'];
    $category_id = $_POST['category_id'];
    $amount = $_POST['amount'];
    $status = $_POST['status'];
    $date = $_POST['order_date'];

    $file = $_FILES['invoice'];
    $filename = time() . "_" . $file['name'];
    $tempname = $file['tmp_name'];
    $filesize = $file['size'];
    $fileext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

    $allowedext = array("pdf", "jpg", "png", "jpeg");
    $maxsize = 2 * 1024 * 1024;

    if(!in_array($fileext, $allowedext)) {
        echo "Error: Only PDF, JPG, and PNG files are allowed.";
    } elseif($filesize > $maxsize) {
        echo "Error: File size must be less than 2MB.";
    } elseif($amount <= 0) {
        echo "Error: Order amount must be greater than 0.";
    } else {
        $target_dir = "uploads/invoices/";


        if (move_uploaded_file($tempname, $target_dir . $filename)) {
            try {
                $sql = "INSERT INTO orders (customer_id, category_id, order_amount, invoice_file, order_status, order_date)
                        VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$customer_id, $category_id, $amount, $filename, $status, $date]);

            } catch(PDOException $e) {
                echo "Database Error: " . $e->getMessage();
            }
        } else {
            echo "Error: Could not upload file.";
        }
    }
}
?>
<form method="POST" enctype="multipart/form-data">
    Customer:
    <select name="customer_id">
        <?php
        $stmt = $conn->query("SELECT * FROM customers");
        while($c = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<option value='{$c['id']}'>{$c['name']}</option>";
        }
        ?>
    </select><br>

    Category:
    <select name="category_id">
        <?php
        $stmt = $conn->query("SELECT * FROM categories WHERE status=1");
        while($cat = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<option value='{$cat['id']}'>{$cat['category_name']}</option>";
        }
        ?>
    </select><br>

    Amount: <input type="number" step="1" name="amount" required><br>
    Order Date: <input type="date" name="order_date" required><br>
    Status:
    <select name="status">
        <option value="Pending">Pending</option>
        <option value="Completed">Completed</option>
        <option value="Cancelled">Cancelled</option>
    </select><br>
    Invoice (Only PDF/JPG/PNG, Max 2MB): <input type="file" name="invoice" required><br>
    <button type="submit" name="submit">Save Order</button>
</form>


