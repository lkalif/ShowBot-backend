<?php
/**
 * Main API calling point
 * 
 * @package ShowBot-backend
 * @author Latif Khalifa <latifer@streamgrid.net>
 * @copyright Copyright(c) 2014, Latif Khalifa
 * @license http://opensource.org/licenses/MIT
 */
define("SITE_ROOT", dirname(__file__));
require_once SITE_ROOT . "/lib/init.php";

/**
 * Result class
 *
 * Result is sent in form of Success boolean
 * with accompanying Message
 */
class ResponseStatus
{
    public $Success = false;
    public $Message;
}

/**
 * Base class for incoming requests
 *
 * Incoming requests are POST-ed to this URL with
 * JSON datra that at least has $ServerID and $Channel
 * set. ApiAuth is required for all request that modify
 * or store new data
 */
class Request
{
    public $ApiAuth;
    public $ServerID;
    public $Channel;

    function __construct($json = null)
    {
        if ($json)
        {
            $this->FromJSON($json);
        }
    }

    function FromJSON($json)
    {
        foreach($json as $key => $val)
        {
            $this->$key = trim((string)$val);
        }
    }
}

/**
 * Add new suggestion
 */
class Sugggestion extends Request
{
    public $User;
    public $Title;
    
    function getDigest()
    {
        $t = preg_replace('/[^\w]/', ' ', strtolower($this->Title));
        $t = preg_replace('/\s+/', ' ', $t);
        return sha1($t);
    }
    
    function exists()
    {
        $digest = $this->getDigest();
        $q = kl_str_sql("select count(*) as nr from suggestions where server_id=!s and channel=!s and digest=!s", $this->ServerID, $this->Channel, $digest);
        if ($res = DBH::$db->query($q))
        {
            if ($row = DBH::$db->fetchRow($res))
            {
                return $row["nr"] !== "0";
            }
        }
        
        return false;
    }
    
    function add()
    {
        $q = kl_str_sql("insert into suggestions(server_id, channel, digest, suggestion, user) values (!s, !s, !s, !s, !s)",
                        $this->ServerID,
                        $this->Channel,
                        $this->getDigest(),
                        $this->Title,
                        $this->User);

        if (!$res = DBH::$db->query($q))
        {
            return -1;
        }
        else
        {
            return DBH::$db->insertID();
        }
    }
    
    static function getSorted($serverID, $channel)
    {
        $ret = [];
        $q = kl_str_sql("select * from suggestions where server_id=!s and channel=!s order by votes desc, id asc", $serverID, $channel);
        if (!$res = DBH::$db->query($q))
        {
            return $ret;
        }
        while ($row = DBH::$db->fetchRow($res))
        {
            $s = new Sugggestion;
            $s->ID = $row["id"];
            $s->ServerID = $row["server_id"];
            $s->Channel = $row["channel"];
            $s->Digest = $row["digest"];
            $s->Votes = $row["votes"];
            $s->Title = $row["suggestion"];
            $s->User = $row["user"];
            $ret[] = $s;
        }
        
        return $ret;
    }
}

/**
 * Sends back response and terminates execution
 *
 * @param bool $success Indicates if the operation was successful
 * @param string $message Text explaining the status of the operation
 */
function respond($success, $message)
{
    header("Content-Type: application/json");
    $res = new ResponseStatus;
    $res->Success = $success;
    $res->Message = $message;
    print json_encode($res);
    die();
}

/**
 * Apply rate limiting. Send error and terminate when limit is exceeded.
 *
 * @param string $id Unique ID of the type of request. For instence client IP + operation
 * @param int $limit Maximum number of requsts
 * @param int $period Number of seconds during which $limit must not be exceeded
 */
function rateLimit($id, $limit, $period)
{
    $key = "ratelimit_$id";
    
    $nr = Memc::$daemon->get($key);
    
    if ($nr === false)
    {
        Memc::$daemon->set($key, 1, $period);
    }
    else if ($nr < $limit)
    {
        Memc::$daemon->increment($key);
    }
    else
    {
        //http_response_code(403);
        respond(false, "Rate of max $limit per $period seconds exceeded");
    }
}

/**
 * Check authentication
 *
 * This function checks if the request is allowed to proceed-
 * It by default checks the authentication token, and that
 * ServerID and Channel are on the allowed list set in init.php
 * configuration file.
 *
 * @param object $req JSON deserialized request data
 * @param bool $requireApiAuth Should auth token be checked. Default is true.
 **/
function checkAuth($req, $requireApiAuth = true)
{
    global $ALLOWED_SERVERS, $ALLOWED_CHANNELS;
    
    if ($requireApiAuth && (!isset($req->ApiAuth) || API_AUTH !== $req->ApiAuth))
    {
        respond(false, "Access denied");
    }
    
    if (!in_array($req->ServerID, $ALLOWED_SERVERS, true)
        || !in_array($req->Channel, $ALLOWED_CHANNELS[$req->ServerID]))
    {
        respond(false, "Unknown channel");
    }
}

/**
 * Main
 */
$input = file_get_contents("php://input");
$req = json_decode($input);
@file_put_contents("/tmp/showbot.txt", var_export($input, true));

if (!$req)
{
    respond(false, "No data");
}

if (!isset($req->Function))
{
    respond(false, "Invalid request: func not specified");
}

$func = (string)$req->Function;

switch ($func)
{
    case "suggestion_add":
        $suggestion = new Sugggestion($req);
        if (!$suggestion->Title || !$suggestion->User)
        {
            respond(false, "Empty user or title");
        }
        checkAuth($req);
        //rateLimit("add_sug_" + $_SERVER["REMOTE_ADDR"], 1, 10);
        
        if ($suggestion->exists())
        {
            respond(false, "Already added");
        }
        
        $id = $suggestion->add();
        
        if ($id == -1)
        {
            respond(false, "Adding suggestion failed");
        }
        else
        {
            respond(true, "Added, thanks! ($id)");
        }
        
        break;
    
    case "channel_reset":
        checkAuth($req);
        $r = new Request($req);
        DBH::$db->query(kl_str_sql("delete from suggestions where server_id=!s and channel=!s", $r->ServerID, $r->Channel));
        respond(true, "channel {$r->Channel} reset");
        break;
    
    case "channel_top":
        checkAuth($req);
        $res = Sugggestion::getSorted($req->ServerID, $req->Channel);
        $out = "Results:\n";
        for ($i = 0; $i < 5 && $i < count($res); $i++)
        {
            $out .= "({$res[$i]->Votes} votes) {$res[$i]->Title} ({$res[$i]->User})\n";
        }
        respond(true, $out);
        break;

    case "web_top":
        checkAuth($req, false);
        $res = Sugggestion::getSorted($req->ServerID, $req->Channel);
        header("Content-Type: application/json");
        print json_encode($res);
        die();
        break;
    
    default:
        respond(false, "Invalid request: unknown func");
}
