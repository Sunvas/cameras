<?php
/** Token generator. Access to this file should be restricted via http authorization from the panel. Access to cameras is opened on the basis of the generated token.
 * URL to this file is mentioned in the application.
 * @author Alexnader Sunvas <a@sunvas.online>
 * @date 2024-04-03 */

const
	NS='Cache-Control: no-store',//Shortland of a single header
	TIME_OFFSET=1712091600,//Time offset preventing numbers in the file of being too big
	TIMEUP=86400,//Authorization validity period
	SECRET_KEY='your_secret_key_should_be_here',//Secret key
	LOGS=__DIR__.'/access.log';//File with logs

//No http authorization - restrict access
if(!isset($_SERVER['HTTP_AUTHORIZATION'],$_SERVER['REMOTE_ADDR']))
{
	header(NS,true,403);
	die;
}

$expires=time()+TIMEUP-TIME_OFFSET;
$signature=hash_hmac('sha3-256',$expires,SECRET_KEY);

//Log: date, login, ip
if(LOGS)
{
	$au=(string)$_SERVER['HTTP_AUTHORIZATION'];
	$au=explode(' ',$au);
	$au=base64_decode(array_pop($au));

	[$login,$pass]=explode(':',$au);
	$ip=$_SERVER['REMOTE_ADDR'];
	
	$date=date('Y-m-d H:i:s');
	file_put_contents(LOGS,$date."\t{$login}\t{$ip}\n",FILE_APPEND);
}

header(NS,true,200);
echo $expires,'.',$signature;