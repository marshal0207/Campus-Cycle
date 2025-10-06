<?php
include 'config.php';
if(isset($_GET['id'])){
    $id = $_GET['id'];
    $stmt = $conn->prepare("DELETE FROM events WHERE id=?");
    $stmt->bind_param("i",$id);
    $stmt->execute();
    $stmt->close();
}
header('Location: events.php');
?>
