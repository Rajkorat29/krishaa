<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Coupon;
use App\Models\Metal;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DaisyEcommerceTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $customer;
    protected Category $ringsCategory;
    protected Metal $goldMetal;
    protected Product $sampleRing;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();

        $this->admin = User::where('email', 'admin@daisy.test')->first();
        $this->customer = User::where('email', 'customer@daisy.test')->first();
        $this->ringsCategory = Category::where('slug', 'rings')->first();
        $this->goldMetal = Metal::first();
        $this->sampleRing = Product::where('slug', 'bandhan-ring-stack')->first();
    }

    /**
     * 1. Test homepage loads with 200 OK.
     */
    public function test_homepage_loads_successfully(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Jewellery with a');
        $response->assertSee('New drops');
    }

    /**
     * 2. Test product listing and filters / sort.
     */
    public function test_product_listing_and_filters(): void
    {
        $response = $this->get('/rings');
        $response->assertStatus(200);
        $response->assertSee('Bandhan Ring Stack');

        // Test max_price filter
        $filteredResponse = $this->get('/rings?max_price=3000');
        $filteredResponse->assertStatus(200);
        $filteredResponse->assertSee('Everyday Thin Band');

        // Test AJAX JSON request
        $ajaxResponse = $this->getJson('/rings?q=Bandhan');
        $ajaxResponse->assertStatus(200);
        $ajaxResponse->assertJsonStructure(['html', 'count', 'total']);

        // Test other category pages
        $this->get('/earrings')->assertStatus(200)->assertSee('Earrings');
        $this->get('/bangles')->assertStatus(200)->assertSee('Bangles');
        $this->get('/necklaces')->assertStatus(200)->assertSee('Necklaces');
        $this->get('/pearls')->assertStatus(200)->assertSee('Pearls');
    }

    /**
     * 3. Test Add to Cart & Cart Updates.
     */
    public function test_add_to_cart_and_update(): void
    {
        $response = $this->postJson('/cart/add', [
            'product_id' => $this->sampleRing->id,
            'size_label' => '16',
            'quantity' => 2,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'cart_count' => 2,
        ]);

        // Cart drawer fragment view
        $cartView = $this->get('/cart?fragment=1');
        $cartView->assertStatus(200);
        $cartView->assertSee('Bandhan Ring Stack');
    }

    /**
     * 4. Test Coupon Code application.
     */
    public function test_coupon_application(): void
    {
        // Add item to cart first
        $this->postJson('/cart/add', [
            'product_id' => $this->sampleRing->id,
            'size_label' => '16',
            'quantity' => 1,
        ]);

        // Apply WELCOME10 coupon (10% off)
        $couponResponse = $this->postJson('/cart/apply-coupon', [
            'coupon_code' => 'WELCOME10',
        ]);

        $couponResponse->assertStatus(200);
        $couponResponse->assertJson([
            'success' => true,
        ]);
    }

    /**
     * 5. Test Checkout creates order and reduces product stock.
     */
    public function test_checkout_creates_order_and_reduces_stock(): void
    {
        $initialStock = $this->sampleRing->stock;

        // Login as customer
        $this->actingAs($this->customer);

        // Add 1 item to cart
        $this->postJson('/cart/add', [
            'product_id' => $this->sampleRing->id,
            'size_label' => '16',
            'quantity' => 1,
        ]);

        // Post checkout
        $response = $this->post('/checkout', [
            'selected_address_id' => 'new',
            'name' => 'Aarav Sharma',
            'phone' => '9811223344',
            'pincode' => '400001',
            'address_line' => 'Flat 402, Sea Breeze Apts, Marine Drive',
            'city' => 'Mumbai',
            'state' => 'Maharashtra',
            'payment_method' => 'COD',
        ]);

        $response->assertRedirect();

        // Verify order created in database
        $this->assertDatabaseHas('orders', [
            'user_id' => $this->customer->id,
            'customer_name' => 'Aarav Sharma',
            'payment_method' => 'COD',
            'order_status' => 'placed',
        ]);

        // Verify stock reduced by 1
        $this->sampleRing->refresh();
        $this->assertEquals($initialStock - 1, $this->sampleRing->stock);
    }

    /**
     * 6. Test Admin Access Control.
     */
    public function test_admin_access_control(): void
    {
        // Unauthenticated guest redirected
        $guestResponse = $this->get('/admin');
        $guestResponse->assertRedirect(route('login'));

        // Customer receives 403 or redirect
        $this->actingAs($this->customer);
        $customerResponse = $this->get('/admin');
        $customerResponse->assertRedirect(route('login'));

        // Admin allowed
        $this->actingAs($this->admin);
        $adminResponse = $this->get('/admin');
        $adminResponse->assertStatus(200);
        $adminResponse->assertSee('Total Sales');
    }

    /**
     * 7. Test Admin Product creation with image upload.
     */
    public function test_admin_can_create_product_with_image(): void
    {
        Storage::fake('public');

        $this->actingAs($this->admin);

        $file = UploadedFile::fake()->create('ruby_ring.jpg', 100, 'image/jpeg');

        $response = $this->post(route('admin.products.store'), [
            'name' => 'Royal Peacock Ring',
            'slug' => 'royal-peacock-ring',
            'category_id' => $this->ringsCategory->id,
            'metal_id' => $this->goldMetal->id,
            'price' => 45000,
            'mrp' => 52000,
            'stock' => 15,
            'weight_grams' => 6.2,
            'stone' => 'Tanzanite & Emerald',
            'art_symbol' => 'ring-round',
            'color_class' => 'c-sapphire',
            'is_active' => '1',
            'images' => [$file],
        ]);

        $response->assertRedirect(route('admin.products.index'));

        $this->assertDatabaseHas('products', [
            'name' => 'Royal Peacock Ring',
            'slug' => 'royal-peacock-ring',
            'price' => 45000,
        ]);

        $created = Product::where('slug', 'royal-peacock-ring')->first();
        $this->assertNotNull($created);
        $this->assertCount(1, $created->images);
    }
}
