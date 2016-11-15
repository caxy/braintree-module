/* globals drupalSettings, braintree, jQuery, Drupal, cardValidator */
(function ($, window, Drupal, drupalSettings, braintree, cardValidator) {
  'use strict';

  var instance;

  Drupal.behaviors.braintreeDropin = {
    attach: function (context, settings) {
      $('[data-braintree-dropin]', context).each(function () {
        var id = $(this).attr('id');
        var options = {
          container: id,
          onReady: function (integration) {
            instance = integration;
          }
        };

        braintree.setup(settings.braintree.clientToken, 'dropin', options);
      });
    },
    detach: function (context, settings, trigger) {
      if (trigger === 'unload') {
        instance.teardown(function () {
          instance = null;
        });
      }
    }
  };

  Drupal.behaviors.braintreeCustom = {
    attach: function (context, settings) {
      $('[data-braintree-custom]', context).each(function () {
        var id = $(this).attr('id');
        var $nonce = $('[data-braintree-payment-method-nonce]', this);
        var $form = $(this);

        var options = {
          id: id,
          enableCORS: true,
          onReady: function (integration) {
            instance = integration;
          },
          onPaymentMethodReceived: function (payload) {
            $nonce.val(payload.nonce);
            $form.trigger('submit');
          },
          paypal: {
            onSuccess: function (nonce, email, shippingAddress) {
              // Show in UI that the PayPal payment method is selected.
            },
            headless: true
          }
        };

        $('[data-braintree-paypal]', this).on('click', function (event) {
          event.preventDefault();
          instance.paypal.initAuthFlow();
        });

        braintree.setup(settings.braintree.clientToken, 'custom', options);
      });
    },
    detach: function (context, settings, trigger) {
      if (trigger === 'unload') {
        instance.teardown(function () {
          instance = null;
        });
      }
    }
  };

  Drupal.behaviors.braintreeNumberValidate = {
    attach: function (context, settings) {
      var $icon = $('i[data-braintree-cc-icon]', context);
      var cardTypeClass = 'fa-credit-card';
      var classes = {
        'american-express': 'fa-cc-amex',
        'visa': 'fa-cc-visa',
        'master-card': 'fa-cc-mastercard',
        'diners-club': 'fa-diners-club',
        'discover': 'fa-discover',
        'jcb': 'fa-jcb',
        'unionpay': 'fa-credit-card',
        'mestro': 'fa-maestro'
      };
      var defaultNumberAttrs = {
        size: 19,
        minlength: null,
        maxlength: null,
        pattern: null
      };
      var defaultCvvAttrs = {
        size: 4,
        minlength: 4,
        maxlength: 4
      };

      var $cvv = $('[data-braintree-name="cvv"]', context).attr(defaultCvvAttrs);
      var $number = $('[data-braintree-name="number"]', context).attr(defaultNumberAttrs);

      $number.on('input', function (event) {
        var validation = cardValidator.number($(this).val());

        if (cardTypeClass) {
          $icon.removeClass(cardTypeClass);
        }
        if (validation.card && classes.hasOwnProperty(validation.card.type)) {
          cardTypeClass = classes[validation.card.type];
        }
        else {
          cardTypeClass = 'fa-credit-card';
        }
        $icon.addClass(cardTypeClass);

        if (validation.card) {
          $(this).attr({
            pattern: validation.card.pattern,
            size: Math.max.apply(null, validation.card.lengths),
            maxlength: Math.max.apply(null, validation.card.lengths),
            minlength: Math.min.apply(null, validation.card.lengths)
          });

          $cvv.attr({
            size: validation.card.code.size,
            minlength: validation.card.code.size,
            maxlength: validation.card.code.size
          });

          // TODO: Make this less fragile.
          $cvv.siblings('.field-description').text(validation.card.code.name);
        }
        else {
          $(this).attr(defaultNumberAttrs);
          $cvv.attr(defaultCvvAttrs);
        }
      });
    }
  };

})(jQuery, window, Drupal, drupalSettings, braintree, cardValidator);

