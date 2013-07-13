var player = new Player(document.createElement('audio'));
var counter = new Worker('scripts/counter.js');
var playlist = new Playlist();

var random_is_on = false;
var playlist_visible = false;
var results_visible = false;
var page = 'player';

function resetAdder() {
    $('#addsuccess').hide();
    $('#addfailure').hide();
    $('input').val("");
}

function changePageTo(choice) {
    if (choice != page) {
        $('#' + choice + 'menulink').className = choice;
        $('#' + page).hide();
        $('#' + choice).fadeIn(800);
        page = choice;
    }
}

function share() {
    alert('http://tunejet.net/?song=' + player.s_ID);
}

function playByIndex(s_index) {
    var s_ID = playlist.songs[s_index].ID;
    var description = playlist.getSongData(s_ID);
    playlist.s_index = s_index;
    
    player.play(s_ID);
    counter.postMessage('reset');
    
    $('#nowplaying').html("Currently playing " + description + " <button class='btn btn-mini' style='font-size: 12px;' onclick='toggleResults()'>other results</button><button class='btn btn-mini' style='font-size: 12px;' onclick='share()'>share</button>");
    playlist.is_on = true;
    random_is_on = false;
    
    if (results_visible) {
        results_visible = false;
        $('#results').hide();
    }
}

function playByID(s_ID) {
    if (s_ID != null && !isNaN(s_ID)) {
        var description = playlist.getSongData(s_ID);
        $('#nowplaying').html("Currently playing " + description + " <button id='addbtn' class='btn btn-mini' type='button'><i class='icon-download'></i> Add</button><button class='btn btn-mini' style='font-size: 12px;' onclick='toggleResults()'>other results</button><button class='btn btn-mini' style='font-size: 12px; margin-left:0px;' onclick='share()'>share</button>");
        
        // Add song to playlist
        $('#addbtn').click(function() {
            playlist.add(s_ID);
            $('#nowplaying').append(" <i class='icon-ok'></i>");
            updatePlaylist();
        });
        
        player.play(s_ID);
        counter.postMessage('reset');
        playlist.is_on = false;
        random_is_on = false;
        
        changePageTo('player');
        
        if (results_visible) {
            results_visible = false;
            $('#results').hide();
        }
    }
}

function playNext() {
    if (playlist.s_index == playlist.songs.length - 1) {
        playByIndex(0);
    } else {
        playByIndex(playlist.s_index += 1);
    }
}

function playRandom() {
    playByID(Math.round(Math.random()*COUNT));
    random_is_on = true;
}

function updatePlaylist() {
    $('#playlistbox').html("");
    for (var i = 0; i < playlist.songs.length; i++) {
        $('#playlistbox').append('<div class="song"><button class="btn btn-link btn-mini" onclick="playlist.moveSongUp('+i+'); updatePlaylist()"><i class="icon-arrow-up icon-white"></i></button><button class="btn btn-link btn-small" onclick="playByIndex('+i+')">'+playlist.songs[i].description+'</button><button type="button" class="close" onclick="playlist.remove('+i+'); updatePlaylist()"><i class="icon-trash"></i></button></div>');
    }
}

function toggleResults() {
    if (results_visible) {
        $('#results').slideUp(500);
    } else {
        $.post('scripts/ajax.php', {action: 'getResults', ID: player.s_ID}, function(data) {
            $('#results').hide().html('<div>'+data+'</div>');
            $('#results').slideDown(500);
        });
    }
    
    results_visible = !results_visible;
}

$('document').ready(function() {

    var initializing = true;
    resetAdder();
    
    // Search for and play song
    $('#go').click(function(e) {
        e.preventDefault();
        if ($('#songbox').val() != "") {
            var s_ID = player.getID($('#songbox').val());
            playByID(s_ID);
        }
        
        $('#songbox').val("");
    });
    
    $('#toggleplaylist').click(function() {
        if (playlist_visible) {
            $('#playlistdiv').slideUp(500);
        } else {
            if (initializing) {
                $('#playlistdiv').hide().html('<div class="general"><ul id="playlistbox" class="nav"></ul><center><p><button id="publish" class="btn btn-mini btn-success">Publish</button> <button id="clearbtn" class="btn btn-mini btn-danger">Clear</button></p></center></div>');

                $('#publish').click(function() {
                    var pln = prompt("Give your playlist a name");
                    var obj = JSON.stringify(playlist.songs);
                    if (pln) {
                        $.post('scripts/ajax.php', {action: 'addPlaylist', OBJ: obj, PLN: pln}, function(data) {
                            if (data == 'ADDED') {
                                alert("Your playlist can be accessed at http://tunejet.net/?playlist=" + pln);
                            } else {
                                alert("We couldn't publish your playlist");
                            }
                        });
                    }
                });

                $('#clearbtn').click(function() {
                    if (confirm('Are you sure?')) {
                        playlist.clear();
                        updatePlaylist();
                    }
                });
                
                initializing = false;
            }
            
            updatePlaylist();
            $('#playlistdiv').slideDown(500);
        }
        
        playlist_visible = (playlist_visible) ? false : true;
    });
    
    $(player.song).bind('loadedmetadata', function() {
        counter.postMessage(player.song.duration);
        
        $.post('scripts/ajax.php', {action: 'updateRating', ID: player.s_ID});
    });
    
    counter.onmessage = function(event) {
        $('#progress').css('width', Math.round(event.data) + '%');
    }
    
    $('#playbtn').click(function() {
        counter.postMessage('resume');
        player.resume();
    });
    
    $('#pausebtn').click(function() {
        counter.postMessage('stop');
        player.pause();
    });
    
    // Add a song to central database
    $('#addsongbtn').click(function(e) {
        e.preventDefault();
        $.post('scripts/ajax.php', {
            action: 'addSong',
            title: $('#songname').val(),
            artist: $('#artist').val(),
            link: $('#songlink').val()
        }, function(data) {
            if (data == 'ADDED') {
                $('#addfailure').hide();
                $('#addsuccess').fadeIn(500);
            } else if (data == 'OVERLOAD') {
                alert("Whoa, there. We appreciate your prolific contribution, but to prevent spam, we have to ask that you restart your browser.");
            } else {
                $('#addsuccess').hide();
                $('#addfailure').fadeIn(500);          
            }
        });
    });
    
    $('#playermenulink').click(function(e) {
        e.preventDefault();
        changePageTo('player');
        $('#playermenulink').className = "";
    });
    
    $('#aboutmenulink').click(function(e) {
        e.preventDefault();
        changePageTo('about');
        $('#playermenulink').className = "";
    });
    
    $('#recentmenulink').click(function(e) {
        e.preventDefault();
        changePageTo('recent');
        $('#playermenulink').className = "";
    });
    
    $(player.song).bind('error', function() {
        if (playlist.is_on)
            playNext();
        if (random_is_on)
            playRandom();
    });
    
    $(player.song).bind('ended', function() {
        if (playlist.is_on)
            playNext();
        if (random_is_on)
            playRandom();
    });
    
    if (INIT_PL) {
        $.post('scripts/ajax.php', {action: 'getPlaylistObject', PL: INIT_PL}, function(data) {
            if (data == "ERROR") {
                alert("We can't find a playlist called " + INIT_PL);
            } else {
                playlist.songs = JSON.parse(data);
                updatePlaylist();
                playByIndex(0);
            }
        });
    }

    // Play GET-requested song
    playByID(INIT_ID);
    
});