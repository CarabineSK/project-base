<?php

namespace SS6\ShopBundle\Component\Translation;

use Symfony\Bundle\FrameworkBundle\Translation\Translator as BaseTranslator;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Translation\MessageSelector;

class Translator extends BaseTranslator {

	const DEFAULT_DOMAIN = 'messages';
	const NOT_TRANSLATED_PREFIX = '##';
	const SOURCE_LOCALE = 'cs';

	/**
	 * @var \Symfony\Component\Translation\MessageSelector
	 */
	private $messageSelector;

	public function __construct(
		ContainerInterface $container,
		MessageSelector $selector,
		$loaderIds = [],
		array $options = []
	) {
		parent::__construct($container, $selector, $loaderIds, $options);

		$this->messageSelector = $selector;
	}

	/**
	 * When translation for given locale is not defined and locale is not self::SOURCE_LOCALE,
	 * function returns translation ID string with self::NOT_TRANSLATED_PREFIX prefix.
	 * {@inheritdoc}
	 *
	 * @api
	 */
	public function trans($id, array $parameters = [], $domain = self::DEFAULT_DOMAIN, $locale = null) {
		if ($locale === null) {
			$locale = $this->getLocale();
		} else {
			$this->assertValidLocale($locale);
		}

		if (!isset($this->catalogues[$locale])) {
			$this->loadCatalogue($locale);
		}

		$catalogue = $this->catalogues[$locale];
		if ($catalogue->defines($id, $domain)) {
			return strtr($this->catalogues[$locale]->get((string)$id, $domain), $parameters);
		} elseif ($locale === self::SOURCE_LOCALE) {
			return strtr($id, $parameters);
		} else {
			return self::NOT_TRANSLATED_PREFIX . strtr($id, $parameters);
		}
	}

	/**
	 * When translation for given locale is not defined and locale is not self::SOURCE_LOCALE,
	 * function returns translation ID string with self::NOT_TRANSLATED_PREFIX prefix.
	 * {@inheritdoc}
	 *
	 * @api
	 */
	public function transChoice($id, $number, array $parameters = [], $domain = self::DEFAULT_DOMAIN, $locale = null) {
		if ($locale === null) {
			$locale = $this->getLocale();
		} else {
			$this->assertValidLocale($locale);
		}

		if (!isset($this->catalogues[$locale])) {
			$this->loadCatalogue($locale);
		}

		$id = (string)$id;

		$catalogue = $this->catalogues[$locale];

		if ($catalogue->defines($id, $domain)) {
			$message = $this->messageSelector->choose($catalogue->get($id, $domain), (int)$number, $locale);
		} else {
			$message = $this->messageSelector->choose($id, (int)$number, self::SOURCE_LOCALE);
		}

		$message = strtr($message, $parameters);

		if (!$catalogue->defines($id, $domain) && $locale !== self::SOURCE_LOCALE) {
			$message = self::NOT_TRANSLATED_PREFIX . $message;
		}

		return $message;
	}

}
