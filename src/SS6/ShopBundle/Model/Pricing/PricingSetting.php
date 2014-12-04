<?php

namespace SS6\ShopBundle\Model\Pricing;

use SS6\ShopBundle\Model\Product\Pricing\ProductPriceRecalculationScheduler;
use SS6\ShopBundle\Model\Setting\Setting;
use SS6\ShopBundle\Model\Setting\SettingValue;

class PricingSetting {

	const INPUT_PRICE_TYPE = 'inputPriceType';
	const ROUNDING_TYPE = 'roundingType';

	const INPUT_PRICE_TYPE_WITH_VAT = 1;
	const INPUT_PRICE_TYPE_WITHOUT_VAT = 2;

	const ROUNDING_TYPE_HUNDREDTHS = 1;
	const ROUNDING_TYPE_FIFTIES = 2;
	const ROUNDING_TYPE_INTEGER = 3;

	/**
	 * @var \SS6\ShopBundle\Model\Setting\Setting
	 */
	private $setting;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Pricing\ProductPriceRecalculationScheduler
	 */
	private $productPriceRecalculationScheduler;

	public function __construct(
		Setting $setting,
		ProductPriceRecalculationScheduler $productPriceRecalculationScheduler
	) {
		$this->setting = $setting;
		$this->productPriceRecalculationScheduler = $productPriceRecalculationScheduler;
	}

	/**
	 * @return int
	 */
	public function getInputPriceType() {
		return $this->setting->get(self::INPUT_PRICE_TYPE, SettingValue::DOMAIN_ID_COMMON);
	}

	/**
	 * @return int
	 */
	public function getRoundingType() {
		return $this->setting->get(self::ROUNDING_TYPE, SettingValue::DOMAIN_ID_COMMON);
	}

	/**
	 * @param int $roundingType
	 */
	public function setRoundingType($roundingType) {
		if (!in_array($roundingType, $this->getRoundingTypes())) {
			throw new \SS6\ShopBundle\Model\Pricing\Exception\InvalidRoundingTypeException(
				sprintf('Rounding type %s is not valid', $roundingType)
			);
		}

		$this->setting->set(self::ROUNDING_TYPE, $roundingType, SettingValue::DOMAIN_ID_COMMON);
		$this->productPriceRecalculationScheduler->scheduleRecalculatePriceForAllProducts();
	}

	/**
	 * @return array
	 */
	public static function getInputPriceTypes() {
		return array(
			self::INPUT_PRICE_TYPE_WITHOUT_VAT,
			self::INPUT_PRICE_TYPE_WITH_VAT,
		);
	}

	/**
	 * @return array
	 */
	public static function getRoundingTypes() {
		return array(
			self::ROUNDING_TYPE_HUNDREDTHS,
			self::ROUNDING_TYPE_FIFTIES,
			self::ROUNDING_TYPE_INTEGER,
		);
	}

}
