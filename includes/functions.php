<?php
session_start();

function setFlash($type, $message) {
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message
    ];
}

function getFlash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

function redirect($url) {
    header("Location: $url");
    exit();
}

function getOperators($pdo) {
    $stmt = $pdo->query("SELECT * FROM operators ORDER BY name");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getCategories($pdo) {
    $stmt = $pdo->query("SELECT * FROM categories ORDER BY name");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getSubcategories($pdo, $categoryId = null) {
    if ($categoryId) {
        $stmt = $pdo->prepare("SELECT * FROM subcategories WHERE category_id = ? ORDER BY name");
        $stmt->execute([$categoryId]);
    } else {
        $stmt = $pdo->query("SELECT * FROM subcategories ORDER BY name");
    }
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>