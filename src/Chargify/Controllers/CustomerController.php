<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 10/24/2016
 * Time: 10:31 AM
 */

namespace Invigor\Chargify\Controllers;


use Illuminate\Support\Facades\Cache;
use Invigor\Chargify\Models\Customer;
use Invigor\Chargify\Traits\Curl;

class CustomerController
{
    use Curl;

    public function create()
    {

    }

    public function all()
    {
        if (config('chargify.caching.enable') == true) {
            return Cache::remember("chargify.customers", config('chargify.caching.ttl'), function () {
                return $this->__all();
            });
        } else {
            return $this->__all();
        }
    }

    public function get($customer_id)
    {
        if (config('chargify.caching.enable') == true) {
            return Cache::remember("chargify.customers.{$customer_id}", config('chargify.caching.ttl'), function () use ($customer_id) {
                return $this->__get($customer_id);
            });
        } else {
            return $this->__get($customer_id);
        }
    }

    public function getByReference($reference)
    {
        if (config('chargify.caching.enable') == true) {
            return Cache::remember("chargify.customers.lookup.reference.{$reference}", config('chargify.caching.ttl'), function () use ($reference) {
                return $this->__getByReference($reference);
            });
        } else {
            return $this->__getByReference($reference);
        }
    }

    public function getByQuery($query)
    {
        if (config('chargify.caching.enable') == true) {
            return Cache::remember("chargify.customers.query.{$query}", config('chargify.caching.ttl'), function () use ($query) {
                return $this->__getByQuery($query);
            });
        } else {
            return $this->__getByQuery($query);
        }
    }

    private function __all()
    {
        $url = config('chargify.api_domain') . "customers.json";
        $customers = $this->_get($url);
        if (is_array($customers)) {
            $customers = array_pluck($customers, 'customer');
            $output = array();
            foreach ($customers as $customer) {
                $output[] = $this->__assign($customer);
            }
            return $output;
        } else {
            return $customers;
        }
    }

    private function __get($customer_id)
    {
        $url = config('chargify.api_domain') . "customers/{$customer_id}.json";
        $customer = $this->_get($url);
        if (!is_null($customer)) {
            $customer = $customer->customer;
            $output = $this->__assign($customer);
            return $output;
        } else {
            return $customer;
        }
    }

    private function __getByReference($reference)
    {
        $reference = urlencode($reference);
        $url = config('chargify.api_domain') . "customers/lookup.json?reference={$reference}";
        $customer = $this->_get($url);
        if (!is_null($customer)) {
            $customer = $customer->customer;
            $output = $this->__assign($customer);
            return $output;
        } else {
            return $customer;
        }
    }

    private function __getByQuery($query)
    {
        $query = urlencode($query);
        $url = config('chargify.api_domain') . "customers.json?q={$query}";
        $customers = $this->_get($url);
        if (is_array($customers)) {
            $customers = array_pluck($customers, 'customer');
            $output = array();
            foreach ($customers as $customer) {
                $output[] = $this->__assign($customer);
            }
            return $output;
        } else {
            return $customers;
        }
    }

    private function __assign($input_customer)
    {
        $customer = new Customer;
        foreach ($input_customer as $key => $value) {
            if (property_exists($customer, $key)) {
                $customer->$key = $value;
            }
        }
        return $customer;
    }
}