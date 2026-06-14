<?php

namespace App\Helpers;

use App\Models\Product;
use App\Models\ProductVariant;

class Cart
{
    public static function get()
    {
        return session()->get('cart', []);
    }

    public static function add($productId, $qty = 1, $variantId = null)
    {
        $cart = self::get();
        $key = $productId . ($variantId ? '-' . $variantId : '');

        $product = Product::find($productId);
        if (!$product) return;

        $price = $product->selling_price;
        $name = $product->name;
        $variantName = '';

        if ($variantId) {
            $variant = ProductVariant::find($variantId);
            if ($variant) {
                $price = $variant->selling_price ?? $product->selling_price;
                $attributes = is_array($variant->attribute_values) ? $variant->attribute_values : json_decode($variant->attribute_values ?? '[]', true);
                if (!empty($attributes)) {
                    $variantName = implode(', ', array_map(function($key, $val) {
                        return ucfirst($key) . ': ' . $val;
                    }, array_keys($attributes), $attributes));
                }
            }
        }

        if (isset($cart[$key])) {
            $cart[$key]['qty'] += $qty;
        } else {
            $cart[$key] = [
                'id' => $productId,
                'name' => $name,
                'image' => $product->images()->first()?->path ?? '',
                'price' => (float)$price,
                'qty' => $qty,
                'variant_id' => $variantId,
                'variant_name' => $variantName,
                'tax_rate' => (float)($product->tax_rate ?? 0),
            ];
        }

        session()->put('cart', $cart);
    }

    public static function update($key, $qty)
    {
        $cart = self::get();
        if (isset($cart[$key])) {
            if ($qty <= 0) {
                unset($cart[$key]);
            } else {
                $cart[$key]['qty'] = $qty;
            }
            session()->put('cart', $cart);
        }
    }

    public static function remove($key)
    {
        $cart = self::get();
        if (isset($cart[$key])) {
            unset($cart[$key]);
            session()->put('cart', $cart);
        }
    }

    public static function clear()
    {
        session()->forget('cart');
    }

    public static function count()
    {
        $cart = self::get();
        return array_sum(array_column($cart, 'qty'));
    }

    public static function subtotal()
    {
        $cart = self::get();
        $total = 0;
        foreach ($cart as $item) {
            $total += $item['price'] * $item['qty'];
        }
        return $total;
    }

    public static function taxTotal()
    {
        $cart = self::get();
        $tax = 0;
        foreach ($cart as $item) {
            $tax += (($item['price'] * $item['tax_rate']) / 100) * $item['qty'];
        }
        return $tax;
    }

    public static function total()
    {
        return self::subtotal() + self::taxTotal();
    }
}
