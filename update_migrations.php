<?php
$dir = __DIR__ . '/database/migrations';
$files = scandir($dir);

$schemas = [
    'create_orders_table' => <<<PHP
            \$table->id();
            \$table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            \$table->string('status')->default('pending');
            \$table->decimal('total_amount', 10, 2)->default(0);
            \$table->string('payment_method')->default('cod');
            \$table->string('payment_status')->default('pending');
            \$table->text('shipping_address')->nullable();
            \$table->decimal('shipping_charge', 10, 2)->default(0);
            \$table->timestamps();
PHP,

    'create_order_items_table' => <<<PHP
            \$table->id();
            \$table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            \$table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            \$table->uuid('variant_id')->nullable();
            \$table->integer('quantity');
            \$table->decimal('unit_price', 10, 2);
            \$table->decimal('total', 10, 2);
            \$table->timestamps();
PHP,

    'create_banners_table' => <<<PHP
            \$table->id();
            \$table->string('title')->nullable();
            \$table->string('image');
            \$table->string('link')->nullable();
            \$table->string('status')->default('active');
            \$table->timestamps();
PHP,

    'create_pages_table' => <<<PHP
            \$table->id();
            \$table->string('title');
            \$table->string('slug')->unique();
            \$table->longText('content')->nullable();
            \$table->string('status')->default('active');
            \$table->timestamps();
PHP,

    'create_coupons_table' => <<<PHP
            \$table->id();
            \$table->string('code')->unique();
            \$table->string('type')->default('fixed'); // fixed or percent
            \$table->decimal('discount_value', 10, 2);
            \$table->decimal('min_spend', 10, 2)->nullable();
            \$table->timestamp('expiry_date')->nullable();
            \$table->string('status')->default('active');
            \$table->timestamps();
PHP,

    'create_shipping_zones_table' => <<<PHP
            \$table->id();
            \$table->string('name');
            \$table->decimal('cost', 10, 2)->default(0);
            \$table->string('status')->default('active');
            \$table->timestamps();
PHP,

    'create_settings_table' => <<<PHP
            \$table->id();
            \$table->string('key')->unique();
            \$table->longText('value')->nullable();
            \$table->timestamps();
PHP,

    'create_campaigns_table' => <<<PHP
            \$table->id();
            \$table->string('title');
            \$table->string('discount_type')->default('percent');
            \$table->decimal('discount_value', 10, 2);
            \$table->timestamp('start_date')->nullable();
            \$table->timestamp('end_date')->nullable();
            \$table->string('status')->default('active');
            \$table->timestamps();
PHP,
];

foreach ($files as $file) {
    foreach ($schemas as $key => $schema) {
        if (strpos($file, $key) !== false) {
            $path = $dir . '/' . $file;
            $content = file_get_contents($path);
            
            // replace `$table->id();\n            $table->timestamps();`
            $search = "\$table->id();\n            \$table->timestamps();";
            if (strpos($content, $search) !== false) {
                $content = str_replace($search, $schema, $content);
                file_put_contents($path, $content);
                echo "Updated $file\n";
            }
        }
    }
}
