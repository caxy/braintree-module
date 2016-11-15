<?php

namespace Drupal\braintree\Element;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element\FormElement;

/**
 * Class CustomElement
 * @package Drupal\braintree\Element
 * @FormElement("braintree_custom")
 */
class CustomElement extends FormElement
{

  /**
   * Returns the element properties for this element.
   *
   * @return array
   *   An array of element properties. See
   *   \Drupal\Core\Render\ElementInfoManagerInterface::getInfo() for
   *   documentation of the standard properties of all elements, and the
   *   return value format.
   */
  public function getInfo() {
    $class = get_class($this);
    return array(
      '#input' => TRUE,
      '#process' => array(
        array($class, 'processBraintreeCustom'),
      ),
      '#element_validate' => [
        [$class, 'validateNonce'],
      ]
    );
  }

  public static function validateNonce(&$element, FormStateInterface $form_state, &$complete_form) {
    $nonce = $element['nonce']['#value'];

    $form_state->setValueForElement($element, $nonce);
  }

  public static function processBraintreeCustom(&$element, FormStateInterface $form_state, &$complete_form) {
    $complete_form['#attributes']['data-braintree-custom'] = true;
    $complete_form['#attached']['library'][] = 'braintree/braintree';
    $element['nonce'] = [
      '#type' => 'hidden',
      '#attributes' => [
        'data-braintree-payment-method-nonce' => true,
      ]
    ];
    $element['paypal'] = [
      '#type' => 'html_tag',
      '#tag' => 'button',
      '#value' => 'Pay with PayPal',
      '#attributes' => [
        'type' => 'button',
        'data-braintree-paypal' => true,
      ],
    ];
    $element['number'] = [
      '#title' => 'CC Number',
      '#type' => 'textfield',
      '#attributes' => [
        'name' => false,
        'autocomplete' => 'cc-number',
        'data-braintree-name' => 'number',
      ],
      '#field_suffix' => '<i class="fa fa-2x fa-credit-card" aria-hidden="true" data-braintree-cc-icon></i>',
    ];
    $element['expiration_date'] = [
      '#title' => 'Expiration date',
      '#type' => 'textfield',
      '#attributes' => [
        'name' => false,
        'autocomplete' => 'cc-exp',
        'data-braintree-name' => 'expiration_date',
      ],
    ];
    $element['cvv'] = [
      '#title' => 'CVV',
      '#type' => 'textfield',
      '#attributes' => [
        'name' => false,
        'autocomplete' => 'cc-csc',
        'data-braintree-name' => 'cvv',
      ],
    ];

    return $element;
  }
}