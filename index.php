<!DOCTYPE html>
<!-- Copyright (C) 2013 Alex Reidy -->
<?php

session_start();
require 'scripts/database.php';

if (!isset($_SESSION['adds']))
    $_SESSION['adds'] = 0;

$result = mysql_query("SELECT MAX(id) FROM songs;");
$row = mysql_fetch_array($result);
$n = $row[0];

// Play GET-requested song:
if (isset($_GET['song'])) {
    $song = $_GET['song'];
} else {
    $song = "";
}

if (isset($_GET['playlist'])) {
    $playlist = $_GET['playlist'];
} else {
    $playlist = "";
}

?>

<html>
    <head>
        <title>tunejet.net</title>
        <meta name="description" content="Listen to awesome music for free - simply search and play.">
        <meta name="keywords" content="tunejet, Music, tunes, Online, Playlist, MP3s, free, Play non-iTunes music on iPhone, iTunes workaround">
        <link rel="icon" type="image/png" href="style/favicon.png">
        <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
        <link rel="stylesheet" href="style/style.css">
        
        <script type="text/javascript">

          var _gaq = _gaq || [];
          _gaq.push(['_setAccount', 'UA-38550261-1']);
          _gaq.push(['_trackPageview']);

          (function() {
            var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
            ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
            var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
          })();
          
        </script>
        
    </head>
    <body>
        
        <div class="container">
            <div class="masthead" style="text-align:center;">
                <h1 class="title">tunejet.net</h1>
            </div>

            <div class="navbar">
                <div class="navbar-inner">
                    <div class="container">
                        
                        <ul class="nav">
                            <li id="playermenulink"><a href="#">Player</a></li>
                            <li><a id="aboutmenulink" href="#">About</a></li>
                            <li><a id="recentmenulink" href="#">Songs</a></li>
                            <li><a id="recentmenulink" href="http://twitter.com/tunejetdotnet" target="new">@</a></li>
                        </ul>
                    </div>
                </div>
            </div> 
        </div>
        
        <!-- PLAYER -->
        <div id="player">
            <div class="container">
                <div class="general" style="text-align:center;">
                
                    <p id="nowplaying">Welcome to tunejet.net</p>
                    
                    <div id="results"><!-- Other results --></div>
                    
                    <div class="progress progress-striped active">
                        <div id="progress" class="bar"></div>
                    </div>

                    <form class="form-search">
                        <div class="input-append">
                            <input id="songbox" type="text" class="span5 search-query" placeholder="Search away; if we've got the song, it will play">
                            <button id="go" type="submit" class="btn btn-primary"><i class="icon-search icon-white"></i> for <i class="icon-music icon-white"></i></button>
                        </div>
                    </form>
                    
                    <!-- Controls -->
                    <p>
                    <button class="btn btn-small" onclick="playByIndex(playlist.changeSong('previous'))"><i class="icon-backward"></i></button>
                    <button id="playbtn" class="btn btn-small btn-success"><i class="icon-play"></i></button>
                    <button id="pausebtn" class="btn btn-small btn-danger"><i class="icon-pause"></i></button>
                    <button class="btn btn-small" onclick="playByIndex(playlist.changeSong('next'))"><i class="icon-forward"></i></button> &mdash;
                    <button class="btn btn-mini" onclick="player.changeVolume('down')"><i class="icon-volume-down "></i></button>
                    <button class="btn btn-mini" onclick="player.changeVolume('up')"><i class="icon-volume-up"></i></button>
                    <a role="button" href="#addsongs" data-toggle="modal" onclick="resetAdder()">Can't find a song?</a>
                    </p>
                    
                    <button id="toggleplaylist" class="btn btn-primary btn-small"><i class="icon-list icon-white"></i> Playlist</button></center>
                    <button class="btn btn-small btn-warning" onclick="playRandom()"><i class="icon-random"></i></button>
                </div>

                <div id="playlistdiv">
                    <!-- Playlist -->
                </div>
                
            </div>
        </div>
        
        <!-- ABOUT -->
        <div id="about">
            <div class="container">
                <div class="general">
                    <h3>About tunejet.net</h3>
                    <p>We think you should be free to listen to your favorite songs without giving a second thought to excessive subscription fees, advertisements, or&mdash;in the absence of those&mdash;tasteless vendor-lock (iTunes). Our goal is to make your music accessible on virtually every modern platform (yep, even iOS), and ultimately to provide the go-to music streaming service that plays your favorite songs on demand.</p>
                    <p>We created tunejet to simplify your relationship with music. Since most people don't use iTunes exclusively, and since they often go to the trouble of syncing non-iTunes music to their iPhones anyway, we thought we should replace this obnoxious process. Making a playlist with non-iTunes music (God forbid) shouldn't be an ordeal. And with tunejet, it's really not. When you want to listen to music that you didn't purchase from iTunes, or when Pandora gets a bit boring, tunejet gives you a few choices: You can search for specific songs, first of all, to see if anyone else has uploaded them, and if they already exist, you can simply add them to your playlist. If you're a real hipster and nobody has linked tunejet to your favorite songs yet, you can throw your MP3s into a public Dropbox folder or your website and tell tunejet where they are so you can easily play them thereafter simply by searching. And then there's Google, who will gladly show you where to find any MP3 if you ask nicely.</p>
                    <p>All of our songs are totally decentralized: Our database is full of nothing but <em>links</em> to MP3 files. While we are still required by the DMCA to remove links to infringing material (like all search engines), we don't scour our database to find links to infringing media unless asked; we leave the moral, civil, and legal decisions to you. When it comes to music, we consider tunejet to be a tool for freedom.</p>
                </div>

                <div class="general">
                    <h3>Frequently-asked questions</h3>
                    
                    <h4>Can I use tunejet on my iPhone? How?</h4>
                    <p style="margin-left:30px;">Yes! After building the site, we were delighted to find out that the media player works perfectly on the iPhone. It's a wonderful alternative to iTunes. We support both Safari and Chrome for iOS.</p>
                    
                    <h4>How is tunejet different from Pandora or Spotify?</h4>
                    <p style="margin-left:30px;">Our service could be described as a streamlined version of Spotify. And it's totally free&mdash;even on the iPhone.</p>
                    
                    <h4>Seems too good to be true. Is tunejet actually legal?</h4>
                    <p style="margin-left:30px;">Yes. Our service is as legal as Google's search engine or your web browser. Our hardware and server software never interact with any illegally-obtained media. Much as Google is free to show you where to find information on creating a nuclear bomb or where to find pirated movies, tunejet is free to tell your web browser where to find MP3s&mdash;copyright-protected or otherwise. That said, it's illegal for you to download (or play) copyright-protected music in most jurisdictions. Just because you <em>can</em> doesn't mean you <em>should</em>. And just like other search engines, we are required by the DMCA to remove links to infringing material when notified.</p>
                    
                    <h4>Where do the songs come from?</h4>
                    <p style="margin-left:30px;">We maintain a database of user-contributed links that point to MP3 files. If you search for a song and it doesn't play, you can add it yourself. The actual files are totally decentralized: Every song we play is sitting on someone else's server out on the Web. If you want your MP3s to be available virtually everywhere, throw them up on the Web (using a public Dropbox folder, for example), and add the links to our database.</p>
                    
                    <h4>How do I share a tunejet song or playlist?</h4>
                    <p style="margin-left:30px;">Playlists can be accessed according to this URL convention: http://tunejet.net/?playlist=my playlist name</p>
                    <p style="margin-left:30px;">Songs can't be accessed by name in the URL; just click "share," then give the link to others.</p>
                </div>
                
                <div class="general">
                    <h3>Contact</h3>
                    <p>If you have questions, ideas, comments, or concerns, let us know.</p>
                    <p><a href="mailto:info@tunejet.net">info@tunejet.net</a></p>
                    <p><a href="https://twitter.com/tunejetdotnet">twitter</a></p>
                </div>
            </div>
        </div>
        
        <!-- RECENT (SONGS) -->
        <div id="recent">
            <div class="container">  
                <div class="general">
                    <h3>Songs recently added to the tunejet database</h3>
                    <?php
                    
                    // $size = mysql_fetch_array(mysql_query("SELECT MAX(id) FROM songs;"))[0];
                    $result = mysql_query("SELECT MAX(id) FROM songs;");
                    $row = mysql_fetch_array($result);
                    $size = $row[0];
                    
                    for ($i = $size; $i > $size - 25; $i--) {
                        $row = mysql_fetch_array(mysql_query("SELECT * FROM songs WHERE id = '{$i}';"));
                        if ($row) { // <a href="/?song='. $row['id'] .'"><strong>' . $row['title'] . '</strong> by ' . $row['artist'] . '</a>
                            echo('<div class="song"><button class="btn btn-link" onclick="playByID('. $row['id'] .')"><strong>'. $row['title'] .'</strong> by '. $row['artist'] .'</button></div>');
                        }
                    }
                    
                    ?>
                </div>
            </div>
        </div>
            
        <!-- Song adder dialog -->
        <div id="addsongs" class="modal hide fade" tabindex="-1" aria-labelledby="addsongtitle" role="dialog" aria-hidden="true">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true" onclick="resetAdder()">&times;</button>
                <h3 id="addsongtitle">Add songs to the tunejet database</h3>
            </div>
            <div class="modal-body">
                <div id="addsuccess" class="alert alert-success">You rock. Thanks for making music more accessible to everyone.</div>
                <div id="addfailure" class="alert alert-error">You totally screwed up.</div>
                <p class="muted">
                We rely on <em>you</em> to populate the tunejet database.
                </p>
                <p class="muted">
                    We don't actually store MP3s on our server; we
                    maintain a database of links that <em>point</em> to MP3 files.
                </p>
                <p class="muted">We're looking for links like this: http://sketchywebsite.com/great-song.mp3</p>
                <input id="songname" type="text" class="span4" placeholder="Song name"></input>
                <input id="artist" type="text" class="span4" placeholder="Artist"></input>
                <input id="songlink" type="text" class="span4" placeholder="Link to MP3 file"></input>
            </div>
            <div class="modal-footer">
                <a href="#" class="btn" data-dismiss="modal" aria-hidden="true" onclick="resetAdder()">Close</a>
                <a id="addsongbtn" href="#" class="btn btn-primary">Add</a>
            </div>
        </div>
    
    </body>
    
    <!-- JS -->
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
    <script type="text/javascript" src="bootstrap/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="scripts/playlist.js"></script>
    <script type="text/javascript" src="scripts/player.js"></script>
    <script type="text/javascript" src="scripts/main.js"></script>
    <script type="text/javascript">
        (function(ID) { INIT_ID = ID || null; })(<?php echo($song); ?>);
        (function(PL) { INIT_PL = PL || null; })('<?php echo($playlist); ?>');
        (function(n) { COUNT = n || null; })();
    </script>
    
</html>
