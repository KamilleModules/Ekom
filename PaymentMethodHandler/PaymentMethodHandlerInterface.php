<?php


namespace Module\Ekom\PaymentMethodHandler;


/**
 * @todo-ling: deprecated in favor of PaymentMethodConfig?
 */
interface PaymentMethodHandlerInterface
{
    /**
     * @return array, the paymentMethodBlock model
     *
     * The model structure depends on the concrete class;
     * however it must contain at least the following keys:
     *
     * - label:
     * - type: the type of the payment block
     * - ?panel: the model for a configuration panel if any
     *
     *
     * If the options argument is passed, it configures the payment block model.
     * For instance, in a block model which offers a choice between multiple credit cards,
     * the options can help selecting one credit card in particular.
     *
     *
     */
    public function getPaymentMethodBlockModel(array $options = []);

    /**
     * Return the selected item.
     *
     * Context:
     * A blockModel is like a container for any number of items.
     * The user expand a block model and configures it (if it has options).
     *
     * Then she submits the form.
     *
     * The item is just the item configured by the user: it's model has the same type
     * and label as the block model, but instead of the panel key, we have the expanded
     * version of the options that the user took.
     *
     * - label
     * - type
     * - img: uri to icon to display the summary of the item
     * - ... (various keys, depending on the options chosen by the user)
     *
     *
     *
     */
    public function getPaymentMethodItem(array $options = []);
}