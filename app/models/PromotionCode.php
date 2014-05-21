<?php

class PromotionCode extends Harvey
{
	public static $rules = array(
		'code' => 'required|unique:promotion_codes,code,NULL,id,status,activate',
        'promotion_id' => 'required|exists:promotions,id',
        'status' => 'required|in:activate,deactivate'
    );

    public function promotion()
    {
    	return $this->belongsTo('Promotion');
    }

    public function promotionCodeLogs()
    {
        return $this->hasMany('PromotionCodeLog');
    }

    public function memberPromotionCodes()
    {
        return $this->hasMany('MemberPromotionCode');
    }

    public function scopeValidCode($query, $code)
    {
        return $query->validCodes()
                     ->whereCode($code);
    }

    public function scopeValidCodes($query)
    {
        return $query->whereStatus('activate')
                     ->where('avaliable', '>', 'used');
    }

    public function isCashVoucher()
    {
        return (boolean) ($this->type == 'cash_voucher');
    }

    public function checkValidCode()
    {
        return (boolean) ($this->status == 'activate' && $this->avaliable > $this->used);
    }

    public function useCode()
    {
    	if ($this->checkValidCode())
    	{
    		if (($this->avaliable - $this->used) <= 1)
    		{
    			$this->status = "deactivate";
    		}

            $this->used = $this->used + 1;

            if ($this->used < 0)
            {
                $this->used = 0;
            }

    		return $this->save();
    	}

    	return false;
    }

    public function recoverCode()
    {
        $this->status = "activate";
        $used = $this->used - 1;
        $this->used = $used > 0 ? $used : 0;
        return $this->save();
    }

}