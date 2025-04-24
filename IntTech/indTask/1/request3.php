<?php
include("connect.php");
include("sqlite_logger.php");

if (!isset($_GET['start_date']) || empty($_GET['start_date']) || !isset($_GET['end_date']) || empty($_GET['end_date'])) {
    die("<p style='color:red;'>Помилка: необхідно вибрати обидві дати!</p>");
}

$start_date = $_GET['start_date'];
$end_date = $_GET['end_date'];
logRequest('date_range_search', $start_date, $end_date); 

try {
    $sqlSelect = "SELECT ID_FILM, name, date, country, director 
                  FROM film 
                  WHERE date BETWEEN ? AND ?";
    
    $stmt = $dbh->prepare($sqlSelect);
    $stmt->execute([$start_date, $end_date]);
    $films = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if ($films) {
        echo "<h2>Фільми з " . htmlspecialchars($start_date) . " по " . htmlspecialchars($end_date) . "</h2>";
        echo "<table border='1'>";
        echo "<thead><tr><th>ID</th><th>Назва</th><th>Дата виходу</th><th>Країна</th><th>Режисер</th></tr></thead><tbody>";
        
        foreach ($films as $film) {
            echo "<tr>";
            echo "<td>" . $film['ID_FILM'] . "</td>";
            echo "<td>" . htmlspecialchars($film['name']) . "</td>";
            echo "<td>" . $film['date'] . "</td>";
            echo "<td>" . htmlspecialchars($film['country']) . "</td>";
            echo "<td>" . htmlspecialchars($film['director']) . "</td>";
            echo "</tr>";
        }
        echo "</tbody></table>";
    } else {
        echo "<p>Фільмів у вказаному інтервалі не знайдено.</p>";
    }
} catch (PDOException $ex) {
    echo "<p style='color:red;'>Помилка: " . htmlspecialchars($ex->getMessage()) . "</p>";
}

$dbh = null;
?>
