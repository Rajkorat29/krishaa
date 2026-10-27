<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Categories
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('image')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. Metals
        Schema::create('metals', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        // 3. Products
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->foreignId('metal_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('sku')->unique();
            $table->text('short_description')->nullable();
            $table->longText('description')->nullable();
            $table->string('stone')->nullable();
            $table->decimal('weight_grams', 8, 2)->default(0);
            $table->decimal('price', 10, 2);
            $table->decimal('mrp', 10, 2)->nullable();
            $table->integer('stock')->default(0);
            $table->decimal('rating_avg', 3, 2)->default(0.00);
            $table->unsignedInteger('rating_count')->default(0);
            $table->string('art_symbol')->default('ring');
            $table->string('color_class')->default('c-pink');
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['category_id', 'is_active', 'price']);
            $table->index(['metal_id', 'is_active']);
            $table->index('rating_avg');
        });

        // 4. Product Images
        Schema::create('product_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('path');
            $table->boolean('is_cover')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // 5. Product Sizes
        Schema::create('product_sizes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('size_label');
            $table->integer('stock')->default(0);
            $table->timestamps();

            $table->unique(['product_id', 'size_label']);
        });

        // 6. Reviews
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('rating')->default(5);
            $table->string('title')->nullable();
            $table->text('body');
            $table->boolean('is_approved')->default(true);
            $table->timestamps();
        });

        // 7. Wishlists
        Schema::create('wishlists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['user_id', 'product_id']);
        });

        // 8. Carts & Cart Items
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('session_token')->nullable()->index();
            $table->string('coupon_code')->nullable();
            $table->timestamps();
        });

        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cart_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('size_label')->default('16');
            $table->unsignedInteger('quantity')->default(1);
            $table->timestamps();

            $table->unique(['cart_id', 'product_id', 'size_label']);
        });

        // 9. Addresses
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('phone');
            $table->string('pincode', 10);
            $table->text('address_line');
            $table->string('city');
            $table->string('state');
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });

        // 10. Orders & Order Items
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('customer_name');
            $table->string('customer_email')->nullable();
            $table->string('customer_phone');
            $table->text('shipping_address_line');
            $table->string('shipping_city');
            $table->string('shipping_state');
            $table->string('shipping_pincode', 10);
            $table->decimal('subtotal', 10, 2);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('delivery_fee', 10, 2)->default(0);
            $table->decimal('total', 10, 2);
            $table->string('payment_method')->default('COD'); // UPI, Card, COD
            $table->string('payment_status')->default('pending'); // pending, paid, failed, refunded
            $table->string('order_status')->default('placed'); // placed, confirmed, packed, shipped, delivered, cancelled
            $table->string('coupon_code')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['order_status', 'created_at']);
            $table->index('user_id');
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->string('product_name');
            $table->string('size_label')->default('16');
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('unit_price', 10, 2);
            $table->decimal('mrp', 10, 2)->nullable();
            $table->string('image_snapshot')->nullable();
            $table->timestamps();
        });

        // 11. Coupons
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->enum('type', ['percent', 'flat'])->default('percent');
            $table->decimal('value', 10, 2);
            $table->decimal('min_order', 10, 2)->default(0);
            $table->unsignedInteger('max_uses')->nullable();
            $table->unsignedInteger('used_count')->default(0);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 12. Hero Slides
        Schema::create('hero_slides', function (Blueprint $table) {
            $table->id();
            $table->string('tag')->nullable();
            $table->string('title');
            $table->string('meta_text')->nullable();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->string('link_url')->nullable();
            $table->string('color_class')->default('c-pink');
            $table->string('art_symbol')->default('ring');
            $table->string('image')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 13. Drops
        Schema::create('drops', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->string('drop_date');
            $table->string('drop_time')->default('11:00');
            $table->string('metal_label')->nullable();
            $table->string('weight_label')->nullable();
            $table->string('price_label')->nullable();
            $table->enum('status', ['available', 'sold_out'])->default('available');
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // 14. Social Posts
        Schema::create('social_posts', function (Blueprint $table) {
            $table->id();
            $table->string('platform_label')->default('Instagram');
            $table->string('caption');
            $table->string('color_class')->default('c-gold');
            $table->string('art_symbol')->default('ring');
            $table->string('image')->nullable();
            $table->string('link_url')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 15. Marquee Words
        Schema::create('marquee_words', function (Blueprint $table) {
            $table->id();
            $table->string('word');
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // 16. Settings
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // 17. Newsletter Subscribers
        Schema::create('newsletter_subscribers', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->timestamp('subscribed_at')->nullable();
            $table->timestamps();
        });

        // 18. Contact Messages
        Schema::create('contact_messages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->text('message');
            $table->boolean('is_read')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contact_messages');
        Schema::dropIfExists('newsletter_subscribers');
        Schema::dropIfExists('settings');
        Schema::dropIfExists('marquee_words');
        Schema::dropIfExists('social_posts');
        Schema::dropIfExists('drops');
        Schema::dropIfExists('hero_slides');
        Schema::dropIfExists('coupons');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('addresses');
        Schema::dropIfExists('cart_items');
        Schema::dropIfExists('carts');
        Schema::dropIfExists('wishlists');
        Schema::dropIfExists('reviews');
        Schema::dropIfExists('product_sizes');
        Schema::dropIfExists('product_images');
        Schema::dropIfExists('products');
        Schema::dropIfExists('metals');
        Schema::dropIfExists('categories');
    }
};
