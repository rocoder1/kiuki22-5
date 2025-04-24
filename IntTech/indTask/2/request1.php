<?php
include("connect.php");

$genre = $_GET['genre'];
$format = $_GET['format'] ?? 'html';

// Логування запиту
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
    $sqlSelect = "SELECT f.ID_FILM, f.name, f.date, f.country, f.director 
                  FROM film f 
                  JOIN film_genre fg ON f.ID_FILM = fg.FID_Film 
                  JOIN genre g ON fg.FID_Genre = g.ID_Genre 
                  WHERE g.title = :genre";
    
    $stmt = $dbh->prepare($sqlSelect);
    $stmt->bindParam(':genre', $genre, PDO::PARAM_STR);
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        if ($format === 'html') {
            echo "<h2>Фільми жанру: " . htmlspecialchars($genre) . "</h2>";
            echo "<table border='1'>";
            echo "<thead>";
            echo "<tr><th>ID</th><th>Film Name</th><th>Release Date</th><th>Country</th><th>Director</th></tr>";
            echo "</thead>";
            echo "<tbody>";
            
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<tr>";
                echo "<td>" . $row['ID_FILM'] . "</td>";
                echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                echo "<td>" . $row['date'] . "</td>";
                echo "<td>" . htmlspecialchars($row['country']) . "</td>";
                echo "<td>" . htmlspecialchars($row['director']) . "</td>";
                echo "</tr>";
            }
            
            echo "</tbody>";
            echo "</table>";
        }
    } else {
        echo "No results found!";
    }
} catch(PDOException $ex) {
    echo "Error: " . $ex->getMessage();
}

$dbh = null;
?>