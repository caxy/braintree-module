<?php

namespace Drupal\braintree\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'braintree_payment' widget.
 *
 * @FieldWidget(
 *   id = "braintree_payment",
 *   label = @Translation("Payment widget"),
 *   field_types = {
 *     "string"
 *   }
 * )
 */
class PaymentWidget extends WidgetBase {

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $element = [];

    $element['value'] = $element + array(
      '#type' => 'braintree_custom',
      '#default_value' => isset($items[$delta]->value) ? $items[$delta]->value : NULL,
    );

    return $element;
  }

}
