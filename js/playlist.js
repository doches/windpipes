$(document).ready(function() {
  // Set up datatables to work nicely with Bootstrap
  $.extend( $.fn.dataTableExt.oStdClasses, {
    "sSortAsc": "header headerSortDown",
    "sSortDesc": "header headerSortUp",
    "sSortable": "header"
  });

  refreshLibrary();
  refreshPlaylist();
});

function refreshLibrary() {
  $.get("api/library.php",
    {},
    function(json) {
      var list = $.parseJSON(json);
      var html = "<thead><tr>";
      for(var i in list[0]) {
        html += "<th>"+i+"</th>";
      }
      html += "<th></th>";
      html += "</tr></thead><tbody>";
      for(var i in list) {
        var piece = list[i];
        
        html += "<tr>";
        for (var j in piece) {
          html += "<td>" + piece[j] + "</td>";
        }
        html += "<td><a href='#' onclick='enqueue("+piece.ID+"); return false;' class='btn'>Enqueue</a></td>";
        html += "</tr>";
      }
      html += "</tbody>";
      
      $("#library-table").html(html);
      $("#library-table").dataTable({
        "sDom": "<'row'<'span3'l><'span3'f>r>t<'row'<'span3'i><'span3'p>>",
        "sPaginationType": "bootstrap"
      });
      $("#library-table").removeClass("table");
      $("#library-table").addClass("table");
  });
}

function enqueue(id) {
  time = new Date();
  time = time.getTime() / 1000 + 30;
  $.post("api/enqueue.php",
    {
      id: id,
      start: time
    },
    function(json) {
      if (json.status == "ERROR") {
        console.log(json);
        // TODO show error.
      } else {
        refreshPlaylist();
      }
  });
}

function refreshPlaylist() {
  $.get("api/playlist.php",
    {},
    function(json) {
      var list = $.parseJSON(json);
      var html = "<thead><tr>";
      for(var i in list[0]) {
        if (i != "playlist_id")
          html += "<th>"+i+"</th>";
      }
      html += "<th></th>";
      html += "</tr></thead><tbody>";
      for(var i in list) {
        var piece = list[i];
        
        html += "<tr>";
        for (var j in piece) {
          if (j != "playlist_id")
            html += "<td>" + piece[j] + "</td>";
        }
        html += "<td><a href='#' onclick='dequeue("+piece.playlist_id+"); return false;' class='btn'>Remove</a></td>";
        html += "</tr>";
      }
      html += "</tbody>";
      $("#playlist-table").html(html);
  });
}

function dequeue(id) {
  $.get("api/dequeue.php",
    {
      id:id
    },
    function(json) {
      refreshPlaylist();
  });
}