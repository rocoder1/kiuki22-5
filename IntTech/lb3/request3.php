<?php
include("connect.php");

if (!isset($_GET['start_date']) || empty($_GET['start_date']) || !isset($_GET['end_date']) || empty($_GET['end_date'])) {
    die(json_encode(['error' => 'Помилка: необхідно вибрати обидві дати!']));
}

$start_date = date('Y-m-d', strtotime(str_replace('.', '-', $_GET['start_date'])));
$end_date = date('Y-m-d', strtotime(str_replace('.', '-', $_GET['end_date'])));
$format = $_GET['format'] ?? 'html';

try {
    $sqlSelect = "SELECT ID_FILM, name, date, country, director 
                  FROM film 
                  WHERE date BETWEEN ? AND ?";
    
    $stmt = $dbh->prepare($sqlSelect);
    $stmt->execute([$start_date, $end_date]);
    $films = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if ($format === 'json') {
        header('Content-Type: application/json');
        echo json_encode($films);
    }
} catch (PDOException $ex) {
    if ($format === 'json') {
        header('Content-Type: application/json');
        echo json_encode(['error' => $ex->getMessage()]);
    }
}

$dbh = null;
?>