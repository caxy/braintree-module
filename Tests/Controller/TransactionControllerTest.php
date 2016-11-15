<?php

namespace Drupal\braintree\Tests;

use Drupal\simpletest\WebTestBase;

/**
 * Provides automated tests for the braintree module.
 */
class TransactionControllerTest extends WebTestBase {
  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return array(
      'name' => "braintree TransactionController's controller functionality",
      'description' => 'Test Unit for module braintree and controller TransactionController.',
      'group' => 'Other',
    );
  }

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();
  }

  /**
   * Tests braintree functionality.
   */
  public function testTransactionController() {
    // Check that the basic functions of module braintree.
    $this->assertEquals(TRUE, TRUE, 'Test Unit Generated via App Console.');
  }

}
