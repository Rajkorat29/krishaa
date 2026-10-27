<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Drop;
use App\Models\HeroSlide;
use App\Models\MarqueeWord;
use App\Models\Metal;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductSize;
use App\Models\Review;
use App\Models\Setting;
use App\Models\SocialPost;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Users
        $admin = User::firstOrCreate(
            ['email' => 'admin@daisy.test'],
            [
                'name' => 'Daisy Admin',
                'phone' => '9876543210',
                'password' => Hash::make('Admin@12345'),
                'role' => 'admin',
                'is_blocked' => false,
                'email_verified_at' => now(),
            ]
        );

        $customer = User::firstOrCreate(
            ['email' => 'customer@daisy.test'],
            [
                'name' => 'Aarav Sharma',
                'phone' => '9811223344',
                'password' => Hash::make('Customer@12345'),
                'role' => 'customer',
                'is_blocked' => false,
                'email_verified_at' => now(),
            ]
        );

        // Saved customer address
        Address::firstOrCreate(
            ['user_id' => $customer->id, 'is_default' => true],
            [
                'name' => 'Aarav Sharma',
                'phone' => '9811223344',
                'pincode' => '400001',
                'address_line' => 'Flat 402, Sea Breeze Apts, Marine Drive',
                'city' => 'Mumbai',
                'state' => 'Maharashtra',
            ]
        );

        // 2. Categories
        $categoriesData = [
            ['name' => 'Rings', 'slug' => 'rings', 'sort_order' => 1],
            ['name' => 'Earrings', 'slug' => 'earrings', 'sort_order' => 2],
            ['name' => 'Bangles', 'slug' => 'bangles', 'sort_order' => 3],
            ['name' => 'Necklaces', 'slug' => 'necklaces', 'sort_order' => 4],
            ['name' => 'Pearls', 'slug' => 'pearls', 'sort_order' => 5],
        ];

        $categories = [];
        foreach ($categoriesData as $cat) {
            $categories[$cat['slug']] = Category::firstOrCreate(['slug' => $cat['slug']], $cat);
        }

        // 3. Metals
        $metalsData = [
            '22K Gold',
            '18K Gold',
            '18K Rose Gold',
            '18K White Gold',
            '925 Silver',
            'Platinum',
            'Gold plated',
        ];

        $metals = [];
        foreach ($metalsData as $m) {
            $metals[$m] = Metal::firstOrCreate(['name' => $m]);
        }

        // 4. Products: Sample 12 rings from the mockup
        $ringSizes = ['10', '12', '14', '16', '18', '20', '22', '24'];

        $rings = [
            [
                'name' => 'Bandhan Ring Stack',
                'slug' => 'bandhan-ring-stack',
                'metal' => '22K Gold',
                'stone' => 'Ruby accents',
                'weight_grams' => 12.00,
                'price' => 38400,
                'mrp' => 45000,
                'rating_avg' => 4.6,
                'rating_count' => 1248,
                'art_symbol' => 'ring',
                'color_class' => 'c-pink',
                'stock' => 45,
                'is_featured' => true,
                'short_description' => 'Five slim stacking bands with tiny ruby accents. Wear together or alone.',
                'description' => 'Crafted in hallmarked 22K yellow gold, the Bandhan Ring Stack offers five delicately contoured bands designed to interlock or be worn individually. Accented with natural rubies hand-selected for vibrant saturation.',
            ],
            [
                'name' => 'Moonlight Solitaire',
                'slug' => 'moonlight-solitaire',
                'metal' => '18K White Gold',
                'stone' => 'Lab diamond 0.5 ct',
                'weight_grams' => 3.60,
                'price' => 54900,
                'mrp' => 64000,
                'rating_avg' => 4.8,
                'rating_count' => 826,
                'art_symbol' => 'ring-round',
                'color_class' => 'c-sapphire',
                'stock' => 20,
                'is_featured' => true,
                'short_description' => 'A round brilliant lab diamond in a beaded halo, set in white gold.',
                'description' => 'Featuring a certified 0.50 ct round brilliant diamond, enveloped in a delicate millgrain halo of micro-pave stones. Set in 18K rhodium-finished white gold.',
            ],
            [
                'name' => 'Meena Band',
                'slug' => 'meena-band',
                'metal' => '925 Silver',
                'stone' => 'Hand enamel',
                'weight_grams' => 5.20,
                'price' => 3490,
                'mrp' => 5990,
                'rating_avg' => 4.3,
                'rating_count' => 2310,
                'art_symbol' => 'ring-band',
                'color_class' => 'c-gold',
                'stock' => 60,
                'is_featured' => false,
                'short_description' => 'Wide silver band with dotted gold-tone detailing and bright enamel.',
                'description' => 'A contemporary take on traditional Jaipuri meenakari enamel. Hand-applied and fired in small batches on pure 925 sterling silver.',
            ],
            [
                'name' => 'Rani Twin Ring',
                'slug' => 'rani-twin-ring',
                'metal' => '18K Rose Gold',
                'stone' => 'Cubic zirconia',
                'weight_grams' => 4.40,
                'price' => 27800,
                'mrp' => 32000,
                'rating_avg' => 4.5,
                'rating_count' => 512,
                'art_symbol' => 'ring-twin',
                'color_class' => 'c-lilac',
                'stock' => 18,
                'is_featured' => false,
                'short_description' => 'Two interlocked bands, one polished and one gold-tone, with a single centre stone.',
                'description' => 'Two gracefully flowing bands entwined to symbolize unity and modern luxury. Cast in warm 18K rose gold.',
            ],
            [
                'name' => 'Marquise Bloom',
                'slug' => 'marquise-bloom',
                'metal' => '22K Gold',
                'stone' => 'Emerald',
                'weight_grams' => 5.80,
                'price' => 41200,
                'mrp' => 47500,
                'rating_avg' => 4.7,
                'rating_count' => 389,
                'art_symbol' => 'ring-marquise',
                'color_class' => 'c-emerald',
                'stock' => 15,
                'is_featured' => true,
                'short_description' => 'A marquise-cut emerald framed by a smooth gold band.',
                'description' => 'Featuring an untreated Colombian green emerald in a marquise cut that elongates the finger. Solid 22K yellow gold.',
            ],
            [
                'name' => 'Everyday Thin Band',
                'slug' => 'everyday-thin-band',
                'metal' => '925 Silver',
                'stone' => 'Plain',
                'weight_grams' => 2.10,
                'price' => 1890,
                'mrp' => 2999,
                'rating_avg' => 4.1,
                'rating_count' => 4120,
                'art_symbol' => 'ring',
                'color_class' => 'c-lilac',
                'stock' => 100,
                'is_featured' => false,
                'short_description' => 'A light daily-wear band that stacks with anything.',
                'description' => 'Polished to a mirror sheen, comfortable ergonomic interior, resistant to tarnish with anti-oxidation rhodium plating.',
            ],
            [
                'name' => 'Kundan Cocktail Ring',
                'slug' => 'kundan-cocktail-ring',
                'metal' => '22K Gold',
                'stone' => 'Kundan',
                'weight_grams' => 7.90,
                'price' => 33600,
                'mrp' => 38000,
                'rating_avg' => 4.4,
                'rating_count' => 277,
                'art_symbol' => 'ring-round',
                'color_class' => 'c-pink',
                'stock' => 12,
                'is_featured' => false,
                'short_description' => 'A statement ring with a large kundan centre for weddings and festivals.',
                'description' => 'Authentic Jadau Kundan craft with pure 24K gold foil setting and delicate enamel work on the reverse.',
            ],
            [
                'name' => 'Emerald Halo',
                'slug' => 'emerald-halo',
                'metal' => 'Platinum',
                'stone' => 'Emerald + diamonds',
                'weight_grams' => 6.30,
                'price' => 98500,
                'mrp' => 112000,
                'rating_avg' => 4.9,
                'rating_count' => 96,
                'art_symbol' => 'ring-marquise',
                'color_class' => 'c-sapphire',
                'stock' => 8,
                'is_featured' => true,
                'short_description' => 'Platinum ring with an emerald centre and a diamond halo.',
                'description' => 'Solid PT950 platinum ring mounting a deep forest green emerald surrounded by conflict-free round brilliant diamonds.',
            ],
            [
                'name' => 'Pearl Dot Ring',
                'slug' => 'pearl-dot-ring',
                'metal' => '925 Silver',
                'stone' => 'Freshwater pearl',
                'weight_grams' => 3.00,
                'price' => 2790,
                'mrp' => 4499,
                'rating_avg' => 4.2,
                'rating_count' => 1530,
                'art_symbol' => 'ring-round',
                'color_class' => 'c-gold',
                'stock' => 40,
                'is_featured' => false,
                'short_description' => 'A single freshwater pearl on a polished silver band.',
                'description' => 'A button-shaped natural white freshwater pearl cradled in a sterling silver cup on a slender 1.5mm band.',
            ],
            [
                'name' => 'Initial Signet',
                'slug' => 'initial-signet',
                'metal' => '18K Rose Gold',
                'stone' => 'Engraved',
                'weight_grams' => 6.00,
                'price' => 18900,
                'mrp' => 22000,
                'rating_avg' => 4.0,
                'rating_count' => 343,
                'art_symbol' => 'ring-band',
                'color_class' => 'c-emerald',
                'stock' => 25,
                'is_featured' => false,
                'short_description' => 'A signet ring that can be engraved with an initial.',
                'description' => 'Classic flat-top oval signet, weighted for substantial hand-feel. Free custom monogram engraving available upon request.',
            ],
            [
                'name' => 'Lotus Adjustable',
                'slug' => 'lotus-adjustable',
                'metal' => '925 Silver',
                'stone' => 'Cubic zirconia',
                'weight_grams' => 3.40,
                'price' => 2290,
                'mrp' => 3999,
                'rating_avg' => 4.4,
                'rating_count' => 2870,
                'art_symbol' => 'ring-twin',
                'color_class' => 'c-pink',
                'stock' => 70,
                'is_featured' => false,
                'short_description' => 'An open-back lotus design that adjusts to most sizes.',
                'description' => 'Stylized lotus petals studded with brilliant micro-zirconia. Flexible open-cuff band smoothly conforms from size 10 to 20.',
            ],
            [
                'name' => 'Heritage Polki',
                'slug' => 'heritage-polki',
                'metal' => '22K Gold',
                'stone' => 'Uncut polki',
                'weight_grams' => 9.40,
                'price' => 72500,
                'mrp' => 81000,
                'rating_avg' => 4.7,
                'rating_count' => 154,
                'art_symbol' => 'ring-marquise',
                'color_class' => 'c-gold',
                'stock' => 10,
                'is_featured' => true,
                'short_description' => 'Traditional polki ring with uncut diamonds and fine gold work.',
                'description' => 'Royal Mughal-inspired jewellery art with raw uncut polki diamonds set in 24K gold foil and surrounded by ruby dust inlay.',
            ],
        ];

        $createdProducts = [];

        foreach ($rings as $index => $item) {
            $metalName = $item['metal'];
            unset($item['metal']);

            $product = Product::firstOrCreate(
                ['slug' => $item['slug']],
                array_merge($item, [
                    'category_id' => $categories['rings']->id,
                    'metal_id' => $metals[$metalName]->id,
                    'sku' => 'RNG-' . str_pad($index + 1, 4, '0', STR_PAD_LEFT),
                    'is_active' => true,
                    'published_at' => now(),
                ])
            );

            // Seed size stock
            foreach ($ringSizes as $size) {
                ProductSize::firstOrCreate(
                    ['product_id' => $product->id, 'size_label' => $size],
                    ['stock' => rand(5, 12)]
                );
            }

            // Attach product image if exists
            $imagePath = "products/{$product->slug}.jpg";
            if (file_exists(storage_path("app/public/{$imagePath}")) || file_exists(public_path("storage/{$imagePath}"))) {
                ProductImage::firstOrCreate(
                    ['product_id' => $product->id, 'is_cover' => true],
                    ['path' => $imagePath, 'sort_order' => 0]
                );
            }

            $createdProducts[$product->slug] = $product;
        }

        // 5. Products in other categories (Earrings, Bangles, Necklaces, Pearls)
        $otherProducts = [
            [
                'name' => 'Chaand Drop Earrings',
                'slug' => 'chaand-drop-earrings',
                'category' => 'earrings',
                'metal' => '22K Gold',
                'stone' => 'Hand-set ruby',
                'weight_grams' => 8.40,
                'price' => 61200,
                'mrp' => 68000,
                'rating_avg' => 4.9,
                'rating_count' => 312,
                'art_symbol' => 'earrings',
                'color_class' => 'c-emerald',
                'stock' => 14,
                'is_featured' => true,
                'short_description' => 'Teardrop gold with a hand-set ruby.',
                'description' => 'Sculpted crescent drop earrings reminiscent of lunar silhouettes, paired with untreated Burmese rubies.',
            ],
            [
                'name' => 'Gulab Jhumkas',
                'slug' => 'gulab-jhumkas',
                'category' => 'earrings',
                'metal' => '22K Gold',
                'stone' => 'Pearls & Rubies',
                'weight_grams' => 14.20,
                'price' => 92000,
                'mrp' => 105000,
                'rating_avg' => 4.8,
                'rating_count' => 180,
                'art_symbol' => 'earrings',
                'color_class' => 'c-pink',
                'stock' => 9,
                'is_featured' => true,
                'short_description' => 'Traditional bell jhumkas with seed pearls.',
                'description' => 'Intricate filigree work topped with sculpted rose motifs and dancing pearl tassels.',
            ],
            [
                'name' => 'Meena Bangle Set',
                'slug' => 'meena-bangle-set',
                'category' => 'bangles',
                'metal' => '925 Silver',
                'stone' => 'Enamel',
                'weight_grams' => 46.00,
                'price' => 14900,
                'mrp' => 18000,
                'rating_avg' => 4.6,
                'rating_count' => 420,
                'art_symbol' => 'bangle',
                'color_class' => 'c-gold',
                'stock' => 25,
                'is_featured' => true,
                'short_description' => 'Four bangles, enamel in three colours.',
                'description' => 'Four slim stackable silver kadas hand-painted in rich turquoise, saffron, and ruby-red vitreous enamel.',
            ],
            [
                'name' => 'Kangan Kada',
                'slug' => 'kangan-kada',
                'category' => 'bangles',
                'metal' => '22K Gold',
                'stone' => 'Navratna gems',
                'weight_grams' => 32.00,
                'price' => 215000,
                'mrp' => 235000,
                'rating_avg' => 5.0,
                'rating_count' => 48,
                'art_symbol' => 'bangle',
                'color_class' => 'c-pink',
                'stock' => 5,
                'is_featured' => true,
                'short_description' => 'Heirloom screw-closure gold kada.',
                'description' => 'A statement heirloom piece featuring lion-head terminals and nine sacred planetary gems.',
            ],
            [
                'name' => 'Rani Haar Necklace',
                'slug' => 'rani-haar-necklace',
                'category' => 'necklaces',
                'metal' => '18K Rose Gold',
                'stone' => 'Kundan centre',
                'weight_grams' => 24.60,
                'price' => 148000,
                'mrp' => 165000,
                'rating_avg' => 4.9,
                'rating_count' => 94,
                'art_symbol' => 'necklace',
                'color_class' => 'c-sapphire',
                'stock' => 7,
                'is_featured' => true,
                'short_description' => 'Layered chain with a kundan centre.',
                'description' => 'Triple strands of hand-twisted cable links converging onto an ornate Kundan-set medallion.',
            ],
            [
                'name' => 'Choker Hasli',
                'slug' => 'choker-hasli',
                'category' => 'necklaces',
                'metal' => '22K Gold',
                'stone' => 'Polki & Pearls',
                'weight_grams' => 28.00,
                'price' => 186000,
                'mrp' => 210000,
                'rating_avg' => 4.7,
                'rating_count' => 62,
                'art_symbol' => 'necklace',
                'color_class' => 'c-gold',
                'stock' => 6,
                'is_featured' => false,
                'short_description' => 'Rigid collar necklace with floral motif.',
                'description' => 'Royal torque collar neckpiece that rests elegantly on the collarbones with fine granulation detail.',
            ],
            [
                'name' => 'Moti Strand',
                'slug' => 'moti-strand',
                'category' => 'pearls',
                'metal' => 'Gold plated',
                'stone' => 'Freshwater pearls',
                'weight_grams' => 31.00,
                'price' => 22500,
                'mrp' => 26000,
                'rating_avg' => 4.8,
                'rating_count' => 210,
                'art_symbol' => 'pearls',
                'color_class' => 'c-lilac',
                'stock' => 30,
                'is_featured' => true,
                'short_description' => 'Hand-knotted freshwater pearls, 18 in.',
                'description' => 'Graduated AAA lustre pearls individually knotted on pure silk thread with an 18K vermeil filigree ball clasp.',
            ],
            [
                'name' => 'Keshi Pearl Locket',
                'slug' => 'keshi-pearl-locket',
                'category' => 'pearls',
                'metal' => '18K Gold',
                'stone' => 'Keshi pearl',
                'weight_grams' => 8.20,
                'price' => 34000,
                'mrp' => 39000,
                'rating_avg' => 4.6,
                'rating_count' => 115,
                'art_symbol' => 'locket',
                'color_class' => 'c-gold',
                'stock' => 16,
                'is_featured' => false,
                'short_description' => 'Baroque organic pearl encased in gold lattice.',
                'description' => 'Each piece is organically one-of-a-kind. Handcrafted cage opens to hold a tiny keepsake.',
            ],
        ];

        foreach ($otherProducts as $index => $item) {
            $catSlug = $item['category'];
            $metalName = $item['metal'];
            unset($item['category'], $item['metal']);

            $product = Product::firstOrCreate(
                ['slug' => $item['slug']],
                array_merge($item, [
                    'category_id' => $categories[$catSlug]->id,
                    'metal_id' => $metals[$metalName]->id,
                    'sku' => strtoupper(substr($catSlug, 0, 3)) . '-' . str_pad($index + 1, 4, '0', STR_PAD_LEFT),
                    'is_active' => true,
                    'published_at' => now(),
                ])
            );

            // One default size (Standard)
            ProductSize::firstOrCreate(
                ['product_id' => $product->id, 'size_label' => 'Standard'],
                ['stock' => $product->stock]
            );

            // Attach product image if exists
            $imagePath = "products/{$product->slug}.jpg";
            if (file_exists(storage_path("app/public/{$imagePath}")) || file_exists(public_path("storage/{$imagePath}"))) {
                ProductImage::firstOrCreate(
                    ['product_id' => $product->id, 'is_cover' => true],
                    ['path' => $imagePath, 'sort_order' => 0]
                );
            }

            $createdProducts[$product->slug] = $product;
        }

        // 6. Reviews
        if (isset($createdProducts['bandhan-ring-stack'])) {
            Review::firstOrCreate(
                ['product_id' => $createdProducts['bandhan-ring-stack']->id, 'user_id' => $customer->id],
                [
                    'rating' => 5,
                    'title' => 'Stunning shine and weight!',
                    'body' => 'Ordered this for my anniversary. The rings look even better in person, and the rubies have a deep, vibrant glint. Highly recommended.',
                    'is_approved' => true,
                ]
            );
        }

        // 7. Coupons
        Coupon::firstOrCreate(
            ['code' => 'WELCOME10'],
            [
                'type' => 'percent',
                'value' => 10.00,
                'min_order' => 0.00,
                'max_uses' => 500,
                'used_count' => 12,
                'is_active' => true,
            ]
        );

        Coupon::firstOrCreate(
            ['code' => 'DAISY500'],
            [
                'type' => 'flat',
                'value' => 500.00,
                'min_order' => 5000.00,
                'max_uses' => 1000,
                'used_count' => 45,
                'is_active' => true,
            ]
        );

        // 8. Hero Slides (powers the hero rail slider)
        $heroSlidesData = [
            [
                'tag' => 'Now in store',
                'title' => 'Bandhan Ring Stack',
                'meta_text' => '22K gold · set of 5 · from ₹38,400',
                'link_url' => '/rings',
                'color_class' => 'c-pink',
                'art_symbol' => 'ring',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'tag' => 'Next · 2 Nov',
                'title' => 'Chaand Drop Earrings',
                'meta_text' => '22K gold · 8.4 g pair · ₹61,200',
                'link_url' => '/earrings',
                'color_class' => 'c-emerald',
                'art_symbol' => 'earrings',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'tag' => 'Next · 9 Nov',
                'title' => 'Meena Bangle Set',
                'meta_text' => '925 silver · hand-enamelled · ₹14,900',
                'link_url' => '/bangles',
                'color_class' => 'c-gold',
                'art_symbol' => 'bangle',
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'tag' => 'Next · 21 Nov',
                'title' => 'Rani Haar Necklace',
                'meta_text' => '18K rose gold · 24.6 g · ₹1,48,000',
                'link_url' => '/necklaces',
                'color_class' => 'c-sapphire',
                'art_symbol' => 'necklace',
                'sort_order' => 4,
                'is_active' => true,
            ],
            [
                'tag' => 'Next · 28 Nov',
                'title' => 'Moti Strand',
                'meta_text' => 'Freshwater pearls · 18 in · ₹22,500',
                'link_url' => '/pearls',
                'color_class' => 'c-lilac',
                'art_symbol' => 'pearls',
                'sort_order' => 5,
                'is_active' => true,
            ],
        ];

        foreach ($heroSlidesData as $slide) {
            HeroSlide::firstOrCreate(['title' => $slide['title']], $slide);
        }

        // 9. Drops List
        $dropsData = [
            [
                'title' => 'Chaand Drop Earrings',
                'subtitle' => 'Teardrop gold with a hand-set ruby',
                'drop_date' => '2 Nov 2026',
                'drop_time' => '11:00',
                'metal_label' => '22K gold',
                'weight_label' => '8.4 g',
                'price_label' => '₹61,200',
                'status' => 'available',
                'sort_order' => 1,
            ],
            [
                'title' => 'Meena Bangle Set',
                'subtitle' => 'Four bangles, enamel in three colours',
                'drop_date' => '9 Nov 2026',
                'drop_time' => '11:00',
                'metal_label' => '925 silver',
                'weight_label' => '46 g',
                'price_label' => '₹14,900',
                'status' => 'available',
                'sort_order' => 2,
            ],
            [
                'title' => 'Bandhan Ring Stack',
                'subtitle' => 'Five stacking bands, 0.3 ct each',
                'drop_date' => '14 Nov 2026',
                'drop_time' => '18:00',
                'metal_label' => 'Lab diamond',
                'weight_label' => '12 g',
                'price_label' => '₹38,400',
                'status' => 'sold_out',
                'sort_order' => 3,
            ],
            [
                'title' => 'Rani Haar Necklace',
                'subtitle' => 'Layered chain with a kundan centre',
                'drop_date' => '21 Nov 2026',
                'drop_time' => '11:00',
                'metal_label' => '18K rose gold',
                'weight_label' => '24.6 g',
                'price_label' => '₹1,48,000',
                'status' => 'available',
                'sort_order' => 4,
            ],
            [
                'title' => 'Moti Strand',
                'subtitle' => 'Hand-knotted freshwater pearls, 18 in',
                'drop_date' => '28 Nov 2026',
                'drop_time' => '11:00',
                'metal_label' => 'Pearl',
                'weight_label' => '31 g',
                'price_label' => '₹22,500',
                'status' => 'available',
                'sort_order' => 5,
            ],
        ];

        foreach ($dropsData as $drop) {
            Drop::firstOrCreate(['title' => $drop['title']], $drop);
        }

        // 10. Social Posts
        $socialPostsData = [
            [
                'platform_label' => 'Instagram · Reel',
                'caption' => 'Stacking five Bandhan rings in 20 seconds',
                'color_class' => 'c-gold',
                'art_symbol' => 'ring',
                'sort_order' => 1,
            ],
            [
                'platform_label' => 'Instagram',
                'caption' => 'Meena enamel, fresh from the kiln',
                'color_class' => 'c-sapphire',
                'art_symbol' => 'bangle',
                'sort_order' => 2,
            ],
            [
                'platform_label' => 'YouTube · Short',
                'caption' => 'At the bench: setting a 0.5 ct stone',
                'color_class' => 'c-lilac',
                'art_symbol' => 'locket',
                'sort_order' => 3,
            ],
            [
                'platform_label' => 'Instagram',
                'caption' => 'Chaand drops on a rainy Monday',
                'color_class' => 'c-pink',
                'art_symbol' => 'earrings',
                'sort_order' => 4,
            ],
            [
                'platform_label' => 'Instagram · Reel',
                'caption' => 'A grandmother\'s gold, reborn as a necklace',
                'color_class' => 'c-emerald',
                'art_symbol' => 'necklace',
                'sort_order' => 5,
            ],
            [
                'platform_label' => 'Instagram',
                'caption' => 'Pearls, knotted by hand',
                'color_class' => 'c-gold',
                'art_symbol' => 'pearls',
                'sort_order' => 6,
            ],
        ];

        foreach ($socialPostsData as $post) {
            SocialPost::firstOrCreate(['caption' => $post['caption']], $post);
        }

        // 11. Marquee Words
        $words = ['Handcrafted', 'Bold', 'Colourful', 'Timeless'];
        foreach ($words as $idx => $word) {
            MarqueeWord::firstOrCreate(['word' => $word], ['sort_order' => $idx + 1]);
        }

        // 12. Settings
        $settings = [
            'site_name' => 'daisy',
            'tagline' => 'Jewellery with a loud heart.',
            'sub_tagline' => 'Handmade gold, silver and gemstone pieces in colours that don\'t whisper.',
            'contact_email' => 'daisyjewelers940@gmail.com',
            'admin_alert_email' => 'daisyjewelers940@gmail.com',
            'contact_phone' => '+91 98765 43210',
            'contact_address' => '12 Zaveri Lane, Old Market, Mumbai 400002',
            'store_hours' => 'Mon–Sat 11:00–20:00',
            'instagram_url' => 'https://www.instagram.com/',
            'youtube_url' => 'https://www.youtube.com/',
            'pinterest_url' => 'https://www.pinterest.com/',
            'facebook_url' => 'https://www.facebook.com/',
            'free_delivery_threshold' => '0',
            'delivery_fee' => '0',
            'footer_note' => 'Hallmarked 22K and 18K gold, 925 sterling silver, ethically sourced stones.',
            'split_card_1_title' => 'Try on, touch, take home',
            'split_card_1_text' => 'Meet the makers, feel the weight of a piece and get it sized on the spot. Every gold piece carries a BIS hallmark.',
            'split_card_2_title' => 'Design your own piece',
            'split_card_2_text' => 'Bring a sketch, a stone or a family heirloom. Our goldsmiths turn it into something new, with a quote before work begins.',
        ];

        foreach ($settings as $k => $v) {
            Setting::firstOrCreate(['key' => $k], ['value' => $v]);
        }

        // 13. Sample Orders
        $orderStatuses = ['placed', 'confirmed', 'packed', 'shipped', 'delivered'];
        $sampleRing = $createdProducts['bandhan-ring-stack'] ?? Product::first();

        foreach ($orderStatuses as $idx => $status) {
            $num = 'DSY-' . (100100 + $idx);
            $order = Order::firstOrCreate(
                ['order_number' => $num],
                [
                    'user_id' => $customer->id,
                    'customer_name' => 'Aarav Sharma',
                    'customer_email' => 'customer@daisy.test',
                    'customer_phone' => '9811223344',
                    'shipping_address_line' => 'Flat 402, Sea Breeze Apts, Marine Drive',
                    'shipping_city' => 'Mumbai',
                    'shipping_state' => 'Maharashtra',
                    'shipping_pincode' => '400001',
                    'subtotal' => 38400.00,
                    'discount' => 0.00,
                    'delivery_fee' => 0.00,
                    'total' => 38400.00,
                    'payment_method' => $idx % 2 === 0 ? 'COD' : 'UPI',
                    'payment_status' => $status === 'delivered' ? 'paid' : ($status === 'placed' ? 'pending' : 'paid'),
                    'order_status' => $status,
                    'created_at' => now()->subDays(6 - $idx),
                ]
            );

            if ($sampleRing) {
                OrderItem::firstOrCreate(
                    ['order_id' => $order->id, 'product_id' => $sampleRing->id],
                    [
                        'product_name' => $sampleRing->name,
                        'size_label' => '16',
                        'quantity' => 1,
                        'unit_price' => $sampleRing->price,
                        'mrp' => $sampleRing->mrp,
                    ]
                );
            }
        }
    }
}
