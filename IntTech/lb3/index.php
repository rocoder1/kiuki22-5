<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Лабораторна робота №1</title>
    <link rel="stylesheet" href="styles.css">
    <script>
        function searchByGenreHTML() {
            const genre = document.getElementById('genre').value;
            const xhr = new XMLHttpRequest();
            xhr.open('GET', `request1.php?genre=${encodeURIComponent(genre)}&format=html`, true);
            
            xhr.onload = function() {
                if (this.status === 200) {
                    document.getElementById('results').innerHTML = this.responseText;
                } else {
                    document.getElementById('results').innerHTML = 'Error occurred';
                }
            };
            
            xhr.send();
            return false;
        }
        function searchByActorXML() {
            const actor = document.getElementById('actor').value;
            const xhr = new XMLHttpRequest();
            xhr.open('GET', `request2.php?actor=${encodeURIComponent(actor)}&format=xml`, true);
            
            xhr.onload = function() {
                if (this.status === 200) {
                    const xmlDoc = this.responseXML;
                    const films = xmlDoc.getElementsByTagName('film');
                    let html = '<h2>Фільми з актором: ' + actor + '</h2>';
                    html += '<table border="1"><thead><tr><th>ID</th><th>Назва</th><th>Дата виходу</th><th>Країна</th><th>Режисер</th></tr></thead><tbody>';
                    
                    for (let i = 0; i < films.length; i++) {
                        const film = films[i];
                        html += '<tr>';
                        html += '<td>' + film.getElementsByTagName('ID_FILM')[0].textContent + '</td>';
                        html += '<td>' + film.getElementsByTagName('name')[0].textContent + '</td>';
                        html += '<td>' + film.getElementsByTagName('date')[0].textContent + '</td>';
                        html += '<td>' + film.getElementsByTagName('country')[0].textContent + '</td>';
                        html += '<td>' + film.getElementsByTagName('director')[0].textContent + '</td>';
                        html += '</tr>';
                    }
                    
                    html += '</tbody></table>';
                    document.getElementById('results').innerHTML = html;
                } else {
                    document.getElementById('results').innerHTML = 'Error occurred';
                }
            };
            
            xhr.send();
            return false;
        }
        async function searchByDateJSON() {
            const startDate = document.getElementById('start_date').value;
            const endDate = document.getElementById('end_date').value;
            
            try {
                const response = await fetch(`request3.php?start_date=${encodeURIComponent(startDate)}&end_date=${encodeURIComponent(endDate)}&format=json`);
                const films = await response.json();
                
                let html = `<h2>Фільми з ${startDate} по ${endDate}</h2>`;
                html += '<table border="1"><thead><tr><th>ID</th><th>Назва</th><th>Дата виходу</th><th>Країна</th><th>Режисер</th></tr></thead><tbody>';
                
                films.forEach(film => {
                    html += '<tr>';
                    html += `<td>${film.ID_FILM}</td>`;
                    html += `<td>${film.name}</td>`;
                    html += `<td>${film.date}</td>`;
                    html += `<td>${film.country}</td>`;
                    html += `<td>${film.director}</td>`;
                    html += '</tr>';
                });
                
                html += '</tbody></table>';
                document.getElementById('results').innerHTML = html;
            } catch (error) {
                document.getElementById('results').innerHTML = 'Error occurred';
            }
            
            return false;
        }
    </script>
</head>
<body>
    <div class="form-container">
        <form onsubmit="return searchByGenreHTML()">
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
        
        <form onsubmit="return searchByActorXML()">
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
        
       <form onsubmit="event.preventDefault(); searchByDateJSON();">
            <label for="start_date">Початкова дата:</label>
            <input type="date" name="start_date" id="start_date" required>
            
            <label for="end_date">Кінцева дата:</label>
            <input type="date" name="end_date" id="end_date" required>
            
            <input type="submit" value="Шукати">
        </form>
    </div>
    
    <div id="results" class="results-container"></div>
</body>
</html>