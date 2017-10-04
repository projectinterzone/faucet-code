<?php

// --
// Remember to release your escrow payment to me at cryptothrift.com
// For assistance: Contact cryptosale@slak.org or on cryptothrift.com
// --

// MySQL Database configuarion
//
$dbhost="localhost"; // Your MySQL host eg localhost
$dbuser=""; // Your MySQL username eg root
$dbpass=""; // Your MySQL password for the username above
$database=""; // The MySQL database containing the tables required by this script


// Daemon configuarion
//
// batch_quantity currently set to 1, and will pay EVERY TIME it's accessed. Faucet will
// not keep track of payouts with a proper indexed accounting until changed. 
// Once this package is functioning properly, I suggest you change set it to 5 or 10
//
$coin[1]["name"]="YourCoinName"; // Coin long name
$coin[1]["currency_code"]="YOU"; // Coin currency code
$coin[1]["rpc_host"]="127.0.0.1"; // RPC Host for the daemon. This is usually 127.0.0.1
$coin[1]["rpc_user"]=""; // RPC Username for the daemon. This should match whatever you have set in the config file.
$coin[1]["rpc_pass"]=""; // RPC Password for the daemon. This should match whatever you have set in the config file.
$coin[1]["rpc_port"]=12345; // RPC Port for the daemon. This should match whatever you have set in the config file.
$coin[1]["faucet_account"]=""; // Account name holding the funds, the default wallet account uses "" (unnamed).
$coin[1]["faucet_amount"]=5; // Payout this amount to each user
$coin[1]["batch_quantity"]=1; // Wait until there are at least this many pending payments before processing [1-?] (1 = instant payment)
$coin[1]["no_return_in_hours"]=24; // IP address cannot get another payment for this number of hours
$coin[1]["disable_faucet"]=false; // Disable the faucet for this coin [true|false]
$coin[1]["needs_passphrase"]=false; // If a passphrase is needed to send funds [true|false]
$coin[1]["passphrase_unlock_for"]=10; // If passphrase needed, unlock for this amount of seconds.
$coin[1]["passphrase"]=""; // If passphrase required, enter it here.
$coin[1]["client_download_url"]="http://someurl.com"; // url for users to download client. Perhaps a forum topic link for example.
$coin[1]["block_explorer_url"]="http://someurl.com/tx/"; // url to a block explorer. The txid will be added directly after this to make up the entire url.

?>