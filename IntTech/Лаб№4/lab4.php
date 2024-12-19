<?php

$page = $_GET['page'] ?? 'article'; 
$filePath = $page . '.txt'; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $newContent = $_POST['content']; 
    
    if (!empty($newContent)) {
        file_put_contents($filePath, $newContent); 
    }
}

if (file_exists($filePath)) {
    $content = file_get_contents($filePath); 
} else {
    $content = ''; 
}
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Лабораторна робота №4</title>
</head>
<body bgcolor="#FFEBCD">
    <h1>Поточний вміст сторінки </h1>
     <div>
        <p><?php echo nl2br(htmlspecialchars($content)); ?></p> 
    </div>

    <h2>Редагування сторінки:</h2>
    <form method="POST">
        <textarea name="content" rows="10" cols="50"><?php echo htmlspecialchars($content); ?></textarea><br>
        <input type="submit" value="Зберегти">
    </form>
</body>
</html>

