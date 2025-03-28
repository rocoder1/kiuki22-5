<?php
include("connect.php");

if (!isset($_GET['actor']) || empty($_GET['actor'])) {
    die("<p style='color:red;'>Помилка: не вибрано актора!</p>");
}

$actor = $_GET['actor'];
$format = $_GET['format'] ?? 'html';

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
        if ($format === 'xml') {
            header('Content-Type: text/xml');
            echo '<?xml version="1.0" encoding="UTF-8"?>';
            echo '<films>';
            foreach ($films as $film) {
                echo '<film>';
                echo '<ID_FILM>' . $film['ID_FILM'] . '</ID_FILM>';
                echo '<name>' . htmlspecialchars($film['name']) . '</name>';
                echo '<date>' . $film['date'] . '</date>';
                echo '<country>' . htmlspecialchars($film['country']) . '</country>';
                echo '<director>' . htmlspecialchars($film['director']) . '</director>';
                echo '</film>';
            }
            echo '</films>';
        }
    } else {
        if ($format === 'xml') {
            header('Content-Type: text/xml');
            echo '<?xml version="1.0" encoding="UTF-8"?>';
            echo '<films></films>';
        }
    }
} catch (PDOException $ex) {
    if ($format === 'xml') {
        header('Content-Type: text/xml');
        echo '<?xml version="1.0" encoding="UTF-8"?>';
        echo '<error>' . htmlspecialchars($ex->getMessage()) . '</error>';
    }
}

$dbh = null;
?>