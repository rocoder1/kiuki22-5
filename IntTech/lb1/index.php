<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Лабораторна робота №1</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <form action="request1.php" method="get">
        <label for="genre">Оберіть жанр:</label>
        <select name="genre" id="genre">
            <option value="Action">Action</option>
            <option value="Adventure">Adventure</option>
            <option value="Comedy">Comedy</option>
            <option value="Fantasy">Fantasy</option>
            <option value="Sci-Fi">Sci-Fi</option>
            <option value="Thriller">Thriller</option>
        </select>
        <input type="submit" value="Шукати">
    </form>
    
    <form action="request2.php" method="get">
        <label for="actor">Оберіть актора:</label>
        <select name="actor" id="actor">
            <option value="Vin Diesel">Vin Diesel</option>
            <option value="Chris Pratt">Chris Pratt</option>
            <option value="Zoe Saldana">Zoe Saldana</option>
            <option value="Jordana Brewster">Jordana Brewster</option>
            <option value="Bryce Dallas Howard">Bryce Dallas Howard</option>
            <option value="Paul Walker">Paul Walker</option>
            <option value="Sam Worthington">Sam Worthington</option>
            <option value="Sigourney Weaver">Sigourney Weaver</option>
        </select>
        <input type="submit" value="Шукати">
    </form>
    
    <form action="request3.php" method="get">
        <label for="start_date">Початкова дата:</label>
        <input type="date" name="start_date" id="start_date" required>
        
        <label for="end_date">Кінцева дата:</label>
        <input type="date" name="end_date" id="end_date" required>
        
        <input type="submit" value="Шукати">
    </form>
</body>
</html>