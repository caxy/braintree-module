<?php

namespace Drupal\braintree\Form;

use Braintree\Result\Error;
use Braintree\Result\Successful;
use Braintree\TransactionGateway;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class DefaultForm.
 *
 * @package Drupal\braintree\Form
 */
class DefaultForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'braintree_default_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['nonce'] = [
      '#type' => 'braintree_custom',
    ];
    $form['cardholder_name'] = [
      '#title' => 'Cardholder name',
      '#type' => 'textfield',
      '#attributes' => [
        'name' => false,
        'autocomplete' => 'cc-name',
        'data-braintree-name' => 'cardholder_name',
      ],
    ];
    $form['billing_address']['postal_code'] = [
      '#title' => 'Postal code',
      '#type' => 'textfield',
      '#attributes' => [
        'name' => false,
        'autocomplete' => 'postal-code',
        'data-braintree-name' => 'postal_code',
      ],
    ];
    $form['amount'] = [
      '#title' => 'Amount',
      '#type' => 'number',
      '#default_value' => 100,
    ];
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => 'Submit',
      '#attributes' => [
        'data-braintree-submit' => true,
      ],
    ];

    return $form;
  }

  public function validateForm(array &$form, FormStateInterface $form_state) {
    $amount = $form_state->getValue('amount');
    $nonce = $form_state->getValue('nonce');

    /** @var TransactionGateway $gateway */
    $gateway = \Drupal::service('braintree.gateway.transaction');

    /** @var Successful|Error $result */
    $result = $gateway->sale([
      'amount' => $amount,
      'paymentMethodNonce' => $nonce,
      'options' => [
        'submitForSettlement' => true
      ]
    ]);

    if ($result->success || !is_null($result->transaction)) {
      $transaction = $result->transaction;
      $form_state->set('transaction', $transaction);
    } else {
      $errorString = "";

      foreach ($result->errors->deepAll() as $error) {
        $errorString .= 'Error: ' . $error->code . ": " . $error->message . "\n";
      }

      $form_state->setError($form['nonce'], $errorString);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $transaction = $form_state->get('transaction');
    \Drupal::keyValueExpirable('braintree')->setWithExpire($transaction->id, $transaction, 24 * 60 * 60);
    $form_state->setRedirect('braintree.transaction.result', ['id' => $transaction->id ]);
  }
}
