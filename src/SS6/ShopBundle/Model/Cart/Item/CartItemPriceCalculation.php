<?php

namespace SS6\ShopBundle\Model\Cart\Item;

use SS6\ShopBundle\Model\Cart\Item\CartItem;
use SS6\ShopBundle\Model\Cart\Item\CartItemPrice;
use SS6\ShopBundle\Model\Pricing\Rounding;
use SS6\ShopBundle\Model\Product\Pricing\ProductPriceCalculationForUser;

class CartItemPriceCalculation {

	/**
	 * @var \SS6\ShopBundle\Model\Product\Pricing\ProductPriceCalculationForUser
	 */
	private $productPriceCalculationForUser;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Rounding
	 */
	private $rounding;

	/**
	 * @var \SS6\ShopBundle\Model\Cart\Item\CartItem
	 */
	private $cartItem;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Price
	 */
	private $productPrice;

	/**
	 * @param \SS6\ShopBundle\Model\Product\Pricing\ProductPriceCalculationForUser $productPriceCalculationForUser
	 * @param \SS6\ShopBundle\Model\Pricing\Rounding$rounding
	 */
	public function __construct(ProductPriceCalculationForUser $productPriceCalculationForUser, Rounding $rounding) {
		$this->productPriceCalculationForUser = $productPriceCalculationForUser;
		$this->rounding = $rounding;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Cart\Item\CartItem $cartItem
	 * @return \SS6\ShopBundle\Model\Cart\Item\CartItemPrice
	 */
	public function calculatePrice(CartItem $cartItem) {
		$this->cartItem = $cartItem;
		$this->productPrice = $this->productPriceCalculationForUser->calculatePriceForCurrentUser($cartItem->getProduct());

		$cartItemPrice = new CartItemPrice(
			$this->productPrice->getPriceWithoutVat(),
			$this->productPrice->getPriceWithVat(),
			$this->productPrice->getVatAmount(),
			$this->getTotalPriceWithoutVat(),
			$this->getTotalPriceWithVat(),
			$this->getTotalPriceVatAmount()
		);

		return $cartItemPrice;
	}

	/**
	 * @return string
	 */
	private function getTotalPriceWithoutVat() {
		return $this->getTotalPriceWithVat() - $this->getTotalPriceVatAmount();
	}

	/**
	 * @return string
	 */
	private function getTotalPriceWithVat() {
		return $this->productPrice->getPriceWithVat() * $this->cartItem->getQuantity();
	}

	/**
	 * @return string
	 */
	private function getTotalPriceVatAmount() {
		return $this->rounding->roundVatAmount(
			$this->getTotalPriceWithVat() * $this->cartItem->getProduct()->getVat()->getCoefficient()
		);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Cart\Item\CartItem[] $cartItems
	 * @return \SS6\ShopBundle\Model\Cart\Item\CartItemPrice[] array indices are preserved
	 */
	public function calculatePrices(array $cartItems) {
		$cartItemPrices = array();
		foreach ($cartItems as $key => $cartItem) {
			$cartItemPrices[$key] = $this->calculatePrice($cartItem);
		}

		return $cartItemPrices;
	}

}
