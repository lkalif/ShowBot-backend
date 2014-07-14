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
    </div>
    <button id="refresh_button">Refresh Now</button>
    <div id="msg_area" style="display: none; padding: 0.2em 0.5em; margin-top: 0.4em; float: right;" class="ui-state-error"></div>
</div>	


<div id="main_content" class="ui-widget-content" style="padding: 1em">
    <p>Suggestions on <?php echo htmlentities($serverID) ?> channel <?php echo htmlentities($channel) ?></p>
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
</div>

<div class="ui-widget-header ui-corner-bottom" style="text-align: right; padding: 5px;">
    <a class="toolbarbutton" href="http://irc.t2t2.eu/">chatrealm</a>
    <a class="toolbarbutton" href="http://diamondclub.tv/">diamondclub.tv</a>
</div>

<script>

var inRefresh = false;
var ServerID = "<?php echo htmlentities($serverID); ?>";
var Channel = "<?php echo htmlentities($channel); ?>";
var refreshTimer = 0;
var refreshInterval = 5000;
var autoRefresh = true;

function showAlert(msg) {
    $("#msg_area")
        .text(msg)
        .fadeIn(100)
        .delay(2000)
        .fadeOut(50);
}

function sendVote(event) {
    alert(event.data.ID);
}

function setRow(row, suggestion) {
    row.empty();
    
    row.attr("data-suggestionID", suggestion.ID);

    var cell = $('<td/>');
    cell.text(suggestion.Votes);
    row.append(cell);
    
    cell = $('<td/>');
    cell.text(suggestion.Title);
    row.append(cell);

    cell = $('<td/>');
    cell.text(suggestion.User);
    row.append(cell);
    row.on("click", suggestion, sendVote);
}

function fullTableInsert(data) {
    $('#main_data').empty();
    for (var i = 0; i < data.length; i++) {
        var row = $('<tr id="row_suggestion_' + data[i].ID + ' "/>');
        setRow(row, data[i]);
        $('#main_data').append(row);
    }
}

function updateRows(data) {
    var i = 0;
    $('#main_data >tr').each(function() {
        var row = $(this)
        if (row.attr("data-suggestionID") != data[i].ID)
        {
            setRow(row, data[i])
        }
        i++;
    });
    
    for (; i < data.length; i++) {
        var row = $('<tr id="row_suggestion_' + data[i].ID + ' "/>');
        setRow(row, data[i]);
        $('#main_data').append(row);
    }
}

function processResult(data) {
    var nRows = $('#main_data >tr').length;
    if (nRows > data.length)
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
        failure: function() {
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


$(document).ready(function() {
    $( ".toolbarbutton" ).button();

    $("#radioset").buttonset();
    
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

 