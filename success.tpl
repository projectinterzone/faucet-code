<!DOCTYPE HTML>
<html>
<head>
<title><tag:name /> Faucet</title>
<style type="text/css">
body {
background-color:black;
color:white;
}
.success { 
color:black; 
font-weight: bold; 
font-size: 14pt; 
background:url(images/chg.gif); 
background-repeat: repeat-x;
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
<br />
  <table border="1" width="500" cellspacing="0" cellpadding="0" style="border-color:#00C900" >
    <tr>
       <td class="success" valign="bottom" align="center">
       <tag:success_title />
       </td>
    </tr>
    <tr>
      <td bgcolor="#FFFFFF" align="center" style="color:#000000;">
        <img src="<tag:success_image />" alt="" style="float:left; margin:5px" />
        <br />
        <tag:success_message />
        <br />
        <br />        
      </td>
    </tr>
  </table>
</div>
<div align="center">
  <a href="javascript:history.go(-1);"><span style="color:lime;">Back</span></a>
</div>
</body>
</html>
