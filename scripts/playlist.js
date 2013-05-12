function Playlist() {
    this.songs = [];
    this.is_on = false;
    this.s_index = 0;
    
    var p_list = localStorage['playlist'];
    if (p_list) this.songs = JSON.parse(p_list);
    
    this.Song = function(description, ID) {
        this.description = description;
        this.ID = ID;
    }
}

Playlist.prototype = {
    changeSong: function(direction) {
        if (direction == 'previous')
            return this.s_index - 1;
        else
            return this.s_index + 1;
    },
    
    saveData: function() {
        localStorage['playlist'] = JSON.stringify(this.songs);
    },
    
    getSongData: function(s_ID) {
        var description;
        
        $.ajax({
            type: 'POST',
            url: 'scripts/ajax.php',
            async: false,
            data: {action: 'getSongData', ID: s_ID}
        }).done(function(data) {
            description = data;
        });
        
        return description;
    },
    
    add: function(s_ID) {
        var description = this.getSongData(s_ID);
        this.songs.push(new this.Song(description, s_ID));
        this.saveData();
    },
    
    remove: function(s_index) {
        this.songs.splice(s_index, 1);
        this.saveData();
    },
    
    clear: function() {
        localStorage['playlist'] = JSON.stringify([]);
        this.songs = JSON.parse(localStorage['playlist']);
    },
    
    moveSongUp: function(song) {
        if (song > 0) {
            // Switch song index
            var s1 = this.songs[song - 1];
            var s2 = this.songs[song];
            this.songs[song - 1] = s2;
            this.songs[song] = s1;
            this.saveData();
        }
    }
    
}
