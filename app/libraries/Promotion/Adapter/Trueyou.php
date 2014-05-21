<?php namespace Promotion\Adapter;

class Trueyou extends AdapterAbstract implements AdapterInterface {

    public function attach($promotion, $attrs = array())
    {
        $variantIds = $this->findVariantIds();

        if (count($variantIds))
        {
            $sync = array_combine($variantIds, array_fill(0, count($variantIds), $attrs));
            return $promotion->variants()->sync($sync);
        }
        else
        {
            return;
        }

    }

}