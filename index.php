<?php

ini_set("display_errors", 0);

session_name("geofaucet");
session_start();
require_once('include/nocsrf.php');
require_once("include/faucet_config.php");
require_once("include/bTemplate.php");
require_once("include/jsonRPCClient.php");
db_connect();

$coin_address=(isset($_POST["coin_address"]) && !empty($_POST["coin_address"])) ? preg_replace("/[^A-Za-z0-9]/", "", $_POST["coin_address"]) : false;
$client = @new jsonRPCClient("http://".$coin[1]["rpc_user"].":".$coin[1]["rpc_pass"]."@".$coin[1]["rpc_host"].":".$coin[1]["rpc_port"]."/");

try
{
    $have_daemon=true;
    $donation_address=$client->getaccountaddress($coin[1]["faucet_account"]);
}
catch ( Exception $e )
{
    $have_daemon=false;
    $donation_address="N/A";
}
if($have_daemon)
{
    $faucet_balance=number_format($client->getbalance($coin[1]["faucet_account"], 6), 2, ".", "");
}
else
{
    $faucet_balance="0.00";
}

$faucet_tpl = new bTemplate();
$batch_amount=($coin[1]["faucet_amount"]*$coin[1]["batch_quantity"]);
$faucet_tpl->set("batch_amount", $batch_amount);
$faucet_tpl->set("faucet_has_funds", (($faucet_balance>=$batch_amount)?true:false), true);
$faucet_tpl->set("donation_address", $donation_address);
$faucet_tpl->set("faucet_balance", $faucet_balance);
$faucet_tpl->set("self","index.php");
$faucet_tpl->set("batch_quantity", $coin[1]["batch_quantity"]);
$faucet_tpl->set("client_download_url", $coin[1]["client_download_url"]);
$faucet_tpl->set("block_explorer_url", $coin[1]["block_explorer_url"]);

$res=sql_to_array("SELECT COUNT(*) `count` FROM `payments` WHERE `coin_type`='1' AND `state`='pending'");
$faucet_tpl->set("current_unpaid", $res[0]["count"]);

$paid_out=0;
$payments_made=0;
$res=sql_to_array("SELECT * FROM `payout_history` WHERE `coin_type`='1' ORDER BY `time_sent` DESC");
if(count($res)>0)
{
    foreach($res as $id => $row)
    {
        $paid_out+=$row["total_sent"];
        $payments_made+=$row["number_of_payees"];
        $res[$id]["time_sent"]=date("jS F Y \a\\t g:i a", $row["time_sent"]);
        $res[$id]["total_sent"]=round($row["total_sent"], 1);
        $res[$id]["transaction_link"]="<a href=\"".$coin[1]["block_explorer_url"].$res[$id]["transaction_id"]."\" target=\"_blank\">Click here</a>";
    }
}
else
{
    $res[0]["total_sent"]="No";
    $res[0]["number_of_payees"]="payments";
    $res[0]["time_sent"]="made";
    $res[0]["transaction_link"]="yet";
}
$faucet_tpl->set("payouts", $res);
$faucet_tpl->set("paid_out", $paid_out);
$faucet_tpl->set("payments_made", $payments_made);
$faucet_tpl->set("name", $coin[1]["name"]);
$faucet_tpl->set("faucet_amount", $coin[1]["faucet_amount"]);
$faucet_tpl->set("currency_code", $coin[1]["currency_code"]);
$faucet_tpl->set("disable_me_1", !$coin[1]["disable_faucet"], true);
$faucet_tpl->set("disable_me_2", $coin[1]["disable_faucet"], true);
$faucet_tpl->set("disable_me_3", $coin[1]["disable_faucet"], true);

if(isset($_POST) && !empty($_POST))
{
    try
    {
        NoCSRF::check('csrf_token', $_POST, true, 60*10, false);
        $result = "OK";
    }
    catch ( Exception $e )
    {
        $result = $e->getMessage() . ' Form ignored.';
    }
    if($result!="OK")
    {
        err_msg("Error", $result);
    }

    if (!class_exists('KeyCAPTCHA_CLASS'))
    {
        include('include/keycaptcha.php');
    }
    $kc_test = new KeyCAPTCHA_CLASS();
    if (!$kc_test->check_result($_POST['capcode']))
    {
        err_msg("Error", "The picture does not match the example!");
    }
    if($coin[1]["disable_faucet"] || $coin[1]["batch_quantity"]==0)
    {
        err_msg("Error", "Sorry, the ".$coin[1]["name"]." faucet is currently disabled. Please check back later!");
    }
    if(!$have_daemon)
    {
        err_msg("Error", "Sorry, we are unable to access the coin daemon right now. Please check back later!");
    }

    $test_address=$client->validateaddress($coin_address);

    if(!$test_address["isvalid"])
    {
        err_msg("Error", "Your ".$coin[1]["name"]." address (".$coin_address.") is invalid!");
    }
    elseif($faucet_balance<($coin[1]["batch_quantity"]*$coin[1]["faucet_amount"]))
    {
        err_msg("Error", "We do not have sufficient funds to pay the next batch. Please check back later!");
    }
    else
    {
        mysql_query("UPDATE `payments` SET `timestamp`=UNIX_TIMESTAMP() WHERE `state`='pending'");
        mysql_query("DELETE FROM `payments` WHERE `state`='paid' AND `timestamp`<(UNIX_TIMESTAMP()-".($coin[1]["no_return_in_hours"]*3600).")");
        if(isset($_SESSION["username"]) && $_SESSION["username"] > (time()-($coin[1]["no_return_in_hours"]*3600)))
        {
            $_SESSION["username"]=time();
                err_msg("Error", "It has been too soon since your last visit, please come back later!");
        }
        $octets=explode(".", $_SERVER["REMOTE_ADDR"]);
        $result=sql_to_array("SELECT `id` FROM `payments` WHERE `coin_type`='1' AND (`ip_address` LIKE '".$octets[0].".".$octets[1].".%' OR `coin_address`='".mysql_real_escape_string($coin_address)."')");
        if(count($result)>0)
        {
            $_SESSION["username"]=time();
            err_msg("Error", "It has been too soon since your last visit, please come back later!");
        }
        else
        {
            if($coin[1]["batch_quantity"]==1)
            {
                $_SESSION["username"]=time();
                mysql_query("INSERT INTO `payments` (`coin_type`, `coin_address`, `ip_address`, `timestamp`, `state`) VALUES ('1', '".mysql_real_escape_string($coin_address)."', '".mysql_real_escape_string($_SERVER["REMOTE_ADDR"])."', UNIX_TIMESTAMP(), 'paid')");
                if($coin[1]["needs_passphrase"])
                {
                    $client->walletpassphrase($coin[1]["passphrase"], $coin[1]["passphrase_unlock_for"]);
                }
                $client->sendfrom($coin[1]["faucet_account"], $coin_address, $coin[1]["faucet_amount"]);
                success_msg("Success", "Your funds have been sent!");
            }
            else
            {
                mysql_query("INSERT INTO payments (`coin_type`, `coin_address`, `ip_address`, `timestamp`, `browser`) VALUES ('1', '".mysql_real_escape_string($coin_address)."', '".mysql_real_escape_string($_SERVER["REMOTE_ADDR"])."', UNIX_TIMESTAMP(), '".mysql_real_escape_string($_SERVER["HTTP_USER_AGENT"])."')");
                $_SESSION["username"]=time();
                $result=sql_to_array("SELECT COUNT(*) `count` FROM `payments` WHERE `coin_type`='1' AND `state`='pending'");
                if($result[0]["count"]==$coin[1]["batch_quantity"])
                {
                    $result=sql_to_array("SELECT `id`, `coin_address` FROM `payments` WHERE `coin_type`='1' AND `state`='pending'");
                    if(count($result)>0)
                    {
                        $ids="";
                        $payout_list=array();
                        foreach($result as $row)
                        {
                            $payout_list[$row["coin_address"]]=$coin[1]["faucet_amount"];
                            $ids.=$row["id"].",";
                        }
                        $ids=trim($ids, ",");
                        if($coin[1]["needs_passphrase"])
                        {
                            $client->walletpassphrase($coin[1]["passphrase"], $coin[1]["passphrase_unlock_for"]);
                        }
                        $txid=$client->sendmany($coin[1]["faucet_account"], $payout_list);
                        mysql_query("INSERT INTO `payout_history` (`coin_type`, `number_of_payees`, `total_sent`, `time_sent`, `transaction_id`) VALUES ('1', '".$coin[1]["batch_quantity"]."', '".($coin[1]["batch_quantity"]*$coin[1]["faucet_amount"])."', UNIX_TIMESTAMP(), '".$txid."')");
                        mysql_query("UPDATE `payments` SET `state`='paid', `timestamp`=UNIX_TIMESTAMP() WHERE `id` IN(".$ids.")");
                    }
                    success_msg("Success", "Your funds have been sent!");
                }
                else
                {
                    success_msg("Success", "Your request for funds has been noted and you shall receive it when there are ".$coin[1]["batch_quantity"]." addresses to pay!");
                }
            }
        }
    }
}
if (!class_exists('KeyCAPTCHA_CLASS'))
{
	include('include/keycaptcha.php');
}
$kc_o = new KeyCAPTCHA_CLASS();
$faucet_tpl->set("captcha", $kc_o->render_js());
$faucet_tpl->set("csrf_token", NoCSRF::generate("csrf_token"));
echo $faucet_tpl->fetch("index.tpl");
die;

function sql_to_array($sqlquery)
{
    $output=array();
    $res=mysql_query($sqlquery);
    if(@mysql_num_rows($res)>0)
    {
        while($row=mysql_fetch_assoc($res))
        {
            $output[]=$row;
        }
    }
    return $output;
}
function db_connect()
{
    global $dbhost, $dbpass, $dbuser, $database;
    $dbconnect=mysql_connect($dbhost, $dbuser, $dbpass) or die("Cannot connect to the MySQL Server with the supplied credentials");
    mysql_select_db($database, $dbconnect) or die("Cannot select the database (".$database.") with the supplied credentials");
}
function err_msg($heading = 'Error!', $string)
{
    global $coin;

    $err_tpl = new bTemplate();
    $err_tpl->set('error_title', $heading);
    $err_tpl->set('error_message', $string);
    $err_tpl->set('error_image', 'images/error.gif');
    $err_tpl->set('name', $coin[1]["name"]);
    echo $err_tpl->fetch('error.tpl');
    die;
}
function success_msg($heading = 'Success!', $string)
{
    global $coin;

    $suc_tpl = new bTemplate();
    $suc_tpl->set('success_title', $heading);
    $suc_tpl->set('success_message', $string);
    $suc_tpl->set('success_image', 'images/success.gif');
    $suc_tpl->set('name', $coin[1]["name"]);
    echo $suc_tpl->fetch('success.tpl');
    die;
}

?>