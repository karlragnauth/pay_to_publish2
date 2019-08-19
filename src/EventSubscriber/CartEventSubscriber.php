<?php

namespace Drupal\commerce_license_pay_to_publish\EventSubscriber;

use Drupal\Core\Url;
use Drupal\commerce_cart\Event\CartEntityAddEvent;
use Drupal\commerce_cart\EventSubscriber\CartEventSubscriber as BaseSubscriber;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Modifies the item added to cart message for pay_to_publish content.
 */
class CartEventSubscriber extends BaseSubscriber implements EventSubscriberInterface
{

    /**
     * {@inheritDoc}
     */
    public function displayAddToCartMessage(CartEntityAddEvent $event)
    {
        $label = $event->getEntity()->label();
        $bundle = $event->getEntity()->bundle();

        // Modify message for pay_to_publish order_items to make more readable
        if ($bundle == 'pay_to_publish') {
            $label = "Listing option for $label";
        }
        $this->messenger->addMessage(
            $this->t(
                '@entity added to <a href=":url">your cart</a>.', [
                '@entity' => $label,
                ':url' => Url::fromRoute('commerce_cart.page')->toString(),
                ]
            )
        );
    }

}
