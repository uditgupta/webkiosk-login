<?php

$MemberCode = $_POST['username'];
$Pass = $_POST['pass'];
$Password= urlencode($Pass);
$ch = curl_init();
$file_path = "cookie.txt";

curl_setopt($ch, CURLOPT_URL,"https://webkiosk.jiit.ac.in/CommonFiles/UserAction.jsp");
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS,
            "txtInst=Institute&InstCode=JIIT&txtuType=Member+Type&UserType=S&txtCode=Enrollment+No&MemberCode=".$MemberCode."&txtPin=Password%2FPin&Password=".$Password);

curl_setopt($ch, CURLOPT_HEADER, true);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch,  CURLOPT_COOKIEJAR, $file_path);
$server_output = curl_exec($ch);

curl_close ($ch);

if($server_output[9] == "3")
{
	echo "Welcome ";
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,"https://webkiosk.jiit.ac.in/CommonFiles/TopTitle.jsp");
	curl_setopt($ch, CURLOPT_HEADER, true);

	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch,  CURLOPT_COOKIEFILE, $file_path);

	$server_output = curl_exec($ch);
	preg_match('/<td>Welcome<br>[A-z]+<br>/',$server_output, $matches);
	$matches = preg_replace('/Welcome<br>/','',$matches[0]);

	echo $matches;
	curl_close ($ch);
}
else 
{
	echo "Wrong username or password";
}
?>
