<?php

session_start();
require 'database.php';

function clean($db, $string) {
    return $db->real_escape_string(strip_tags($string, "<strong>"));
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

function get_ID($db, $title) {
    // Return ID of song of given title with highest rating
    if ($title != "") {
        $row = $db->query("
            SELECT * FROM songs WHERE LOWER(title) = LOWER('{$title}')
            AND rating = (SELECT MAX(rating) FROM songs WHERE LOWER(title) = LOWER('{$title}'));
        ")->fetch_array();
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

        $title = clean($db, $_POST['title']);
        $artist = clean($db, $_POST['artist']);
        $link = clean($db, $_POST['link']);
        
        if (link_uses_http($link) && $title != "" && $artist != "" && $link != "") {
            $result = $db->query("
                INSERT INTO songs (title, artist, link, rating)
                VALUES ('{$title}', '{$artist}', '{$link}', 0);
            ");
            
            if ($result) {
                $_SESSION['adds']++;
                echo("ADDED");   
            }
        }
        
        break;
        
    case 'getID':
        $title = "";
        
        if (isset($_POST['title']))
            $title = clean($db, $_POST['title']);
            
        echo(get_ID($title));
        break;
        
    case 'getLink': // ...by ID to play song
        if (isset($_POST['ID'])) {
            $ID = clean($db, $_POST['ID']);
            $row = $db->query("
                SELECT * FROM songs WHERE id = {$ID};
            ")->fetch_array();
        }

        echo($row['link']);
        break;
        
    case 'addPlaylist':
        if (!isset($_SESSION['adds']))
            break;
        if ($_SESSION['adds'] > 5)
            break;
        if (isset($_POST['OBJ']) && isset($_POST['PLN'])) {
            $pl_obj = clean($db, $_POST['OBJ']);
            $pl_name = clean($db, $_POST['PLN']);
            
            if ($pl_name != "" && $pl_obj != "") {
                $result = $db->query("
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
            $PL = clean($db, $_POST['PL']);

        $row = $db->query("
            SELECT * FROM playlists WHERE name = '{$PL}';
        ")->fetch_array();
        
        // rating++ on each request
        $db->query("
            UPDATE playlists SET rating = rating + 1
            WHERE name = '{$PL}';
        ");
        
        if ($row) echo($row['playlist']);
        else echo("ERROR");
        break;
        
    case 'updateRating':
        if (isset($_POST['ID'])) {
            $ID = clean($db, $_POST['ID']);
            $db->query("
                UPDATE songs SET rating = rating + 1
                WHERE id = {$ID};
            ");
        }
        
        break;
        
    case 'getSongData':
        if (isset($_POST['ID'])) {
            $ID = clean($db, $_POST['ID']);
            $row = $db->query("
                SELECT * FROM songs WHERE id = {$ID};
            ")->fetch_array();
        }
        
        echo("<strong>" . $row['title'] . "</strong> by " . $row['artist']);
        break;
        
    case 'getResults':
        $ID = clean($db, $_POST['ID']);
        $row = $db->query(" SELECT * FROM songs WHERE id={$ID}; ")->fetch_array();
        $title = $row['title'];
        
        $result = $db->query(" SELECT * FROM songs WHERE LOWER(title)=LOWER('{$title}') ORDER BY rating DESC; ");
        $count = 0;
        if ($result) {
            while($row = $result->fetch_array()) {
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
