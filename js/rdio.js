var apiswf = null;
var bPlayingSong = false;
$(document).ready(function() {
  var flashvars = {
    'playbackToken': "GA5SWJUr_____2R2cHlzNHd5ZXg3Z2M0OXdoaDY3aHdrbjE5Mi4yNDEuMTY5LjMzF2NWIdPIZfVbglYuUQ56sQ==", // from token.js
    'domain': "192.241.169.33",          
    'listener': 'callback_object'   
    };
  var params = {
    'allowScriptAccess': 'always'
  };
  var attributes = {};
  swfobject.embedSWF('http://www.rdio.com/api/swf/',
      'apiswf',
      1, 1, '9.0.0', 'expressInstall.swf', flashvars, params, attributes);

  //$('#footer').hide();

});


var callback_object = {};

callback_object.ready = function ready(user) {
  apiswf = $('#apiswf').get(0);
  apiswf.rdio_play("t23010456");
  $('#track_play').click(function() { 
    if(bPlayingSong) {
      apiswf.rdio_pause(); bPlayingSong = false;
      $('#track_play_icon').attr("class", "fa-icon-play");
    } else {
      apiswf.rdio_play(); bPlayingSong = true;
      $('#track_play_icon').attr("class", "fa-icon-pause");
    }
  });
  $('#prev_track').click(function() {
    apiswf.rdio_previous(); 
  });
  $('#next_track').click(function() { 
    apiswf.rdio_next(); 
  });
}

callback_object.playingTrackChanged = function playingTrackChanged(playingTrack, sourcePosition) {
  if (playingTrack != null) {
    $('#track').text(playingTrack['name']);
    $('#artist').text(playingTrack['artist']);
    $('#art').attr('src', playingTrack['icon']);
  }
}

callback_object.playStateChanged = function playStateChanged(playState) {
  if(!bPlayingSong) {
    apiswf.rdio_pause();
  }
}