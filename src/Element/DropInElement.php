<?php

namespace Drupal\braintree\Element;

use Drupal\Core\Render\Element\Container;

/**
 * Class DropInElement
 * @package Drupal\braintree\Element
 *
 * @RenderElement("braintree_dropin")
 */
class DropInElement extends Container
{
  public function getInfo() {
    $info = parent::getInfo();
    $info['#attached']['library'][] = 'braintree/braintree';
    $info['#attributes']['data-braintree-dropin'] = true;
    $info['#input'] = true;

    return $info;
  }
}
