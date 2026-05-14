<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case Cash = 'cash';
    case CreditCard = 'credit_card';
    case BankTransfer = 'bank_transfer';
    case Online = 'online';

    public function label(): string
    {
        return match ($this) {
            self::Cash => 'Nakit',
            self::CreditCard => 'Kredi Kartı',
            self::BankTransfer => 'Banka Transferi',
            self::Online => 'Online',
        };
    }
}
