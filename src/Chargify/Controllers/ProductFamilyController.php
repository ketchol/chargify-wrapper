<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 10/24/2016
 * Time: 11:58 AM
 */

namespace Invigor\Chargify\Controllers;


use Illuminate\Support\Facades\Cache;
use Invigor\Chargify\Models\ProductFamily;
use Invigor\Chargify\Traits\Curl;

class ProductFamilyController
{
    use Curl;


    public function create()
    {

    }

    /**
     * load all product families
     *
     * @return array
     */
    public function all()
    {
        if (config('chargify.caching.enable') == true) {
            return Cache::remember("chargify.product_families", config('chargify.caching.ttl'), function () {
                return $this->__all();
            });
        } else {
            return $this->__all();
        }
    }

    /**
     * load a product family by product family id
     *
     * @param $id
     * @return ProductFamily|null
     */
    public function get($id)
    {
        if (config('chargify.caching.enable') == true) {
            return Cache::remember("chargify.product_families.{$id}", config('chargify.caching.ttl'), function () use ($id) {
                return $this->__get($id);
            });
        } else {
            return $this->__get($id);
        }
    }

    /**
     * @return array
     */
    private function __all()
    {
        $url = config('chargify.api_domain') . "product_families.json";
        $productFamilies = $this->_get($url);
        if (is_array($productFamilies)) {
            $productFamilies = array_pluck($productFamilies, 'product_family');
            $output = array();
            foreach ($productFamilies as $productFamily) {
                $output[] = $this->__assign($productFamily);
            }
            return $output;
        } else {
            return array();
        }
    }

    /**
     * @param $id
     * @return ProductFamily|null
     */
    private function __get($id)
    {
        $url = config('chargify.api_domain') . "product_families/{$id}.json";
        $productFamily = $this->_get($url);
        if (!is_null($productFamily)) {
            $productFamily = $productFamily->product_family;
            $output = $this->__assign($productFamily);
            return $output;
        } else {
            return null;
        }
    }

    /**
     * @param $input_product_family
     * @return ProductFamily
     */
    private function __assign($input_product_family)
    {
        $productFamily = new ProductFamily;
        foreach ($input_product_family as $key => $value) {
            if (property_exists($productFamily, $key)) {
                $productFamily->$key = $value;
            }
        }
        return $productFamily;
    }
}