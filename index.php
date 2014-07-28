<?php
/**
 * Front page
 * 
 * @package ShowBot-backend
 * @author Latif Khalifa <latifer@streamgrid.net>
 * @copyright Copyright(c) 2014, Latif Khalifa
 * @license http://opensource.org/licenses/MIT
 */
define("SITE_ROOT", dirname(__file__));
require_once SITE_ROOT . "/lib/init.php";

// If not specified set server id to diamondclub and channel to #chat
$channel = isset($_REQUEST['channel']) ? (string)$_REQUEST['channel'] : "#chat";
$serverID = isset($_REQUEST['server_id']) ? (string)$_REQUEST['server_id']: "diamondclub";

if (strlen($channel) && $channel[0] != "#")
{
    $channel = "#" . $channel;
}

if (!in_array($serverID, $ALLOWED_SERVERS)
    || !in_array($channel, $ALLOWED_CHANNELS[$serverID]))
{
    Layout::header();
    echo "Unsupported server/channel";    
    Layout::footer();
    die();
}

Layout::header();
?>

<div class="ui-widget-header ui-corner-top" style="padding: 5px; vertical-align: middle;">
    <div id="radioset" style="display: inline-block">
        <input type="radio" id="refresh_on"  value="on"  name="autorefresh" checked="checked"><label for="refresh_on">Autorefresh: ON</label>
        <input type="radio" id="refresh_off" value="off" name="autorefresh"><label for="refresh_off">Autorefresh: OFF</label>
        <input type="checkbox" id="all_caps"><label for="all_caps">CAPS</label>
    </div>
    <button id="refresh_button">Refresh Now</button>
    <div id="msg_area" style="display: none; padding: 0.2em 0.5em; margin-top: 0.4em; float: right;" class="ui-state-error"></div>
</div>	


<div id="main_content" class="ui-widget-content" style="padding: 1em">
    <p>Suggestions on <?php echo htmlentities($serverID) ?> channel <?php echo htmlentities($channel) ?>.</p>
    <table id="votes_table" class="jtable">
        <thead>
            <tr>
                <th>Votes</th>
                <th>Suggestion</th>
                <th>By</th>
            </tr>
        </thead>
        <tbody id="main_data">
        </tbody>
    </table>
    
    <p style="font-size: 12px">To add a suggestion say: <i>!s My suggestion here</i>
    </p>

</div>

<div class="ui-widget-header ui-corner-bottom" style="text-align: right; padding: 5px;">
    <a id="about_btn" class="toolbarbutton" href="javascript:void(0)">about</a>
    <a class="toolbarbutton" href="http://irc.t2t2.eu/" target="_blank">chatrealm</a>
    <a class="toolbarbutton" href="http://diamondclub.tv/" target="_blank">diamondclub.tv</a>
</div>

<div id="about_dialog" style="display: none" title="About ShowBot">
    <p><b>ShowBot 1.0</b><br/>
    IRC channel suggestion catching and voting system<br/>
    <small>Copyright &copy; 2014 Latif Khalifa (<a href="http://twitter.com/lkalif" target="_blank">lkalif</a>)</small></p>
    
    <p>Source code available on Github for
    the <a href="https://github.com/lkalif/ShowBot-backend" target="_blank">backend</a>
    and the <a href="https://github.com/lkalif/ShowBot" target="_blank">IRC bot itself</a>.
    ShowBot is released under the terms of the
    <a href="http://opensource.org/licenses/MIT" target="_blank">MIT License</a>
    </p>
    
    <p>Logo by Sebastian (<a href="http://twitter.com/sebgonz" target="_blank">sebgonz</a>).</p>
</div>

<script>

var inRefresh = false;
var ServerID = "<?php echo htmlentities($serverID); ?>";
var Channel = "<?php echo htmlentities($channel); ?>";
var refreshTimer = 0;
var refreshInterval = 5000;
var autoRefresh = true;
var allCaps = false;
var dragging = false;

function isiOS(){
    return (
        (navigator.platform.indexOf("iPhone") != -1) ||
        (navigator.platform.indexOf("iPad") != -1) ||
        (navigator.platform.indexOf("iPod") != -1)
    );
}

function showAlert(msg) {
    $("#msg_area")
        .stop()
        .text(msg)
        .fadeIn(100)
        .delay(3000)
        .fadeOut(50);
}

$.fn.animateHighlight = function(highlightColor, duration) {
    var highlightBg = highlightColor || "#0ff";
    var durationMs = duration || 250;
    var elem = $(this);
    var originalBg = elem.css("background-color");
    elem.css("background-image", "none");
    if (!elem.inAnimateHighlight) {
        elem.inAnimateHighlight = true;
        elem
            .stop()
            .css("background-color", highlightBg)
            .animate({backgroundColor: originalBg}, durationMs, "swing", function() {
                elem.inAnimateHighlight = false;
                elem.removeAttr("style");
            });
    }
};

function setRow(row, suggestion, animateHighlight) {
    var oldID = row.attr("data-suggestionid");
    var oldVotes = $("td:first", row).text();
    var currIndex = row.index();
    var oldIndex = $("#main_data >tr[data-suggestionid=" + suggestion.ID + "]").index();
    
    if (oldID == suggestion.ID) {
        var firstCell = $("td:first", row);
        if (oldVotes != suggestion.Votes) {
            firstCell.text(suggestion.Votes);
            if (animateHighlight) {
                firstCell.animateHighlight();
            }
        }
        return;
    }
    
    row.empty();
    row.attr("data-suggestionid", suggestion.ID);

    var votesCell = $('<td/>');
    votesCell.text(suggestion.Votes);
    row.append(votesCell);
    
    var cell = $('<td class="title"/>');
    cell.text(suggestion.Title);
    if (allCaps) {
        cell.addClass("allcaps");
    }
    row.append(cell);
    

    cell = $('<td/>');
    cell.text(suggestion.User);
    row.append(cell);
    
    if (animateHighlight && oldIndex != -1 && currIndex < oldIndex) {
        row.animateHighlight();
    }
}

function fullTableInsert(data) {
    $('#main_data').empty();
    for (var i = 0; i < data.length; i++) {
        var row = $('<tr/>');
        setRow(row, data[i]);
        $('#main_data').append(row);
    }
}

function updateRows(data) {
    var i = 0;
    $('#main_data >tr').each(function() {
        var row = $(this)
        setRow(row, data[i], true)
        i++;
    });
    
    for (; i < data.length; i++) {
        var row = $('<tr id="row_suggestion_' + data[i].ID + ' "/>');
        setRow(row, data[i]);
        $('#main_data').append(row);
        row.animateHighlight();
    }
}

function processResult(data) {
    var nRows = $('#main_data >tr').length;
    if (nRows == 0 || nRows > data.length)
    {
        fullTableInsert(data);
    }
    else
    {
        updateRows(data);
    }
    inRefresh = false;
}

function beginRefresh() {
    if (inRefresh) return;
    inRefresh = true;
    $.ajax({
        url: "api.php",
        type: "POST",
        success: processResult,
        error: function() {
            inRefresh = false;
            showAlert("Failed to get list");
        },
        cache: false,
        data: JSON.stringify({
            ServerID: ServerID,
            Channel: Channel,
            Function: "web_top"
        }),
        contentType: "application/json; charset=utf-8",
        dataType: "json",
    });
}

function processVoteResult(data) {
    showAlert(data.Message);
    beginRefresh();
}

function sendVote(ID) {
    $.ajax({
        url: "api.php",
        type: "POST",
        success: processVoteResult,
        error: function() {
            showAlert("Failed to vote");
        },
        cache: false,
        data: JSON.stringify({
            ServerID: ServerID,
            Channel: Channel,
            Function: "vote_add",
            SuggestionID: ID
        }),
        contentType: "application/json; charset=utf-8",
        dataType: "json",
    });
}

if (isiOS()) {

    $(document).on("touchstart", "#main_data >tr", function() {
        dragging = false;
    });
    
    $(document).on("touchmove", "#main_data >tr", function() {
        dragging = true;
    });
    
    $(document).on("touchend", "#main_data >tr", function() {
        if (!dragging) {
            sendVote(this.dataset.suggestionid);
        }
    });
}

$(document).on("click", "#main_data >tr", function() {
    var sel = getSelection().toString();
    if (!sel)
    {
        sendVote(this.dataset.suggestionid);
    }
});

$(document).ready(function() {
    $(".toolbarbutton").button();

    $("#radioset").buttonset();
    
    $("#about_dialog").dialog({
        autoOpen: false,
        buttons: [ { text: "Ok", click: function() { $( this ).dialog( "close" )}}],
        });

    $("#about_btn").on("click", function() {
        $("#about_dialog").dialog("open" );
    });
    
    $("#all_caps").on("change", function() {
        if (this.checked) {
            allCaps = true;
            $(".title").addClass("allcaps");
        } else {
            allCaps = false;
            $(".title").removeClass("allcaps");
        }
    });
    
    $( 'input[name="autorefresh"]:radio' )
        .on("change", function() {
            if (this.value == "on") {
                autoRefresh = true;
                refreshTimer = setInterval(beginRefresh, refreshInterval);
            } else {
                autoRefresh = false;
                clearInterval(refreshTimer);
            }
        });
    
    $("#refresh_button")
        .button()
        .on("click", function(event) {
            beginRefresh();
            event.preventDefault();
        });
        
    beginRefresh();
    
    if (autoRefresh) {
        refreshTimer = setInterval(beginRefresh, refreshInterval);
    }

});
</script>


<?php
Layout::footer();

 