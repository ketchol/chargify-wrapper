<?php

namespace Invigor\Chargify\Models;

use Invigor\Chargify\Controllers\ProductController;
use Invigor\Chargify\Controllers\SubscriptionController;

/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 23/10/2016
 * Time: 1:10 PM
 */

/**
 * A Read-Only object from Chargify, created from Payment
 *
 * This class has almost everything from Payment.
 * After payment is created, payment will be converted to a transaction.
 * Therefore, payment ID is same as transaction ID
 *
 * Please check
 * https://docs.chargify.com/api-transactions
 * for related documentation provided by Chargify
 *
 * Class Transaction
 * @package Invigor\Chargify\Models
 */
class Transaction
{
    public $id;
    public $transaction_type;
    public $amount_in_cents;
    public $created_at;
    public $starting_balance_in_cents;
    public $ending_balance_in_cents;
    public $memo;
    public $subscription_id;
    public $product_id;
    public $success;
    public $payment_id;
    public $kind;
    public $gateway_transaction_id;
    public $gateway_order_id;

    private $subscriptionController;
    private $productController;

    public function __construct()
    {
        $this->subscriptionController = new SubscriptionController;
        $this->productController = new ProductController;
    }

    /**
     * Load the subscription this transaction is for
     *
     * @return Subscription|null
     */
    public function subscription()
    {
        return $this->subscriptionController->get($this->subscription_id);
    }

    /**
     * Load the product this transaction is for
     *
     * @return Product|null
     */
    public function product()
    {
        return $this->productController->get($this->product_id);
    }

    /**
     * This method is not used and not built
     */
    public function payment()
    {
        /*TODO Chargify provide payment ID but not interface to access payment*/
    }
}