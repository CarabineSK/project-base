<?php

namespace SS6\ShopBundle\Component\HttpFoundation;

use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class ResponseListener {

	/**
	 * @param \Symfony\Component\HttpKernel\Event\FilterResponseEvent $event
	 */
	public function onKernelResponse(FilterResponseEvent $event) {
		$event->getResponse()->headers->set('X-Frame-Options', 'deny');
		$event->getResponse()->headers->set('X-XSS-Protection', '1; mode=block');
		$event->getResponse()->headers->set('X-Content-Type-Options', 'nosniff');
	}

}