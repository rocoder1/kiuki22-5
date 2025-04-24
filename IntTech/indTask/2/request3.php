<?php
include("connect.php");

if (!isset($_GET['start_date']) || empty($_GET['start_date']) || !isset($_GET['end_date']) || empty($_GET['end_date'])) {
    die(json_encode(['error' => 'Помилка: необхідно вибрати обидві дати!']));
}

$start_date = date('Y-m-d', strtotime(str_replace('.', '-', $_GET['start_date'])));
$end_date = date('Y-m-d', strtotime(str_replace('.', '-', $_GET['end_date'])));
$format = $_GET['format'] ?? 'html';

if (isset($_GET['log_data'])) {
    $logData = json_decode($_GET['log_data'], true);
    $endpoint = basename($_SERVER['SCRIPT_NAME']);
    $params = json_encode($_GET);
    
    $sqlLog = "INSERT INTO request_logs (request_time, endpoint, parameters, user_agent, latitude, longitude) 
               VALUES (:request_time, :endpoint, :parameters, :user_agent, :latitude, :longitude)";
    
    $stmtLog = $dbh->prepare($sqlLog);
    $stmtLog->bindParam(':request_time', $logData['request_time']);
    $stmtLog->bindParam(':endpoint', $endpoint);
    $stmtLog->bindParam(':parameters', $params);
    $stmtLog->bindParam(':user_agent', $logData['user_agent']);
    $stmtLog->bindParam(':latitude', $logData['latitude']);
    $stmtLog->bindParam(':longitude', $logData['longitude']);
    $stmtLog->execute();
}

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