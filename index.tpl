<!DOCTYPE HTML>
<html>
<head>
<title><tag:name /> Faucet</title>
<style type="text/css">
body {
background-color:black;
color:white;
}
a:link {color:#FFFF00;}      /* unvisited link */
a:visited {color:#00FF00;}  /* visited link */
a:hover {color:#FF0000;}  /* mouse over link */
a:active {color:#0000FF;}  /* selected link */ 
</style>
</head>

<body>

<div align="center"><h1><span style="font-family:Arial, Helvetica, sans-serif"><tag:name /> Faucet</span></h1></div>
<div align="center">
<if:faucet_has_funds>
Enter your <a href="<tag:client_download_url />" target="_blank"><tag:name /></a> address below to receive a gift of <tag:faucet_amount /> <tag:currency_code />:<br />
<tag:captcha /><br />
<form name="faucet" action="<tag:self />" method="post">
<if:disable_me_1><input type="hidden" name="csrf_token" value="<tag:csrf_token />" /></if:disable_me_1>
<input type="hidden" name="capcode" id="capcode" value="false" />
<input type="text" name="coin_address" size="50"<if:disable_me_2> value="<tag:name /> faucet currently closed" disabled<else:disable_me_2> value=""</if:disable_me_2> />
<input type="submit" id="postbut" name="submit" value="Submit"<if:disable_me_3> disabled</if:disable_me_3> />
</form>
<br />
The funds will be sent when there are <tag:batch_quantity /> payments pending. [<tag:current_unpaid />/<tag:batch_quantity />].
<br /><br />
<span style="font-weight:bold;">Faucet Balance:</span> <tag:faucet_balance /> <tag:currency_code />.<br /><span style="font-weight:bold;">Donate to this faucet:</span> <tag:donation_address />
<else:faucet_has_funds>
<span style="font-weight:bold;color:red">Faucet Balance: <tag:faucet_balance /></span> <span style="color:red"><tag:currency_code />.</span><br /><br />This faucet is currently closed as there are insufficient funds to pay the next <tag:batch_amount /> <tag:currency_code /> batch.<br />If you would like to donate to reopen this faucet you can send <tag:name /> to this address:<br /><br /><tag:donation_address /><br /><br />The faucet will automatically reopen once there are sufficient <tag:name /> to pay the next batch.
</if:faucet_has_funds>
</div>
<br />
<div align="center">
<table>
  <tr>
    <th width="100">Total Sent</th>
    <th width="125">Number of payees</th>
    <th width="200">Date/Time sent</th>
    <th width="150">Block Explorer</th>
  </tr>
  <loop:payouts>
  <tr>
    <td style="text-align:center"><tag:payouts[].total_sent /> <tag:currency_code /></td>
    <td style="text-align:center"><tag:payouts[].number_of_payees /></td>
    <td style="text-align:center"><tag:payouts[].time_sent /></td>
    <td style="text-align:center"><tag:payouts[].transaction_link /></td>
  </tr>
  </loop:payouts>
  <tr>
    <td style="text-align:center;font-weight:bold;color:yellow;"><tag:paid_out /> <tag:currency_code /></td>
    <td style="text-align:center;font-weight:bold;color:yellow;"><tag:payments_made /></td>
    <td style="text-align:center;font-weight:bold;">&nbsp;</td>
    <td style="text-align:center;font-weight:bold;">&nbsp;</td>
  </tr>
</table>
</div>
<br />
</body>
</html>