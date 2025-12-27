

<?php
include('db.php');

if(isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM orders WHERE id = ?");
    $stmt->execute([$id]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);
}

if(isset($_POST['update'])) {
    $order_id = $_POST['order_id'];
    $customer_id = $_POST['customer_id'];
    $category_id = $_POST['category_id'];
    $amount = $_POST['amount'];
    $status = $_POST['status'];
    $date = $_POST['order_date'];
    $filename = $_POST['old_invoice'];
    $upload_ok = true;
    $target_dir = "uploads/invoices/";


    if(!empty($_FILES['invoice']['name'])) {
        $file = $_FILES['invoice'];
        $new_filename =  $file['name'];
        $file_ext = strtolower(pathinfo($new_filename, PATHINFO_EXTENSION));



        if(  $file_ext != "pdf" &&
    $file_ext != "jpg" &&
    $file_ext != "png" &&
    $file_ext != "jpeg") {
            echo "Error: Invalid file format.";
            $upload_ok = false;
        } elseif($file['size'] > (2 * 1024 * 1024)) {
            echo "Error: File size exceeds 2MB.";
            $upload_ok = false;
        } else {

            $old_file_path = $target_dir . $_POST['old_invoice'];
            if(!empty($_POST['old_invoice']) && file_exists($old_file_path)) {
                unlink($old_file_path);
            }


            if(move_uploaded_file($file['tmp_name'], $target_dir . $new_filename)) {
                $filename = $new_filename;
            } else {
                echo "Error: Failed to move new file.";
                $upload_ok = false;
            }
        }
    }

    if($upload_ok) {
        $sql = "UPDATE orders SET customer_id=?, category_id=?, order_amount=?, invoice_file=?, order_status=?, order_date=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$customer_id, $category_id, $amount, $filename, $status, $date, $order_id]);


        exit();
    }
}
?>
<h2>Edit Order</h2>
<form method="POST" enctype="multipart/form-data">
    <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
    <input type="hidden" name="old_invoice" value="<?php echo $order['invoice_file']; ?>">

    Customer:
    <select name="customer_id">
        <?php
        $c_stmt = $conn->query("SELECT * FROM customers");
        while($c = $c_stmt->fetch()) {
            $sel = ($c['id'] == $order['customer_id']) ? "selected" : "";
            echo "<option value='{$c['id']}' $sel>{$c['name']}</option>";
        }
        ?>
    </select><br><br>

    Category:
    <select name="category_id">
        <?php
        $cat_stmt = $conn->query("SELECT * FROM categories WHERE status=1");
        while($cat = $cat_stmt->fetch()) {
            $sel = ($cat['id'] == $order['category_id']) ? "selected" : "";
            echo "<option value='{$cat['id']}' $sel>{$cat['category_name']}</option>";
        }
        ?>
    </select><br><br>

    Amount: <input type="number" step="0.01" name="amount" value="<?php echo $order['order_amount']; ?>" required><br><br>

    Order Date: <input type="date" name="order_date" value="<?php echo $order['order_date']; ?>" required><br><br>

    Status:
    <select name="status">
        <option value="Pending" <?php if($order['order_status']=='Pending') echo 'selected'; ?>>Pending</option>
        <option value="Completed" <?php if($order['order_status']=='Completed') echo 'selected'; ?>>Completed</option>
        <option value="Cancelled" <?php if($order['order_status']=='Cancelled') echo 'selected'; ?>>Cancelled</option>
    </select><br><br>


    Modify Invoice (Optional): <input type="file" name="invoice"><br><br>

    <button type="submit" name="update">Update Order</button>
    <a href="index.php">Cancel</a>
</form>

