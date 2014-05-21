<?php

//http://tma.truelife.com/tma/truecrm_agent/agent.aspx?apiurl=http://truecardbn.truelife.com/truecardsrv/services/api.aspx&method=get_card_infomation&thaiid=[CARDID]

return array(

    // True Card.
    'truecard' => array(
        // 'agentUrl' => 'http://tma.truelife.com/tma/truecrm_agent/agent.aspx',
        'agentUrl' => 'http://truecardbn.truelife.com/truecardsrv/services/api.aspx',
        'endpoint' => 'http://truecardbn.truelife.com/truecardsrv/services/api.aspx'
    ),

    'truesms' => array(
        'appId'    => '27',
        'endpoint' => 'http://widget3.truelife.com/msisdn_service/rest',
        'secret'   => 'fabe25bf01f789ac7aa2'
    ),
    
    'itruemart' => 'http://itruemart.com'
	'receipt_app_id'	=> '59fea3621bd61',
	'receipt_url' 	=> 'http://api.dev.itruemart.com/rest/receipt/create/format/json',
);