<?php

class SMS {

	public function send($phone_no, $message, $reference_id = null)
	{
		if ($reference_id == false)
		{
			$reference_id = time();
		}

		$params = compact('phone_no', 'message', 'reference_id');

		$url = "http://secure.truelife.com/messagegw/service.aspx";  // Pro
		$xml_request = '<?xml version="1.0" encoding="utf-8" ?>
			 <request>
				 <header>
					 <app_id>1032006</app_id>
					 <app_pwd>52Y1%BpP</app_pwd>
					 <tran_id>'.$params['reference_id'].'</tran_id>
					 <vsid>0102179320</vsid>
					 <message_type>sms</message_type>
					 <send_type>normal</send_type>
					 <operator_name>realmove</operator_name>
					 <sender>iTrueMart</sender>
					 <message>'.$params['message'].'</message>
					 <recipient>'.$params['phone_no'].'</recipient>
					 <reference_id>'.$params['reference_id'].'</reference_id>
					 <message_seq_id></message_seq_id>
					 <shortcode></shortcode>
				</header>
			</request>';

		$curl = new Curl;
		$response = $curl->simple_post($url, $xml_request);

		/* success (true number) <?xml version="1.0" encoding="utf-8"?><response><header><tran_id>123</tran_id><message_id><![CDATA[13903914926442914]]></message_id><code>200</code><description><![CDATA[Success]]></description></header></response> */
		/* failed (not true number) <?xml version="1.0" encoding="utf-8"?><response><header><tran_id>123</tran_id><message_id><![CDATA[13903914926442914]]></message_id><code>500</code><description><![CDATA[Success]]></description></header></response> */
		if ($response && preg_match('!<code>([0-9]+)</code>!', $response, $matches))
		{
			$code = $matches[1];

			if ($code == 200)
			{
				return true;
			}
		}

		return false;
	}

}