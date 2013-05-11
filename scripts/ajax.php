<?php

session_start();
require 'database.php';

function clean($string) {
    return mysql_real_escape_string(strip_tags($string, "<strong>"));
}

function link_uses_http($link) {
    $prefix = "http";
    for ($i = 0; $i < 4; $i++) {
        if ($link[$i] != $prefix[$i]) {
            if ($link[4] != "s")
                return false;
        }
    }

    return true;
}

function get_ID($title) {
    // Return ID of song of given title with highest rating
    if ($title != "") {
        $row = mysql_fetch_array(mysql_query("
            SELECT * FROM songs WHERE LOWER(title) = LOWER('{$title}')
            AND rating = (SELECT MAX(rating) FROM songs WHERE LOWER(title) = LOWER('{$title}'));
        "));
    }
    
    return $row['id'];
}

switch ($_POST['action']) {
    case 'addSong':
        if (!isset($_SESSION['adds']))
            break;
        if ($_SESSION['adds'] > 15) {
            echo("OVERLOAD");
            break;
        }

        $title = clean($_POST['title']);
        $artist = clean($_POST['artist']);
        $link = clean($_POST['link']);
        
        if (link_uses_http($link) && $title != "" && $artist != "" && $link != "") {
            $result = mysql_query("
                INSERT INTO songs (title, artist, link, rating)
                VALUES ('{$title}', '{$artist}', '{$link}', 0);
            ", $conn);
            
            if ($result) {
                $_SESSION['adds']++;
                echo("ADDED");   
            }
        }
        
        break;
        
    case 'getID':
        $title = "";
        
        if (isset($_POST['title']))
            $title = clean($_POST['title']);
            
        echo(get_ID($title));
        break;
        
    case 'getLink': // ...by ID to play song
        if (isset($_POST['ID'])) {
            $ID = clean($_POST['ID']);
            $row = mysql_fetch_array(mysql_query("
                SELECT * FROM songs WHERE id = {$ID};
            "));
        }

        echo($row['link']);
        break;
        
    case 'addPlaylist':
        if (!isset($_SESSION['adds']))
            break;
        if ($_SESSION['adds'] > 5)
            break;
        if (isset($_POST['OBJ']) && isset($_POST['PLN'])) {
            $pl_obj = clean($_POST['OBJ']);
            $pl_name = clean($_POST['PLN']);
            
            if ($pl_name != "" && $pl_obj != "") {
                $result = mysql_query("
                    INSERT INTO playlists (name, playlist, rating)
                    VALUES ('{$pl_name}', '{$pl_obj}', 0);
                ");
                
                if ($result) {
                    $_SESSION['adds']++;
                    echo('ADDED');
                }
            }
        }
        
        break;
        
    case 'getPlaylistObject':
        if (isset($_POST['PL']))
            $PL = clean($_POST['PL']);
        $row = mysql_fetch_array(mysql_query("
            SELECT * FROM playlists WHERE name = '{$PL}';
        "));
        
        // rating++ on each request
        mysql_query("
            UPDATE playlists SET rating = rating + 1
            WHERE name = '{$PL}';
        ");
        
        if ($row) echo($row['playlist']);
        else echo("ERROR");
        break;
        
    case 'updateRating':
        if (isset($_POST['ID'])) {
            $ID = clean($_POST['ID']);
            mysql_query("
                UPDATE songs SET rating = rating + 1
                WHERE id = {$ID};
            ");
        }
        
        break;
        
    case 'getSongData':
        if (isset($_POST['ID'])) {
            $ID = clean($_POST['ID']);
            $row = mysql_fetch_array(mysql_query("
                SELECT * FROM songs WHERE id = {$ID};
            "));
        }
        
        echo("<strong>" . $row['title'] . "</strong> by " . $row['artist']);
        break;
        
    case 'getResults':
        $ID = clean($_POST['ID']);
        $row = mysql_fetch_array(mysql_query(" SELECT * FROM songs WHERE id={$ID}; "));
        $title = $row['title'];
        
        $result = mysql_query(" SELECT * FROM songs WHERE LOWER(title)=LOWER('{$title}') ORDER BY rating DESC; ");
        $count = 0;
        if ($result) {
            while($row = mysql_fetch_array($result)) {
                if ($count > 4) break;
                if ($row['id'] != $ID) {
                    echo('<div class="song"><button class="btn btn-link btn-mini" onclick="playByID('. $row['id'] .')">'. $row['title'] .' by '. $row['artist'] .'</button></div>');
                    $count++;
                }
            }
        }
        
        if ($count == 0) echo("<div class='alert alert-warning'>We can't find anything else called <strong>". $title ."</strong></div>");
        break;
        
}

?>
