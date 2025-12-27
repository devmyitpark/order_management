<?php
include('db.php');

$id = $_GET['id'];

try {
    $stmt = $conn->prepare( "SELECT invoice_file FROM orders WHERE id=$id");
    $stmt->execute();
    $data = $stmt->fetch(PDO::FETCH_ASSOC);
    $filetodlt = "uploads/invoices/" . $data['invoice_file'];

    if(file_exists($filetodlt)) {
        unlink($filetodlt);
    }

    $sql = "DELETE FROM orders WHERE id=$id";
    $conn->exec($sql);
    header("Location: index.php");
} catch(PDOException $e) {
    echo $sql . "<br>" . $e->getMessage();
}
?>
