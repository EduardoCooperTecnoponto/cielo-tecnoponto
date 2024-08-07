# API-3.0-PHP

SDK API-3.0 PHP

## Pacote forkado do pacote ciareis/cielo-api-3.0-php

Originalmente este pacote é derivado do pacote ciareis/cielo-api-3.0-php com adição de novos recursos.

## Principais recursos

* [x] Pagamentos por cartão de crédito.
* [x] Pagamentos recorrentes.
    * [x] Com autorização na primeira recorrência.
    * [x] Com autorização a partir da primeira recorrência.
    * [x] Atualização de dados do pagamento
    * [x] Alteração do dia de pagamento
    * [x] Alteração do valor da recorrência
* [x] Pagamentos por cartão de débito.
* [x] Pagamentos por boleto.
* [x] Pagamentos por pix.
* [x] Pagamentos por transferência eletrônica.
* [x] Cancelamento de autorização.
* [x] Consulta de pagamentos.
* [x] Tokenização de cartão.

## Limitações

Por envolver a interface de usuário da aplicação, o SDK funciona apenas como um framework para criação das transações. Nos casos onde a autorização é direta, não há limitação; mas nos casos onde é necessário a autenticação ou qualquer tipo de redirecionamento do usuário, o desenvolvedor deverá utilizar o SDK para gerar o pagamento e, com o link retornado pela Cielo, providenciar o redirecionamento do usuário.

## Dependências

* PHP >= 5.6

## Instalando o SDK
```
composer require "tecnoponto/cielo"
```

## Produtos e Bandeiras suportadas e suas constantes

```php
<?php
require 'vendor/autoload.php';

use Tecnoponto\Cielo\API30\Ecommerce\CreditCard;
```

| Bandeira         | Constante              | Crédito à vista | Crédito parcelado Loja | Débito | Voucher |
|------------------|------------------------|-----------------|------------------------|--------|---------|
| Visa             | CreditCard::VISA       | Sim             | Sim                    | Sim    | *Não*   |
| Master Card      | CreditCard::MASTERCARD | Sim             | Sim                    | Sim    | *Não*   |
| American Express | CreditCard::AMEX       | Sim             | Sim                    | *Não*  | *Não*   |
| Elo              | CreditCard::ELO        | Sim             | Sim                    | *Não*  | *Não*   |
| Diners Club      | CreditCard::DINERS     | Sim             | Sim                    | *Não*  | *Não*   |
| Discover         | CreditCard::DISCOVER   | Sim             | *Não*                  | *Não*  | *Não*   |
| JCB              | CreditCard::JCB        | Sim             | Sim                    | *Não*  | *Não*   |
| Aura             | CreditCard::AURA       | Sim             | Sim                    | *Não*  | *Não*   |

## Utilizando o SDK

Para criar um pagamento simples com cartão de crédito com o SDK, basta fazer:

### Criando um pagamento com cartão de crédito

```php
<?php
require 'vendor/autoload.php';

use Tecnoponto\Cielo\API30\Merchant;

use Tecnoponto\Cielo\API30\Ecommerce\Environment;
use Tecnoponto\Cielo\API30\Ecommerce\Sale;
use Tecnoponto\Cielo\API30\Ecommerce\CieloEcommerce;
use Tecnoponto\Cielo\API30\Ecommerce\Payment;
use Tecnoponto\Cielo\API30\Ecommerce\CreditCard;

use Tecnoponto\Cielo\API30\Ecommerce\Request\CieloRequestException;
// ...
// Configure o ambiente
$environment = $environment = Environment::sandbox();

// Configure seu merchant
$merchant = new Merchant('MERCHANT ID', 'MERCHANT KEY');

// Crie uma instância de Sale informando o ID do pedido na loja
$sale = new Sale('123');

// Crie uma instância de Customer informando o nome do cliente
$customer = $sale->customer('Fulano de Tal');

// Crie uma instância de Payment informando o valor do pagamento
$payment = $sale->payment(15700);

// Crie uma instância de Credit Card utilizando os dados de teste
// esses dados estão disponíveis no manual de integração
$payment->setType(Payment::PAYMENTTYPE_CREDITCARD)
        ->creditCard("123", CreditCard::VISA)
        ->setExpirationDate("12/2018")
        ->setCardNumber("0000000000000001")
        ->setHolder("Fulano de Tal");

// Crie o pagamento na Cielo
try {
    // Configure o SDK com seu merchant e o ambiente apropriado para criar a venda
    $sale = (new CieloEcommerce($merchant, $environment))->createSale($sale);

    // Com a venda criada na Cielo, já temos o ID do pagamento, TID e demais
    // dados retornados pela Cielo
    $paymentId = $sale->getPayment()->getPaymentId();

    // Com o ID do pagamento, podemos fazer sua captura, se ela não tiver sido capturada ainda
    $sale = (new CieloEcommerce($merchant, $environment))->captureSale($paymentId, 15700, 0);

    // E também podemos fazer seu cancelamento, se for o caso
    $sale = (new CieloEcommerce($merchant, $environment))->cancelSale($paymentId, 15700);
} catch (CieloRequestException $e) {
    // Em caso de erros de integração, podemos tratar o erro aqui.
    // os códigos de erro estão todos disponíveis no manual de integração.
    $error = $e->getCieloError();
}
// ...
```

### Criando um pagamento e gerando o token do cartão de crédito

```php
<?php
require 'vendor/autoload.php';

use Tecnoponto\Cielo\API30\Merchant;

use Tecnoponto\Cielo\API30\Ecommerce\Environment;
use Tecnoponto\Cielo\API30\Ecommerce\Sale;
use Tecnoponto\Cielo\API30\Ecommerce\CieloEcommerce;
use Tecnoponto\Cielo\API30\Ecommerce\Payment;
use Tecnoponto\Cielo\API30\Ecommerce\CreditCard;

use Tecnoponto\Cielo\API30\Ecommerce\Request\CieloRequestException;
// ...
// Configure o ambiente
$environment = $environment = Environment::sandbox();

// Configure seu merchant
$merchant = new Merchant('MERCHANT ID', 'MERCHANT KEY');

// Crie uma instância de Sale informando o ID do pedido na loja
$sale = new Sale('123');

// Crie uma instância de Customer informando o nome do cliente
$customer = $sale->customer('Fulano de Tal');

// Crie uma instância de Payment informando o valor do pagamento
$payment = $sale->payment(15700);

// Crie uma instância de Credit Card utilizando os dados de teste
// esses dados estão disponíveis no manual de integração.
// Utilize setSaveCard(true) para obter o token do cartão
$payment->setType(Payment::PAYMENTTYPE_CREDITCARD)
        ->creditCard("123", CreditCard::VISA)
        ->setExpirationDate("12/2018")
        ->setCardNumber("0000000000000001")
        ->setHolder("Fulano de Tal")
        ->setSaveCard(true);

// Crie o pagamento na Cielo
try {
    // Configure o SDK com seu merchant e o ambiente apropriado para criar a venda
    $sale = (new CieloEcommerce($merchant, $environment))->createSale($sale);

    // O token gerado pode ser armazenado em banco de dados para vendar futuras
    $token = $sale->getPayment()->getCreditCard()->getCardToken();
} catch (CieloRequestException $e) {
    // Em caso de erros de integração, podemos tratar o erro aqui.
    // os códigos de erro estão todos disponíveis no manual de integração.
    $error = $e->getCieloError();
}
// ...
```

### Criando um pagamento com cartão de crédito tokenizado

```php
<?php
require 'vendor/autoload.php';

use Tecnoponto\Cielo\API30\Merchant;

use Tecnoponto\Cielo\API30\Ecommerce\Environment;
use Tecnoponto\Cielo\API30\Ecommerce\Sale;
use Tecnoponto\Cielo\API30\Ecommerce\CieloEcommerce;
use Tecnoponto\Cielo\API30\Ecommerce\Payment;
use Tecnoponto\Cielo\API30\Ecommerce\CreditCard;

use Tecnoponto\Cielo\API30\Ecommerce\Request\CieloRequestException;
// ...
// Configure o ambiente
$environment = $environment = Environment::sandbox();

// Configure seu merchant
$merchant = new Merchant('MERCHANT ID', 'MERCHANT KEY');

// Crie uma instância de Sale informando o ID do pedido na loja
$sale = new Sale('123');

// Crie uma instância de Customer informando o nome do cliente
$customer = $sale->customer('Fulano de Tal');

// Crie uma instância de Payment informando o valor do pagamento
$payment = $sale->payment(15700);

// Crie uma instância de Credit Card utilizando os dados de teste
// esses dados estão disponíveis no manual de integração
$payment->setType(Payment::PAYMENTTYPE_CREDITCARD)
        ->creditCard("123", CreditCard::VISA)
        ->setCardToken("TOKEN-PREVIAMENTE-ARMAZENADO");

// Crie o pagamento na Cielo
try {
    // Configure o SDK com seu merchant e o ambiente apropriado para criar a venda
    $sale = (new CieloEcommerce($merchant, $environment))->createSale($sale);

    // Com a venda criada na Cielo, já temos o ID do pagamento, TID e demais
    // dados retornados pela Cielo
    $paymentId = $sale->getPayment()->getPaymentId();
} catch (CieloRequestException $e) {
    // Em caso de erros de integração, podemos tratar o erro aqui.
    // os códigos de erro estão todos disponíveis no manual de integração.
    $error = $e->getCieloError();
}
// ...
```

### Criando um pagamento recorrente

```php
<?php
require 'vendor/autoload.php';

use Tecnoponto\Cielo\API30\Merchant;

use Tecnoponto\Cielo\API30\Ecommerce\Environment;
use Tecnoponto\Cielo\API30\Ecommerce\Sale;
use Tecnoponto\Cielo\API30\Ecommerce\CieloEcommerce;
use Tecnoponto\Cielo\API30\Ecommerce\Payment;
use Tecnoponto\Cielo\API30\Ecommerce\CreditCard;

use Tecnoponto\Cielo\API30\Ecommerce\Request\CieloRequestException;
// ...
// ...
// Configure o ambiente
$environment = $environment = Environment::sandbox();

// Configure seu merchant
$merchant = new Merchant('MID', 'MKEY');

// Crie uma instância de Sale informando o ID do pedido na loja
$sale = new Sale('123');

// Crie uma instância de Customer informando o nome do cliente
$customer = $sale->customer('Fulano de Tal');

// Crie uma instância de Payment informando o valor do pagamento
$payment = $sale->payment(15700);

// Crie uma instância de Credit Card utilizando os dados de teste
// esses dados estão disponíveis no manual de integração
$payment->setType(Payment::PAYMENTTYPE_CREDITCARD)
        ->creditCard("123", CreditCard::VISA)
        ->setExpirationDate("12/2018")
        ->setCardNumber("0000000000000001")
        ->setHolder("Fulano de Tal");

// Configure o pagamento recorrente
$payment->recurrentPayment(true)->setInterval(RecurrentPayment::INTERVAL_MONTHLY);

// Crie o pagamento na Cielo
try {
    // Configure o SDK com seu merchant e o ambiente apropriado para criar a venda
    $sale = (new CieloEcommerce($merchant, $environment))->createSale($sale);

    $recurrentPaymentId = $sale->getPayment()->getRecurrentPayment()->getRecurrentPaymentId();
} catch (CieloRequestException $e) {
    // Em caso de erros de integração, podemos tratar o erro aqui.
    // os códigos de erro estão todos disponíveis no manual de integração.
    $error = $e->getCieloError();
}
// ...
```

### Consultando uma recorrencia

```php
<?php

require 'vendor/autoload.php';

use Tecnoponto\Cielo\API30\Merchant;

use Tecnoponto\Cielo\API30\Ecommerce\Environment;
use Tecnoponto\Cielo\API30\Ecommerce\CreditCard;
use Tecnoponto\Cielo\API30\Ecommerce\CieloEcommerce;

use Tecnoponto\Cielo\API30\Ecommerce\Request\CieloRequestException;

// ...
// ...
// Configure o ambiente
$environment = Environment::sandbox();

// Configure seu merchant
$merchant = new Merchant('MID', 'MKEY');

try {
    $recurrency = (new CieloEcommerce($merchant, $environment))->getRecurrentPayment('recurrency id');
} catch (CieloRequestException $e) {
    // Em caso de erros de integração, podemos tratar o erro aqui.
    // os códigos de erro estão todos disponíveis no manual de integração.
    $error = $e->getCieloError();
}
// ...
```

### Atualizando dados de pagamento de uma recorrencia

```php
<?php
require 'vendor/autoload.php';

use Tecnoponto\Cielo\API30\Merchant;

use Tecnoponto\Cielo\API30\Ecommerce\Environment;
use Tecnoponto\Cielo\API30\Ecommerce\Sale;
use Tecnoponto\Cielo\API30\Ecommerce\CieloEcommerce;
use Tecnoponto\Cielo\API30\Ecommerce\Payment;
use Tecnoponto\Cielo\API30\Ecommerce\CreditCard;

use Tecnoponto\Cielo\API30\Ecommerce\Request\CieloRequestException;
// ...
// Configure o ambiente
$environment = $environment = Environment::sandbox();

// Configure seu merchant
$merchant = new Merchant('MERCHANT ID', 'MERCHANT KEY');

// Crie uma instância de Sale informando o ID do da recorrencia
// Id devolvido por: $sale->getPayment()->getRecurrentPayment()->getRecurrentPaymentId();
$sale = new Sale('123');

// Crie uma instância de Customer informando o nome do cliente
$customer = $sale->customer('Fulano de Tal');

// Crie uma instância de Payment informando o valor do pagamento
$payment = $sale->payment(15700);

// Crie uma instância de Credit Card utilizando os dados de teste
// esses dados estão disponíveis no manual de integração.
// Utilize setSaveCard(true) para obter o token do cartão
$payment->setType(Payment::PAYMENTTYPE_CREDITCARD)
        ->creditCard("123", CreditCard::VISA)
        ->setExpirationDate("12/2018")
        ->setCardNumber("0000000000000001")
        ->setHolder("Fulano de Tal");

// Crie o pagamento na Cielo
try {
    // Configure o SDK com seu merchant e o ambiente apropriado para criar a venda
    (new CieloEcommerce($merchant, $environment))->updateRecurrentPayment($sale);
    // não retorna nada se for bem sucedido
    return true
} catch (CieloRequestException $e) {
    // Em caso de erros de integração, podemos tratar o erro aqui.
    // os códigos de erro estão todos disponíveis no manual de integração.
    $error = $e->getCieloError();
}
```

### Ativando/Desativando uma recorrência

```php
<?php

require 'vendor/autoload.php';

use Tecnoponto\Cielo\API30\Merchant;

use Tecnoponto\Cielo\API30\Ecommerce\Environment;
use Tecnoponto\Cielo\API30\Ecommerce\CreditCard;
use Tecnoponto\Cielo\API30\Ecommerce\CieloEcommerce;

use Tecnoponto\Cielo\API30\Ecommerce\Request\CieloRequestException;

// ...
// ...
// Configure o ambiente
$environment = Environment::sandbox();

// Configure seu merchant
$merchant = new Merchant('MID', 'MKEY');

try {
    // Desativa uma recorrência
    $recurrency = (new CieloEcommerce($merchant, $environment))->deactivateRecurrentPayment('recurrency id');
    // Reativa uma recorrência
    $recurrency = (new CieloEcommerce($merchant, $environment))->reactivateRecurrentPayment('recurrency id');
} catch (CieloRequestException $e) {
    // Em caso de erros de integração, podemos tratar o erro aqui.
    // os códigos de erro estão todos disponíveis no manual de integração.
    $error = $e->getCieloError();
}
// ...
```

### Alterar dia/valor de uma recorrência

```php
<?php

require 'vendor/autoload.php';

use Tecnoponto\Cielo\API30\Merchant;

use Tecnoponto\Cielo\API30\Ecommerce\Environment;
use Tecnoponto\Cielo\API30\Ecommerce\CreditCard;
use Tecnoponto\Cielo\API30\Ecommerce\CieloEcommerce;

use Tecnoponto\Cielo\API30\Ecommerce\Request\CieloRequestException;

// ...
// ...
// Configure o ambiente
$environment = Environment::sandbox();

// Configure seu merchant
$merchant = new Merchant('MID', 'MKEY');

try {
    // Altera o dia da recorrência
    $recurrency = (new CieloEcommerce($merchant, $environment))->changeDayRecurrentPayment('recurrency id', 10);
    // Altera o valor da recorrência
    $recurrency = (new CieloEcommerce($merchant, $environment))->changeAmountRecurrentPayment('recurrency id', 1500);
} catch (CieloRequestException $e) {
    // Em caso de erros de integração, podemos tratar o erro aqui.
    // os códigos de erro estão todos disponíveis no manual de integração.
    $error = $e->getCieloError();
}
// ...
```

### Atualizando dados de pagamento de uma recorrencia

```php
<?php
require 'vendor/autoload.php';

use Tecnoponto\Cielo\API30\Merchant;

use Tecnoponto\Cielo\API30\Ecommerce\Environment;
use Tecnoponto\Cielo\API30\Ecommerce\Sale;
use Tecnoponto\Cielo\API30\Ecommerce\CieloEcommerce;
use Tecnoponto\Cielo\API30\Ecommerce\Payment;
use Tecnoponto\Cielo\API30\Ecommerce\CreditCard;

use Tecnoponto\Cielo\API30\Ecommerce\Request\CieloRequestException;
// ...
// Configure o ambiente
$environment = $environment = Environment::sandbox();

// Configure seu merchant
$merchant = new Merchant('MERCHANT ID', 'MERCHANT KEY');

// Crie uma instância de Sale informando o ID do da recorrencia
// Id devolvido por: $sale->getPayment()->getRecurrentPayment()->getRecurrentPaymentId();
$sale = new Sale('123');

// Crie uma instância de Customer informando o nome do cliente
$customer = $sale->customer('Fulano de Tal');

// Crie uma instância de Payment informando o valor do pagamento
$payment = $sale->payment(15700);

// Crie uma instância de Credit Card utilizando os dados de teste
// esses dados estão disponíveis no manual de integração.
// Utilize setSaveCard(true) para obter o token do cartão
$payment->setType(Payment::PAYMENTTYPE_CREDITCARD)
        ->creditCard("123", CreditCard::VISA)
        ->setExpirationDate("12/2018")
        ->setCardNumber("0000000000000001")
        ->setHolder("Fulano de Tal");

// Crie o pagamento na Cielo
try {
    // Configure o SDK com seu merchant e o ambiente apropriado para criar a venda
    $sale = (new CieloEcommerce($merchant, $environment))->updateRecurrentPayment($sale);
} catch (CieloRequestException $e) {
    // Em caso de erros de integração, podemos tratar o erro aqui.
    // os códigos de erro estão todos disponíveis no manual de integração.
    $error = $e->getCieloError();
}
```

### Criando transações com cartão de débito

```php
<?php
require 'vendor/autoload.php';

use Tecnoponto\Cielo\API30\Merchant;

use Tecnoponto\Cielo\API30\Ecommerce\Environment;
use Tecnoponto\Cielo\API30\Ecommerce\Sale;
use Tecnoponto\Cielo\API30\Ecommerce\CieloEcommerce;
use Tecnoponto\Cielo\API30\Ecommerce\CreditCard;

use Tecnoponto\Cielo\API30\Ecommerce\Request\CieloRequestException;

// ...
// Configure o ambiente
$environment = $environment = Environment::sandbox();

// Configure seu merchant
$merchant = new Merchant('MERCHANT ID', 'MERCHANT KEY');

// Crie uma instância de Sale informando o ID do pedido na loja
$sale = new Sale('123');

// Crie uma instância de Customer informando o nome do cliente
$customer = $sale->customer('Fulano de Tal');

// Crie uma instância de Payment informando o valor do pagamento
$payment = $sale->payment(15700);

// Defina a URL de retorno para que o cliente possa voltar para a loja
// após a autenticação do cartão
$payment->setReturnUrl('https://localhost/test');

// Crie uma instância de Debit Card utilizando os dados de teste
// esses dados estão disponíveis no manual de integração
$payment->debitCard("123", CreditCard::VISA)
        ->setExpirationDate("12/2018")
        ->setCardNumber("0000000000000001")
        ->setHolder("Fulano de Tal");

// Crie o pagamento na Cielo
try {
    // Configure o SDK com seu merchant e o ambiente apropriado para criar a venda
    $sale = (new CieloEcommerce($merchant, $environment))->createSale($sale);

    // Com a venda criada na Cielo, já temos o ID do pagamento, TID e demais
    // dados retornados pela Cielo
    $paymentId = $sale->getPayment()->getPaymentId();

    // Utilize a URL de autenticação para redirecionar o cliente ao ambiente
    // de autenticação do emissor do cartão
    $authenticationUrl = $sale->getPayment()->getAuthenticationUrl();
} catch (CieloRequestException $e) {
    // Em caso de erros de integração, podemos tratar o erro aqui.
    // os códigos de erro estão todos disponíveis no manual de integração.
    $error = $e->getCieloError();
}
// ...
```

### Criando uma venda com Boleto

```php
<?php
require 'vendor/autoload.php';

use Tecnoponto\Cielo\API30\Merchant;

use Tecnoponto\Cielo\API30\Ecommerce\Environment;
use Tecnoponto\Cielo\API30\Ecommerce\Sale;
use Tecnoponto\Cielo\API30\Ecommerce\CieloEcommerce;
use Tecnoponto\Cielo\API30\Ecommerce\Payment;

use Tecnoponto\Cielo\API30\Ecommerce\Request\CieloRequestException;
// ...
// Configure o ambiente
$environment = $environment = Environment::sandbox();

// Configure seu merchant
$merchant = new Merchant('MERCHANT ID', 'MERCHANT KEY');

// Crie uma instância de Sale informando o ID do pedido na loja
$sale = new Sale('123');

// Crie uma instância de Customer informando o nome do cliente,
// documento e seu endereço
$customer = $sale->customer('Fulano de Tal')
                  ->setIdentity('00000000001')
                  ->setIdentityType('CPF')
                  ->address()->setZipCode('22750012')
                             ->setCountry('BRA')
                             ->setState('RJ')
                             ->setCity('Rio de Janeiro')
                             ->setDistrict('Centro')
                             ->setStreet('Av Marechal Camara')
                             ->setNumber('123');

// Crie uma instância de Payment informando o valor do pagamento
$payment = $sale->payment(15700)
                ->setType(Payment::PAYMENTTYPE_BOLETO)
                ->setAddress('Rua de Teste')
                ->setBoletoNumber('1234')
                ->setAssignor('Empresa de Teste')
                ->setDemonstrative('Desmonstrative Teste')
                ->setExpirationDate(date('d/m/Y', strtotime('+1 month')))
                ->setIdentification('11884926754')
                ->setInstructions('Esse é um boleto de exemplo');

// Crie o pagamento na Cielo
try {
    // Configure o SDK com seu merchant e o ambiente apropriado para criar a venda
    $sale = (new CieloEcommerce($merchant, $environment))->createSale($sale);

    // Com a venda criada na Cielo, já temos o ID do pagamento, TID e demais
    // dados retornados pela Cielo
    $paymentId = $sale->getPayment()->getPaymentId();
    $boletoURL = $sale->getPayment()->getUrl();

    printf("URL Boleto: %s\n", $boletoURL);
} catch (CieloRequestException $e) {
    // Em caso de erros de integração, podemos tratar o erro aqui.
    // os códigos de erro estão todos disponíveis no manual de integração.
    $error = $e->getCieloError();
}
```

### Tokenizando um cartão

```php
<?php

require 'vendor/autoload.php';

use Tecnoponto\Cielo\API30\Merchant;

use Tecnoponto\Cielo\API30\Ecommerce\Environment;
use Tecnoponto\Cielo\API30\Ecommerce\CreditCard;
use Tecnoponto\Cielo\API30\Ecommerce\CieloEcommerce;

use Tecnoponto\Cielo\API30\Ecommerce\Request\CieloRequestException;

// ...
// ...
// Configure o ambiente
$environment = Environment::sandbox();

// Configure seu merchant
$merchant = new Merchant('MID', 'MKEY');

// Crie uma instância do objeto que irá retornar o token do cartão 
$card = new CreditCard();
$card->setCustomerName('Fulano de Tal');
$card->setCardNumber('0000000000000001');
$card->setHolder('Fulano de Tal');
$card->setExpirationDate('10/2020');
$card->setBrand(CreditCard::VISA);

try {
    // Configure o SDK com seu merchant e o ambiente apropriado para recuperar o cartão
    $card = (new CieloEcommerce($merchant, $environment))->tokenizeCard($card);

    // Get the token
    $cardToken = $card->getCardToken();
} catch (CieloRequestException $e) {
    // Em caso de erros de integração, podemos tratar o erro aqui.
    // os códigos de erro estão todos disponíveis no manual de integração.
    $error = $e->getCieloError();
}
// ...
```

### Criando um pagamento na modalidade Pix:

```php
<?php
$sale->customer('Nome do cliente')
    ->setIdentity('1234567898745')
    ->setIdentityType('CNPJ');
        
$sale->payment(158900)
    ->setType(Payment::PAYMENTTYPE_PIX)
    ->setAmount(158900);

try {
    $sale = (new CieloEcommerce($this->merchant, $this->environment))->createSale($sale);

    $paymentId = $sale->getPayment()->getPaymentId();
    $pix_qrcode = $sale->getPayment()->getQrcodeString();
    $nsu_pix = $sale->getProofOfSale();

    if (isset($paymentId)) {
        return true;
    } else {
        return false;
    }

} catch (CieloRequestException $e) {
    $error = $e->getCieloError();
    return ApiResponseClass::sendResponseInternal(false, $e->getMessage());
}  
// ...
```

## Manual

Para mais informações sobre a integração com a API 3.0 da Cielo, vide o manual em: [Integração API 3.0](https://developercielo.github.io/manual/cielo-ecommerce)


## Créditos

Ao autor original do pacote [developercielo/api-3.0-php](https://github.com/DeveloperCielo/API-3.0-PHP)

