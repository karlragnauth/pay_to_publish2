<?php

namespace Drupal\commerce_license_pay_to_publish;

use Drupal\commerce\AvailabilityManagerInterface;
use Drupal\commerce\Context;
use Drupal\commerce_order\Entity\OrderInterface;
use Drupal\commerce_license\LicenseOrderProcessorMultiples as BaseSubscriber;

/**
 * Decorates Commerce License Order Processor, overriding quantity 
 * restrictions for pay_to_publish order item types.
 *
 * Order processor that ensures only 1 of each license may be added to the cart.
 *
 * This is an order processor rather than an availability checker, as
 * \Drupal\commerce_order\AvailabilityOrderProcessor::check() removes the
 * entire order item if availability fails, whereas we only want to keep the
 * quantity at 1.
 *
 * @todo: Figure out if this is still necessary or if the cart event
 * subscriber covers all cases.
 *
 * @see \Drupal\commerce_license\EventSubscriber\LicenseMultiplesCartEventSubscriber
 */
class PayToPublishOrderProcessorMultiples extends BaseSubscriber
{

    /**
     * {@inheritdoc}
     */
    public function process(OrderInterface $order)
    {
        foreach ($order->getItems() as $order_item) {
            // Skip order items that do not have a license reference field.
            // Or if the order item type is a pay_to_publish order item type.
            if (!$order_item->hasField('license') || $order_item->bundle() == 'pay_to_publish') {
                continue;
            }

            $quantity = $order_item->getQuantity();
            if ($quantity > 1) {
                // Force the quantity back to 1.
                $order_item->setQuantity(1);

                $purchased_entity = $order_item->getPurchasedEntity();
                if ($purchased_entity) {
                    // Note that this message shows both when attempting to increase the
                    // quantity of a license product already in the cart, and when
                    // attempting to put more than 1 of a license product into the cart.
                    // In the latter case, the message isn't as clear as it could be, but
                    // site builders should be hiding the quantity field from the add to
                    // cart form for license products, so this is moot.
                    drupal_set_message(
                        t(
                            "You may only have one of @product-label in your cart.", [
                            '@product-label' => $purchased_entity->label(),
                            ]
                        ), 'error'
                    );
                }
            }
        }

        return;
    }
}

