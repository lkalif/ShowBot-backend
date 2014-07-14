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
        <input type="radio" id="refresh_on" name="autorefresh" checked="checked"><label for="refresh_on">Autorefresh: ON</label>
        <input type="radio" id="refresh_off" name="autorefresh"><label for="refresh_off">Autorefresh: OFF</label>
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
            <tr class="rowhighlight">
                <td>12</td>
                <td>This is title suggestion 1</td>
                <td>lkalif</td>
            </tr>
            <tr class="rowhighlight">
                <td>4</td>
                <td>This is title suggestion 2</td>
                <td>lkalif</td>
            </tr>
            <tr class="rowhighlight">
                <td>0</td>
                <td>This is title suggestion 3</td>
                <td>lkalif</td>
            </tr>
        </tbody>
    </table>
</div>

<div class="ui-widget-header ui-corner-bottom" style="text-align: right; padding: 5px;">
    <a class="toolbarbutton" href="http://irc.t2t2.eu/">chatrealm</a>
    <a class="toolbarbutton" href="http://diamondclub.tv/">diamondclub.tv</a>
</div>

<script>

function showAlert(msg) {
    $("#msg_area")
        .text(msg)
        .fadeIn(100)
        .delay(2000)
        .fadeOut(50);
}

$(document).ready(function() {
    $( ".toolbarbutton" ).button();
    $( "#radioset" ).buttonset();
    
    $("#refresh_button")
        .button()
        .on("click", function(event) {
            showAlert("Not implemented");
            event.preventDefault();
        });

});
</script>


<?php
Layout::footer();

 