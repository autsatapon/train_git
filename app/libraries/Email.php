<?php

class Email {
	
	public function send($to, $subject , $message = '', $apiApp = 'true', $options = array())
	{
		$default = array(
			'cc' => '',
			'bcc' => '',
		);

		$options = array_merge($default, $options);

		$config = Config::get('mailapp');

		$from        = $config['smtp_sender'];
		$from_sender = $config['smtp_fromname'];
		$curlurl     = 'http://www.weloveshopping.com/wetrust/mailwls/sendmail';
		$xml         = '<?xml version="1.0"  encoding="utf-8"?>
			<request>
				<from>'.$from.'</from>
				<fromname>'.$from_sender.'</fromname>
				<to>'.$to.'</to>
				<cc>'.$options['cc'].'</cc>
				<bcc>'.$options['bcc'].'</bcc>
				<subject>'.urlencode($subject).'</subject>
				<message>'.urlencode($message).'</message>
			</request>
		';
		$params = urlencode($xml);

		if ($apiApp == 'google')
		{

			$curlurl = $config['smtp_url'];

			$params = array(
						'app_id'        => $config['smtp_app_id'] , 
						'secret_key'    => $config['smtp_secret_key'] , 
						'mail_sender'   => $from , 
						'mail_fromname' => $from_sender ,
						'mail_to'       => $to,
						'mail_cc'       => $options['cc'],
						'mail_bcc'      => $options['bcc'],
						'subject'       => "=?utf-8?B?".base64_encode($subject)."?="  ,
						'body'          => $message
						);
		}

		$result = with(new Curl)->simple_post($curlurl, $params);

		return $result;

	}

}