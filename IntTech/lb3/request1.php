<?php
include("connect.php");

$genre = $_GET['genre'];
$format = $_GET['format'] ?? 'html';

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