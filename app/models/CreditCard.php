<?php
class CreditCard extends Eloquent{
    protected $table = 'credit_cards';
    public static $unguarded = true; // MassAssignmentException
    public static $rules = array(
            'card_number' => 'required|alpha_num|size:16',
            'card_expiry_date' => array(
                    'required',
                    'alpha_dash',
                    'size:7',
                    'regex:/^(01|02|03|04|05|06|07|08|09|10|11|12)-([0-9]{4})$/'
            ),
            'card_type' => array(
                    'required',
                    'alpha_num',
                    'size:3',
                    'regex:/^[0-9]{3}$/'
            ),
            'payment_token' => array(
                    'required',
                    'alpha_num',
                    'min:22',
                    'max:30',
                    'regex:/^[0-9]{22,30}$/'
            ),
            'member_id' => array(
                    'required',
                    'min:1',
                    'numeric'
            )
    );
}

