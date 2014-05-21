<?php

use Illuminate\Database\Eloquent\Collection;

class PromotionRepository implements PromotionRepositoryInterface {

	private $dependencies = array();

	private $instances = array();

	public function __construct(
		Promotion $promotion ,
		Product $product ,
		Brand $brand ,
		ProductVariant $variant ,
		Collection $collection
	) {
		$this->dependencies = compact('promotion', 'product', 'variant', 'collection');
	}

	public function setPromotion($ids = array())
	{
		$ids = (array) $ids;

		$this->instances['promotion'] = $this->dependencies['promotion']->whereIn('id', $ids)->active()->get();
	}

	public function setActivePromotions()
	{
		$this->instances['promotion'] = $this->dependencies['promotion']->active()->get();
	}

	public function getPromotion()
	{
		return $this->instances['promotion'];
	}

	public function getDiscountItems()
	{
		return $this->getFollowingItem('discount');
	}

	public function getFreeItems()
	{
		return $this->getFollowingItem('free');
	}

	private function getFollowingItem($type)
	{
		if ( ! in_array($type, array('discount', 'free')))
		{
			return false;
		}

		if ( ! empty($this->instances['promotion']) && $this->instances['promotion'] instanceof Illuminate\Database\Eloquent\Collection)
		{
			$followingItems = array();

			foreach($this->instances['promotion'] as $promotion)
			{
				$effects = $promotion->effects;

				if ($effects[$type]['on'] != 'following')
				{
					continue;
				}

				$items = array();

				$which = $effects[$type]['which'];
				$itemIds = $effects[$type]['following_items'];

				switch ($which) {
					case 'product':
						$products = $this->dependencies[$which]->whereIn('pkey', $itemIds)->get();

						break;

					case 'variant':
						$items = $this->dependencies[$which]->whereIn('inventory_id', $itemIds)->get();
						break;

					default:
						break;
				}




			// target return inventory id

			}
		}
	}
}