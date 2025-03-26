<?php
require 'vendor/autoload.php';

use MongoDB\Client;

$client = new Client("mongodb://localhost:27017");
$db = $client->library;
$collection = $db->literary_resources;

// Функція для відображення результатів
function displayResults($results, $title) {
    echo "<h3>$title</h3>";
    echo "<ul>";
    foreach ($results as $result) {
        echo "<li style='margin-bottom:15px;padding-bottom:10px;border-bottom:1px solid #eee'>";
        echo "<div><b>Назва:</b> " . htmlspecialchars($result['title']) . "</div>";
        echo "<div><b>Рік:</b> " . htmlspecialchars($result['year']) . "</div>";
        echo "<div><b>Видавництво:</b> " . htmlspecialchars($result['publisher']) . "</div>";
        if (!empty($result['authors'])) {
            echo "<div><b>Автори:</b> " . htmlspecialchars($result['authors']) . "</div>";
        }
        echo "</li>";
    }
    echo "</ul>";
}

// Обробка вибору користувача
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $searchType = $_POST['search_type'] ?? '';
    $searchData = [];
    $results = [];
    
    echo "<div style='margin:20px;padding:15px;background:#f5f5f5;border-radius:5px'>";
    
    switch ($searchType) {
        case 'publisher':
            $publisherName = $_POST['publisher'] ?? '';
            if (!empty($publisherName)) {
                $cursor = $collection->find(['publisher' => $publisherName]);
                foreach ($cursor as $doc) {
                    $results[] = [
                        'title' => $doc['title'] ?? 'Невідомо',
                        'year' => $doc['year'] ?? 'Невідомо',
                        'publisher' => $doc['publisher'] ?? 'Невідомо',
                        'authors' => isset($doc['authors']) ? implode(", ", (array)$doc['authors']) : ''
                    ];
                }
                $searchData = ['type' => 'publisher', 'value' => $publisherName];
                displayResults($results, "Література видавництва: $publisherName");
            } else {
                echo "<p>Введіть назву видавництва</p>";
            }
            break;
            
        case 'year_range':
            $startYear = (int)($_POST['start_year'] ?? 0);
            $endYear = (int)($_POST['end_year'] ?? 0);
            if ($startYear > 0 && $endYear > 0 && $startYear <= $endYear) {
                $cursor = $collection->find(['year' => ['$gte' => $startYear, '$lte' => $endYear]]);
                foreach ($cursor as $doc) {
                    $results[] = [
                        'title' => $doc['title'] ?? 'Невідомо',
                        'year' => $doc['year'] ?? 'Невідомо',
                        'publisher' => $doc['publisher'] ?? 'Невідомо',
                        'authors' => isset($doc['authors']) ? implode(", ", (array)$doc['authors']) : ''
                    ];
                }
                $searchData = ['type' => 'year_range', 'start' => $startYear, 'end' => $endYear];
                displayResults($results, "Література за $startYear-$endYear роки");
            } else {
                echo "<p>Введіть коректний діапазон років</p>";
            }
            break;
            
        case 'author':
            $authorName = $_POST['author'] ?? '';
            if (!empty($authorName)) {
                $cursor = $collection->find(['authors' => $authorName]);
                foreach ($cursor as $doc) {
                    $results[] = [
                        'title' => $doc['title'] ?? 'Невідомо',
                        'year' => $doc['year'] ?? 'Невідомо',
                        'publisher' => $doc['publisher'] ?? 'Невідомо',
                        'authors' => isset($doc['authors']) ? implode(", ", (array)$doc['authors']) : ''
                    ];
                }
                $searchData = ['type' => 'author', 'value' => $authorName];
                displayResults($results, "Книги автора: $authorName");
            } else {
                echo "<p>Введіть ім'я автора</p>";
            }
            break;
            
        case 'all':
            $cursor = $collection->find([]);
            foreach ($cursor as $doc) {
                $results[] = [
                    'title' => $doc['title'] ?? 'Невідомо',
                    'year' => $doc['year'] ?? 'Невідомо',
                    'publisher' => $doc['publisher'] ?? 'Невідомо',
                    'authors' => isset($doc['authors']) ? implode(", ", (array)$doc['authors']) : ''
                ];
            }
            $searchData = ['type' => 'all'];
            displayResults($results, "Вся література");
            break;
            
        default:
            echo "<p>Виберіть тип пошуку</p>";
    }
    
    if (!empty($results) && !empty($searchData)) {
        echo "<script>
            const searchData = ".json_encode($searchData).";
            const results = ".json_encode($results).";
            
            // Зберігаємо поточний пошук
            localStorage.setItem('currentSearch', JSON.stringify({searchData, results}));
            
            // Додаємо до історії
            let history = JSON.parse(localStorage.getItem('searchHistory')) || [];
            history.unshift({searchData, results, timestamp: new Date().toLocaleString()});
            if (history.length > 5) history = history.slice(0, 5);
            localStorage.setItem('searchHistory', JSON.stringify(history));
            
            // Оновлюємо відображення історії
            updateHistory();
        </script>";
    }
    
    echo "</div>";
    echo "<p><a href='".$_SERVER['PHP_SELF']."' style='display:inline-block;margin:10px;padding:8px 15px;background:#eee;border-radius:4px;text-decoration:none;'>← Назад</a></p>";
} else {
    // Відображення форми
    ?>
    <!DOCTYPE html>
    <html lang="uk">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Пошук літератури</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                max-width: 800px;
                margin: 0 auto;
                padding: 20px;
                line-height: 1.5;
                background:rgb(175, 171, 206);
            }
            h2 {
                color: #333;
                margin-bottom: 20px;
            }
            form {
                margin-bottom: 20px;
            }ы
            select, input[type="text"], input[type="number"] {
                width: 100%;
                padding: 8px;
                margin: 5px 0 15px;
                box-sizing: border-box;
                border: 1px solid #ddd;
                border-radius: 4px;
            }
            input[type="submit"] {
                background:rgb(131, 84, 167);
                color: white;
                border: none;
                padding: 10px 15px;
                border-radius: 4px;
                cursor: pointer;
            }
            .search-option {
                display: none;
                margin: 10px 0;
            }
            .history-item {
                padding: 10px;
                margin: 5px 0;
                background:rgb(228, 226, 226);
                border-radius: 4px;
                cursor: pointer;
            }
            .history-item:hover {
                background:rgb(180, 124, 124);
            }
            #historyResults {
                margin-top: 20px;
            }
        </style>
    </head>
    <body>
        <h2>Пошук літератури</h2>
        
        <form method="post">
            <div>
                <label for="search_type">Тип пошуку:</label>
                <select name="search_type" id="search_type" onchange="showSearchOptions(this.value)" required>
                    <option value="">-- Виберіть --</option>
                    <option value="publisher">По видавництву</option>
                    <option value="year_range">За роками</option>
                    <option value="author">По автору</option>
                    <option value="all">Всі записи</option>
                </select>
            </div>
            
            <div id="publisher_fields" class="search-option">
                <label for="publisher">Назва видавництва:</label>
                <input type="text" name="publisher" id="publisher">
            </div>
            
            <div id="year_fields" class="search-option">
                <label for="start_year">Від року:</label>
                <input type="number" name="start_year" id="start_year" min="1900">
                
                <label for="end_year">До року:</label>
                <input type="number" name="end_year" id="end_year" min="1900">
            </div>
            
            <div id="author_fields" class="search-option">
                <label for="author">Ім'я автора:</label>
                <input type="text" name="author" id="author">
            </div>
            
            <input type="submit" value="Пошук">
        </form>
        
        <div id="historyContainer">
            <h3>Історія пошуку</h3>
            <div id="historyResults"></div>
        </div>

        <script>
            // Показуємо відповідні поля вводу
            function showSearchOptions(type) {
                document.querySelectorAll('.search-option').forEach(el => {
                    el.style.display = 'none';
                });
                
                if (type === 'publisher') {
                    document.getElementById('publisher_fields').style.display = 'block';
                } else if (type === 'year_range') {
                    document.getElementById('year_fields').style.display = 'block';
                } else if (type === 'author') {
                    document.getElementById('author_fields').style.display = 'block';
                }
            }
            
            // Оновлюємо відображення історії
            function updateHistory() {
                const history = JSON.parse(localStorage.getItem('searchHistory')) || [];
                const container = document.getElementById('historyResults');
                
                if (history.length === 0) {
                    container.innerHTML = '<p>Немає збережених запитів</p>';
                    return;
                }
                
                let html = '';
                history.forEach((item, index) => {
                    let title = '';
                    switch(item.searchData.type) {
                        case 'publisher':
                            title = `Видавництво: ${item.searchData.value}`;
                            break;
                        case 'year_range':
                            title = `Роки: ${item.searchData.start}-${item.searchData.end}`;
                            break;
                        case 'author':
                            title = `Автор: ${item.searchData.value}`;
                            break;
                        case 'all':
                            title = `Вся література`;
                            break;
                    }
                    
                    html += `
                        <div class="history-item" onclick="showHistoryResult(${index})">
                            <div><strong>${title}</strong></div>
                            <div style="font-size:0.8em;color:#666">${item.timestamp}</div>
                        </div>
                    `;
                });
                
                container.innerHTML = html;
            }
            
            // Показуємо результат з історії
            function showHistoryResult(index) {
                const history = JSON.parse(localStorage.getItem('searchHistory')) || [];
                if (index >= 0 && index < history.length) {
                    const item = history[index];
                    const resultDiv = document.createElement('div');
                    resultDiv.style.marginTop = '20px';
                    resultDiv.style.padding = '15px';
                    resultDiv.style.background = '#f9f9f9';
                    resultDiv.style.borderRadius = '5px';
                    
                    let title = '';
                    switch(item.searchData.type) {
                        case 'publisher':
                            title = `Література видавництва: ${item.searchData.value}`;
                            break;
                        case 'year_range':
                            title = `Література за ${item.searchData.start}-${item.searchData.end} роки`;
                            break;
                        case 'author':
                            title = `Книги автора: ${item.searchData.value}`;
                            break;
                        case 'all':
                            title = `Вся література`;
                            break;
                    }
                    
                    let html = `<h4>${title}</h4><ul>`;
                    item.results.forEach(result => {
                        html += `<li style="margin-bottom:10px;padding-bottom:10px;border-bottom:1px solid #eee">
                            <div><b>Назва:</b> ${result.title}</div>
                            <div><b>Рік:</b> ${result.year}</div>
                            <div><b>Видавництво:</b> ${result.publisher}</div>
                            ${result.authors ? `<div><b>Автори:</b> ${result.authors}</div>` : ''}
                        </li>`;
                    });
                    html += `</ul>`;
                    
                    resultDiv.innerHTML = html;
                    
                    // Додаємо кнопку закриття
                    const closeBtn = document.createElement('button');
                    closeBtn.textContent = 'Закрити';
                    closeBtn.style.marginTop = '10px';
                    closeBtn.style.padding = '5px 10px';
                    closeBtn.style.background = '#e74c3c';
                    closeBtn.style.color = 'white';
                    closeBtn.style.border = 'none';
                    closeBtn.style.borderRadius = '4px';
                    closeBtn.style.cursor = 'pointer';
                    closeBtn.onclick = function() {
                        resultDiv.remove();
                    };
                    resultDiv.appendChild(closeBtn);
                    
                    // Додаємо результат після контейнера історії
                    document.getElementById('historyContainer').appendChild(resultDiv);
                }
            }
            
            // Ініціалізація при завантаженні сторінки
            document.addEventListener('DOMContentLoaded', function() {
                updateHistory();
                
                // Відновлюємо останній пошук, якщо він є
                const currentSearch = JSON.parse(localStorage.getItem('currentSearch'));
                if (currentSearch) {
                    showHistoryResult(0); // Перший елемент - це останній пошук
                }
            });
        </script>
    </body>
    </html>
    <?php
}
?>