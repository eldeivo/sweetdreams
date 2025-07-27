<?php
function verPocoStock($pdo) {
    $stmt = $pdo->prepare("SELECT * FROM vista_poco_stock");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
