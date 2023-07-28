# Price Engine

[![Build Status](https://github.com/vaened/php-price-engine/actions/workflows/tests.yml/badge.svg)](https://github.com/vaened/php-price-engine/actions?query=workflow:Tests)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](license)

The Price Calculation Library is a tool that allows you to perform complex calculations for prices, taxes, discounts, and charges in your applications. This library is based on the `Brick\Money` library to ensure precise monetary calculations.

## Installation

You can install the library using `composer`.

```shell
composer require vaened/php-price-engine
```

## Usage

### Initializing the Cashier

To start using the Price Engine, create an instance of the any [Cashier](#cashiers) and provide an [`Amount`](./src/Money/Amount.php).

```php
$cashier = new SimpleCashier(
    Amount::taxable(
        Money::of(100, 'USD'),
        TaxCodes::any()
    ),
    quantity : 1,
    taxes    : Taxes::from([
        Tax\Inclusive::proporcional(21, TaxCode::IVA),
    ])
);
```

### Updating Quantity

You can update the quantity using the `update()` method, that will calculate the totals in the next summary.

```php
$cashier->update(10);
```

### Applying Discounts

To apply discounts, use the `apply()` method, that will calculate the totals in the next summary, and receive as parameter N amount
of [`Discount`](./src/Adjustments/Discount.php).

```php
$cashier->apply(
    Discount::proporcional(2)->named('NEW_USERS'),
    Discount::fixed(5)->named('PROMOTIONAL'),
);
```

### Adding Charges

To add charges, use the `add()` method, it will calculate the totals in the next summary, and received as parameter N amount
of [`Charge`](./src/Adjustments/Charge.php).

```php
$cashier->add(
    Charge::proporcional(5)->named('POS'),
    Charge::fixed(10)->named('DELIVERY')
);
```

### Obtaining Individual Totals

To obtain individual values of any price adjuster, you can use the `tax()` `charge()`, or `discount()` functions, all of which receive the code established during creation and return an instance of [`Adjustment`](./src/Adjustment.php)

```php
$cashier->taxes()->locate('IVA');
$cashier->charges()->locate('DELIVERY');
$cashier->discounts()->locate('NEW_USERS');
```

### Obtaining Totals

To obtain the total, you can use individual functions, each of which returns an instance of `Brick\Money`.

```php
$cashier->quantity();
$cashier->unitPrice();
$cashier->subtotal();
$cashier->taxes()->total();
$cashier->charges()->total();
$cashier->discounts()->total();
$cashier->total();
```

## Configuration
Currently there are 2 built-in ways to handle calculations

### Cashiers
- [**SimpleCashier**](./src/Cashiers/SimpleCashier.php): This `cashier` operates based on the gross price concept. It means that the price provided will be cleaned to get the final price without tax. Adjustments are applied directly to the gross price and taxes are calculated separately after adjustments are applied

- [**RegularCashier**](./src/Cashiers/RegularCashier.php):  This `cashier` operates based on the concept of unit price + taxes. It means that the configured taxes will be maintained or added to the indicated price, and discounts and charges will be applied to this price with taxes included.


> Choose the `cashier` that best suits your specific business needs and requirements. You can create your own cashier to fit any specific logic or rule related to your business. If you need to create additional cashiers or implement custom logic, you can do so by extending [`Cashier`](./src/Cashier.php). This library provides a flexible foundation to handle various pricing scenarios effectively.


### Amounts
There are 2 ways to create the amount.

- **Amount with applicable taxes**: these amounts are subject to taxes, whether they are `inclusive taxes` or `exclusive taxes` and can be defined as follows.
  ```php
  Amount::taxable(
      Money::of(100, 'PEN'),
      TaxCodes::only(['IGV'])
  );
  ```
  > The tax codes establish what taxes are applicable for the amount
  
  Allow All                 | Only Allowed                          |Allow Nothing
  --------------------------|---------------------------------------|--------------------------
  **TaxCodes**::***any()*** | **TaxCodes**::***only(['IGV', ...])***| **TaxCodes**::***none()*** 

- **Amounts without applicable taxes**: These amounts are not subject to any tax and will not have taxes applied.

  ```php
  Amount::taxexempt(
      Money::of(10, 'PEN')
  );
  ```
  > These would be the same as creating a taxable amount but passing **TaxCodes**::***none()*** as the allowed codes.

### Taxes
Taxes can be established in two ways.

- **Inclusive**: Taxes included in the unit price, and will be cleared during calculations
  ```php
  use Vaened\PriceEngine\Adjustments\Tax;
  
  $amount->impose([
    Tax\Inclusive::proporcional(18, 'IGV'); // 18%
    Tax\Inclusive::fixed(2, 'ISC'); // 2 PEN
  ]);
  // or
  $cashier = new RegularCashier(
    ...
    taxes : Taxes::from([
      Tax\Inclusive::proporcional(18, 'IGV'); // 18%
      Tax\Inclusive::fixed(2, 'ISC'); // 2 PEN
    ])
  );
  ```
- **Exclusive**: Taxes not included in the unit price, and will be added for the final calculations.
  ```php
  use Vaened\PriceEngine\Adjustments\Tax;

  $amount->impose([
    Tax\Exclusive::proporcional(18, 'IGV'); // 18%
    Tax\Exclusive::fixed(2, 'ISC'); // 2 PEN
  ]);
  // or
  $cashier = new RegularCashier(
    ...
    taxes : Taxes::from([
      Tax\Exclusive::proporcional(18, 'IGV'); // 18%
      Tax\Exclusive::fixed(2, 'ISC'); // 2 PEN
    ])
  );
  ```

## License
This library is licensed under the MIT License. For more information, please see the [`license`](./license) file.