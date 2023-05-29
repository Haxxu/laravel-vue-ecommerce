<?php

/**
 * User:
 */

namespace App\Enums;

/**
 * Class AdressType
 *
 * @author  Zura Sekhniashvili <zurasekhniashvili@gmail.com>
 * @package App\Enums
 */
enum AddressType: string
{
    case Shipping = 'shipping';
    case Billing = 'billing';
}
