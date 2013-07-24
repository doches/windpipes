$(document).ready(function() {
  fetchCurrentPiece();
});

var currentPiece = {};
var pipCount = 3;
var emptyPlaylistDelay = 5000;

function fetchCurrentPiece() {
  $.get("api/current",
    {},
    function(json) {
      currentPiece = $.parseJSON(json);
      
      if (currentPiece.error != null) {
        $("#delay-label").html(currentPiece.error);
        setTimeout(function() { fetchCurrentPiece(); }, emptyPlaylistDelay);
        for(var i = 0; i < 6; i++) {
          $("part-"+(i+1)).addClass("taken");
        }
        return;
      } else {
        $("#delay-label").html("Waiting (<span id='delay-seconds'>?</span>s)<br /><span id='delay-part'></span>");
      }
      
      $("#delay-part").html((currentPiece.part+1) + " / " + currentPiece.parts);
      
      startTimeMS = currentPiece.timestamp * 1000;
      now = currentPiece.now * 1000;
      delayUntilStart = startTimeMS - now;
      secondsTilStart = delayUntilStart;
      for(var countdown = secondsTilStart; countdown > 0; countdown-=1000) {
        setTimeout(makeCountdownEvent(Math.floor(secondsTilStart - countdown)/1000), countdown);
      }
      
      // Extract piece information
      noteDuration = currentPiece.note_duration_ms;
      payload = $.parseJSON(currentPiece.payload);
      
      // A payload can now contain multiple parts, instead of a single stream of notes.
      payload = payload[currentPiece.part].part;
      
      var cumulativeDelay = delayUntilStart;
      for(var i = 0; i < payload.length; i++) {
        note = payload[i];
        
        var noteLength = note.duration * noteDuration;
        var pipDelay = noteLength / pipCount;
        
        // Pip change
        for(var j = pipCount; j > 0; j--) {
          cumulativeDelay += pipDelay;
          setTimeout(makePipEvent(j), cumulativeDelay);
          console.log('pip: ' + cumulativeDelay);
        }
        cumulativeDelay += pipDelay;
        console.log('note: ' + cumulativeDelay);
        
        // Note change
        //cumulativeDelay += note.duration * noteDuration;
        setTimeout(makeNoteEvent(note, payload[i+1]), cumulativeDelay);
        if (note.finish != true) {
          setTimeout(makePipEvent(4), cumulativeDelay);
        } else {
          setTimeout(makePipEvent(0), cumulativeDelay);
        }
      } 
  });
}

function makeCountdownEvent(countdown) {
  return function() {
    if (countdown <= 0) {
      $("#delay-box").css("display", "none");
      $("#note-box").css("display", "inline-block");
    } else {
      $("#delay-seconds").html(countdown);
    }
  }
}

// Functional helpers
function makePipEvent(count) {
  return function() {
    html = "";
    for(var i = 0; i < count; i++) {
      html += "<span class='pip'></span>";
    }
    $("#countdown").html(html);
  }
}

function makeNoteEvent(note, nextNote) {
  return function() {
    color = "255, 255, 255";
    if (note.note != null) { 
      // We have a note, so flash the screen in the corresponding colour
      color = rgbForNote(note.note);
      // ...and show the note label
      $("#note-label").html(note.note);
      $("#note-label").css("color", "#fff");
    } else {
      message = note.finish == true ? "finish" : "rest";
      $("#note-label").html(message);
      $("#note-label").css("color", "#000");
    }
    
    if (nextNote != null && nextNote.note != null) {
      $("#next-note").css("display", "block");
      $("#next-note").css("background-color", "rgb("+rgbForNote(nextNote.note)+")");
      $("#next-note").html(nextNote.note);
    } else {
      $("#next-note").css("display", "none");
    }
    $("#note-box").css("background-color", "rgb("+color+")");
    $("#next-note").css("display", "none");
    
    console.log(color);
  }
}