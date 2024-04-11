<?php
/** Authorization for mediamtx based on tokens.
 * URL to this file should be written in externalAuthenticationURL parameter of mediamtx.yml
 * @author Alexnader Sunvas <a@sunvas.online>
 * @date 2024-04-03 */
 
const
	NS='Cache-Control: no-store',//Shortland of a single header
	TIME_OFFSET=1712091600,//Time offset preventing numbers in the file of being too big
	SECRET_KEY='your_secret_key_should_be_here',//Secret key
	LOGS=__DIR__.'/restricted/fail.log';

$post=in_array($_SERVER['REQUEST_METHOD'],['POST','PUT']);

if(!$_POST && $post)
	$_POST+=json_decode(trim(file_get_contents('php://input')),true) ?? [];

if(isset($_POST['query']))
{
	$ip=(string)($_POST['ip'] ?? '');

	#Local bypass
	if(str_starts_with($ip,'192.168.'))
		goto Good;

	$token=(string)$_POST['query'];

	#Token should consist of 2 parts: [expires].[signature]
	if(!str_contains($token,'.'))
		goto Bad;

	[$expires,$usersign]=explode('.',$token);
	$signature=hash_hmac('sha3-256',$expires,SECRET_KEY);

	if(hash_equals($signature,$usersign) && (time()-TIME_OFFSET)<(int)$expires)
	{
		Good:
		header(NS,true,204);
		die;
	}

	Bad:

	//Log of unsuccessful attempts
	if(LOGS)
	{
		$date=date('Y-m-d H:i:s');
		$path=(string)($_POST['path'] ?? '-');
		$user=(string)($_POST['user'] ?? '');
		$pass=(string)($_POST['password'] ?? '-');

		if($user)
			file_put_contents(LOGS,$date."\t{$user}\t{$pass}\t{$path}\t{$ip}\n",FILE_APPEND);
	}
}

header(NS,true,403);