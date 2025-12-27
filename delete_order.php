<?php
include('db.php');

$id = $_GET['id'];

try {
    $stmt = $conn->prepare( "SELECT invoice_file FROM orders WHERE id=$id");
    $stmt->execute();
    $data = $stmt->fetch(PDO::FETCH_ASSOC);
    $file_to_delete = "uploads/invoices/" . $data['invoice_file'];

    if(file_exists($file_to_delete)) {
        unlink($file_to_delete);
    }

    $sql = "DELETE FROM orders WHERE id=$id";
    $conn->exec($sql);
    header("Location: index.php");
} catch(PDOException $e) {
    echo $sql . "<br>" . $e->getMessage();
}
?>
