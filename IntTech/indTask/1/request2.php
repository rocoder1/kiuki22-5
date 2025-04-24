<?php
include("connect.php");
include("sqlite_logger.php");

if (!isset($_GET['actor']) || empty($_GET['actor'])) {
    die("<p style='color:red;'>Помилка: не вибрано актора!</p>");
}

$actor = $_GET['actor'];
logRequest('actor_search', $actor); // Додано логування


try {
    $sqlSelect = "SELECT f.ID_FILM, f.name, f.date, f.country, f.director 
                  FROM film f 
                  JOIN film_actor fa ON f.ID_FILM = fa.FID_Film 
                  JOIN actor a ON fa.FID_Actor = a.ID_Actor 
                  WHERE a.name = ?";
    
    $stmt = $dbh->prepare($sqlSelect);
    $stmt->execute([$actor]);
    $films = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if ($films) {
        echo "<h2>Фільми з актором: " . htmlspecialchars($actor) . "</h2>";
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
        echo "<p>Фільмів з цим актором не знайдено.</p>";
    }
} catch (PDOException $ex) {
    echo "<p style='color:red;'>Помилка: " . htmlspecialchars($ex->getMessage()) . "</p>";
}

$dbh = null;
?>
