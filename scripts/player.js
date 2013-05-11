function Player(song) {
    this.song = song;
    this.s_ID;
}

Player.prototype = {
    pause: function() {
        this.song.pause();
    },
    
    resume: function() {
        this.song.play();
    },
    
    changeVolume: function(direction) {
        var volume = this.song.volume;
        if (direction == "up" && volume < 1)
            this.song.volume += 0.1;
        else if (direction == "down" && volume >= 0.1)
            this.song.volume -= 0.1;
    },
    
    getID: function(s) {
        var ID;
        $.ajax({
            type: 'POST',
            url: 'scripts/ajax.php',
            async: false,
            data: {action: 'getID', title: s}
        }).done(function(data) {
            ID = data;
        });
        
        return parseInt(ID);
    },
    
    play: function(s_ID) {
        this.s_ID = s_ID;
        var link;
        if (typeof(s_ID) === "number") {
            $.ajax({
                type: 'POST',
                url: 'scripts/ajax.php',
                async: false,
                data: {action: 'getLink', ID: s_ID}
            }).done(function(data) {
                link = data;
            });
        }
        
        this.song.setAttribute('src', link);
        this.song.play();
    }
}
