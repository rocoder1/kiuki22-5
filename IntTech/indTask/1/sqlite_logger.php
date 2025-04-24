<?php
// Файл для роботи з логами в SQLite
function getLogDB() {
    $logDbPath = __DIR__ . '/logs.db';
    
    try {
        // Створюємо базу даних, якщо її немає
        $logDb = new PDO('sqlite:' . $logDbPath);
        $logDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Створюємо таблицю для логів, якщо її немає
        $logDb->exec("CREATE TABLE IF NOT EXISTS request_logs (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            request_type TEXT NOT NULL,
            param1 TEXT,
            param2 TEXT,
            request_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");
        
        return $logDb;
    } catch(PDOException $e) {
        // У виробничому середовищі краще писати в файл логу, а не виводити на екран
        error_log("SQLite Error: " . $e->getMessage());
        return null;
    }
}

function logRequest($requestType, $param1 = null, $param2 = null) {
    $logDb = getLogDB();
    if ($logDb) {
        try {
            $stmt = $logDb->prepare("INSERT INTO request_logs (request_type, param1, param2) 
                                     VALUES (:request_type, :param1, :param2)");
            $stmt->bindParam(':request_type', $requestType);
            $stmt->bindParam(':param1', $param1);
            $stmt->bindParam(':param2', $param2);
            $stmt->execute();
        } catch(PDOException $e) {
            error_log("Failed to log request: " . $e->getMessage());
        }
    }
}
?>