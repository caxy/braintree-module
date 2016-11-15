<?php

namespace Drupal\braintree\Controller;

use Braintree\Result\Error;
use Braintree\Result\Successful;
use Braintree\Transaction;
use Braintree\TransactionGateway;
use Drupal\Core\Controller\ControllerBase;

/**
 * Class TransactionController.
 *
 * @package Drupal\braintree\Controller
 */
class TransactionController extends ControllerBase {

  private $transactionSuccessStatuses = [
    Transaction::AUTHORIZED,
    Transaction::AUTHORIZING,
    Transaction::SETTLED,
    Transaction::SETTLING,
    Transaction::SETTLEMENT_CONFIRMED,
    Transaction::SETTLEMENT_PENDING,
    Transaction::SUBMITTED_FOR_SETTLEMENT
  ];

  /**
   * Result.
   *
   * @param $id
   * @return string Return Hello string.
   * Return Hello string.
   */
  public function result($id) {
    /** @var Successful|Error $transaction */
    $transaction = \Drupal::keyValueExpirable('braintree')->get($id);

    $output = \Drupal::service('twig')->render('@braintree/transaction.html.twig', [
      'status' => in_array($transaction->status, $this->transactionSuccessStatuses),
      'transaction' => $transaction,
    ]);

    return [
      '#type' => 'markup',
      '#markup' => $output,
    ];
  }

  /**
   * @param $id
   * @return string
   */
  public function resultTitle($id)
  {
    $transaction = \Drupal::keyValueExpirable('braintree')->get($id);
    if (in_array($transaction->status, $this->transactionSuccessStatuses)) {
      return 'Success';
    } else {
      return 'Transaction Failed';
    }
  }
}
