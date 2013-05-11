var time = 0;
var duration = 200;
var timer;

function count() {
    if (time == duration) {
        clearInterval(timer);
    } else {
        time++;
        postMessage((time/duration)*100);
    }
}

onmessage = function(event) {
    var data = event.data;
    if (isNaN(data)) {
        if (data == 'stop') {
            clearInterval(timer);
        } else if (data == 'resume') {
            timer = setInterval(count, 1000);
        } else if (data == 'reset') {
            clearInterval(timer);
            time = 0;
        }
    } else {
        duration = data;
        timer = setInterval(count, 1000);
    }
}
