# privatbank-autoclient
Autoclient PrivatBank low-level API implementation

Class for working with account statement PrivatBank (via autoclient)

Класс для получения онлайн выписки по расчетному счету в ПриватБанке. Выписка выгружается через авто-клиент. Как создать клиента - ссылки с описанием ниже.


PrivatBank API:

https://docs.google.com/document/d/e/2PACX-1vTion-fu1RzMCQgZXOYKKWAmvi-QAAxZ7AKnAZESGY5lF2j3nX61RBsa5kXzpu7t5gacl6TgztonrIE/pub

https://docs.google.com/document/d/e/2PACX-1vTtKvGa3P4E-lDqLg3bHRF6Wi9S7GIjSMFEFxII5qQZBGxuTXs25hQNiUU1hMZQhOyx6BNvIZ1bVKSr/pub
 
 
 
## Installation

Install using composer:

```bash
composer require kagatan/privatbank-autoclient  
```

## Usage

```php

<?php 

use Kagatan\PrivatbankAutoClient\ClientAPI;

$id = '0a550a93-XXX-XXXX-XXXX-1f345gtty56ac53';
$token = 'your_token';

// bank account
$acc = '123546788'; 

//  time ts (~ previous 3 day)
$startDate = time() - 3600 * 24 * 3;

//  time ts
$endDate = time();

$client = new ClientAPI($id, $token);

// Get lastday transactions
$transactions = $Client->getLastdayTransactions($acc); 
var_dump($transactions);

// Get today transactions
$transactions = $Client->getTodayTransactions(acc);
var_dump($transactions);

// Get previous transactions
$transactions = $client->getPreviousTransactions($acc, $startDate, $endDate);
var_dump($transactions);

foreach ($transactions AS $transaction) {

    $transaction = array_shift($transaction);

    // Если платеж проведен и нашли инвойс
    if (isset($transaction['BPL_PR_PR']) AND $transaction['BPL_PR_PR'] == 'r') {
     //
    }
    
}
```
