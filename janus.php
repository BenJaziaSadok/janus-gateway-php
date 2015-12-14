/*
 *  This file contains client side REST calls for janus-gateway
 *  With this example you can use the admin-api in your janus infrastructure
 *  and call it securely
 *  
 * This is a beta-version in php
 * Feel free to update
 */
 
 <?php
error_reporting(E_ALL ^ E_NOTICE);

function generateRandomString($length = 12) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function CallAPI($method, $url, $data = false)
{
    $curl = curl_init();

    switch ($method)
    {
        case "POST":
            curl_setopt($curl, CURLOPT_POST, 1);

            if ($data)
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            break;
        case "PUT":
            curl_setopt($curl, CURLOPT_PUT, 1);
            break;
        default:
            if ($data)
                $url = sprintf("%s?%s", $url, http_build_query($data));
    }

    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

    $result = curl_exec($curl);

    curl_close($curl);

    return $result;
}

if(!isset($_POST['action']));
$action = $_POST['action'];

if(!isset($_POST['sessionId']));
$sessionId = $_POST['sessionId'];

if(!isset($_POST['plugin']));
$plugin = $_POST['plugin'];

if(!isset($_POST['handleId']));
$handleId = $_POST['handleId'];

if(!isset($_POST['message']));
$message = $_POST['message'];

if(!isset($_POST['candidate']));
$candidate = $_POST['candidate'];

if(!isset($_POST['jsep']));
$jsep = $_POST['jsep'];

//api_secret = janusrocks         ; String that all Janus requests must contain
//admin_secret = janusoverlord 

$server='http://server/janus';
$password='janusrocks';

switch($action){
	case 'CreateSession' :{
		$data='{"janus":"create","transaction":"'.generateRandomString().'","apisecret":"'.$password.'"}';
		$result=CallAPI('POST',$server,$data);
		echo ($result);
		}
	break;
	case 'Refresh' :{
		$date = date_create();
		date_timestamp_get($date);

		$longpoll = $server."/".$sessionId."?rid=".date_timestamp_get($date);
		$longpoll = $longpoll .'&maxev=1&_='.date_timestamp_get($date).'&apisecret='.$password;
		$result=CallAPI('GET',$longpoll);
		echo ($result);
		}
	break;
		case 'createHandle' :{
		$data = '{"janus": "attach", "plugin": "'.$plugin.'", "transaction": "'.generateRandomString().'","apisecret":"'.$password.'"}';
		$server=$server.'/'.$sessionId;
		$result=CallAPI('POST',$server,$data);
		echo ($result);
		}
	break;
	case 'destroySession' :{
		$data = '{"janus": "destroy", "transaction": "'.generateRandomString().'","apisecret":"'.$password.'"}';
		$server=$server.'/'.$sessionId;
		$result=CallAPI('POST',$server,$data);
		echo ($result);
		}
	break;
		case 'sendMessage' :{

if ($jsep==''){$request = '{"janus": "message", "body": '.$message.', "transaction": "'.generateRandomString().'","apisecret":"'.$password.'"}';}
else{$request = '{"janus": "message", "body": '.$message.', "transaction": "'.generateRandomString().'","jsep":'.$jsep.',"apisecret":"'.$password.'"}';}


		//	echo $request;
		$server=$server.'/'.$sessionId.'/'.$handleId;
		$result=CallAPI('POST',$server,$request);
		echo ($result);
		}
	break;
		case 'sendTrickleCandidate' :{
		$request = '{"janus": "trickle", "candidate": '.$candidate.', "transaction": "'.generateRandomString().'","apisecret":"'.$password.'"}';

		$server=$server.'/'.$sessionId.'/'.$handleId;
		$result=CallAPI('POST',$server,$request);
		echo ($result);
		}
	break;
			case 'destroyHandle' :{
		$request = '{"janus":"detach","transaction":"'.generateRandomString().'","apisecret":"'.$password.'"}';

		$server=$server.'/'.$sessionId.'/'.$handleId;
		$result=CallAPI('POST',$server,$request);
		echo ($result);
		}
	break;


;break;

	default:{die();}
}

