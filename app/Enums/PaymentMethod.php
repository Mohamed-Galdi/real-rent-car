<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case CREDIT_CARD = 'credit_card';
    case DEBIT_CARD = 'debit_card';
    case PAYPAL = 'paypal';
    case STRIPE = 'stripe';
    case BANK_TRANSFER = 'bank_transfer';
    case CASH = 'cash';
}
