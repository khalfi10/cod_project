<?php
/**
 * The marketing-related functionality of the plugin.
 *
 * @link       https://example.com
 * @since      1.0.0
 * @package    Hajri_Cod_Shop
 * @subpackage Hajri_Cod_Shop/includes
 */

/**
 * The marketing-related functionality of the plugin.
 *
 * Handles marketing pixels, conversion tracking, and other marketing features.
 *
 * @package    Hajri_Cod_Shop
 * @subpackage Hajri_Cod_Shop/includes
 * @author     Your Name <email@example.com>
 */
class Hajri_Cod_Shop_Marketing {

    /**
     * Add tracking pixels to the site header.
     *
     * @since    1.0.0
     */
    public static function add_tracking_pixels() {
        $settings = get_option('hajri_cod_shop_settings', array());
        
        // Facebook Pixel
        if (isset($settings['facebook_pixel_enabled']) && $settings['facebook_pixel_enabled'] && !empty($settings['facebook_pixel_id'])) {
            self::add_facebook_pixel($settings['facebook_pixel_id']);
        }
        
        // TikTok Pixel
        if (isset($settings['tiktok_pixel_enabled']) && $settings['tiktok_pixel_enabled'] && !empty($settings['tiktok_pixel_id'])) {
            self::add_tiktok_pixel($settings['tiktok_pixel_id']);
        }
        
        // Snapchat Pixel
        if (isset($settings['snapchat_pixel_enabled']) && $settings['snapchat_pixel_enabled'] && !empty($settings['snapchat_pixel_id'])) {
            self::add_snapchat_pixel($settings['snapchat_pixel_id']);
        }
        
        // Google Analytics
        if (isset($settings['google_analytics_enabled']) && $settings['google_analytics_enabled'] && !empty($settings['google_analytics_id'])) {
            self::add_google_analytics($settings['google_analytics_id']);
        }
    }

    /**
     * Add Facebook Pixel to the site header.
     *
     * @since    1.0.0
     * @param    string    $pixel_id    The Facebook Pixel ID.
     */
    private static function add_facebook_pixel($pixel_id) {
        ?>
        <!-- Facebook Pixel Code -->
        <script>
            !function(f,b,e,v,n,t,s)
            {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
            n.callMethod.apply(n,arguments):n.queue.push(arguments)};
            if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
            n.queue=[];t=b.createElement(e);t.async=!0;
            t.src=v;s=b.getElementsByTagName(e)[0];
            s.parentNode.insertBefore(t,s)}(window, document,'script',
            'https://connect.facebook.net/en_US/fbevents.js');
            fbq('init', '<?php echo esc_js($pixel_id); ?>');
            fbq('track', 'PageView');
        </script>
        <noscript>
            <img height="1" width="1" style="display:none" 
                src="https://www.facebook.com/tr?id=<?php echo esc_attr($pixel_id); ?>&ev=PageView&noscript=1"/>
        </noscript>
        <!-- End Facebook Pixel Code -->
        <?php
    }

    /**
     * Add TikTok Pixel to the site header.
     *
     * @since    1.0.0
     * @param    string    $pixel_id    The TikTok Pixel ID.
     */
    private static function add_tiktok_pixel($pixel_id) {
        ?>
        <!-- TikTok Pixel Code -->
        <script>
            !function (w, d, t) {
                w.TiktokAnalyticsObject=t;var ttq=w[t]=w[t]||[];ttq.methods=["page","track","identify","instances","debug","on","off","once","ready","alias","group","enableCookie","disableCookie"],ttq.setAndDefer=function(t,e){t[e]=function(){t.push([e].concat(Array.prototype.slice.call(arguments,0)))}};for(var i=0;i<ttq.methods.length;i++)ttq.setAndDefer(ttq,ttq.methods[i]);ttq.instance=function(t){for(var e=ttq._i[t]||[],n=0;n<ttq.methods.length;n++)ttq.setAndDefer(e,ttq.methods[n]);return e},ttq.load=function(e,n){var i="https://analytics.tiktok.com/i18n/pixel/events.js";ttq._i=ttq._i||{},ttq._i[e]=[],ttq._i[e]._u=i,ttq._t=ttq._t||{},ttq._t[e]=+new Date,ttq._o=ttq._o||{},ttq._o[e]=n||{};var o=document.createElement("script");o.type="text/javascript",o.async=!0,o.src=i+"?sdkid="+e+"&lib="+t;var a=document.getElementsByTagName("script")[0];a.parentNode.insertBefore(o,a)};
                ttq.load('<?php echo esc_js($pixel_id); ?>');
                ttq.page();
            }(window, document, 'ttq');
        </script>
        <!-- End TikTok Pixel Code -->
        <?php
    }

    /**
     * Add Snapchat Pixel to the site header.
     *
     * @since    1.0.0
     * @param    string    $pixel_id    The Snapchat Pixel ID.
     */
    private static function add_snapchat_pixel($pixel_id) {
        ?>
        <!-- Snapchat Pixel Code -->
        <script>
            (function(e,t,n){if(e.snaptr)return;var a=e.snaptr=function()
            {a.handleRequest?a.handleRequest.apply(a,arguments):a.queue.push(arguments)};
            a.queue=[];var s='script';r=t.createElement(s);r.async=!0;
            r.src=n;var u=t.getElementsByTagName(s)[0];
            u.parentNode.insertBefore(r,u);})(window,document,
            'https://sc-static.net/scevent.min.js');
            
            snaptr('init', '<?php echo esc_js($pixel_id); ?>', {
                'user_email': '__INSERT_USER_EMAIL__'
            });
            
            snaptr('track', 'PAGE_VIEW');
        </script>
        <!-- End Snapchat Pixel Code -->
        <?php
    }

    /**
     * Add Google Analytics to the site header.
     *
     * @since    1.0.0
     * @param    string    $ga_id    The Google Analytics ID.
     */
    private static function add_google_analytics($ga_id) {
        ?>
        <!-- Google Analytics -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo esc_attr($ga_id); ?>"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', '<?php echo esc_js($ga_id); ?>');
        </script>
        <!-- End Google Analytics -->
        <?php
    }

    /**
     * Track a conversion for a completed order.
     *
     * @since    1.0.0
     * @param    int       $order_id    The order ID.
     * @param    array     $order_data  The order data.
     */
    public static function track_conversion($order_id, $order_data) {
        $settings = get_option('hajri_cod_shop_settings', array());
        
        // Custom event for tracking conversions
        do_action('hajri_cod_shop_track_conversion', $order_id, $order_data);
        
        // Set up conversion data
        $transaction_id = $order_id;
        $value = $order_data['total_amount'];
        $currency = 'DZD'; // Algerian Dinar
        
        // Build products array for tracking
        $products = array();
        if (!empty($order_data['products']) && is_array($order_data['products'])) {
            foreach ($order_data['products'] as $product) {
                $products[] = array(
                    'id' => $product['id'],
                    'name' => $product['name'],
                    'price' => $product['price'],
                    'quantity' => $product['quantity']
                );
            }
        }
        
        // Facebook Pixel conversion tracking
        if (isset($settings['facebook_pixel_enabled']) && $settings['facebook_pixel_enabled'] && !empty($settings['facebook_pixel_id'])) {
            self::track_facebook_conversion($transaction_id, $value, $currency, $products);
        }
        
        // TikTok Pixel conversion tracking
        if (isset($settings['tiktok_pixel_enabled']) && $settings['tiktok_pixel_enabled'] && !empty($settings['tiktok_pixel_id'])) {
            self::track_tiktok_conversion($transaction_id, $value, $currency, $products);
        }
        
        // Snapchat Pixel conversion tracking
        if (isset($settings['snapchat_pixel_enabled']) && $settings['snapchat_pixel_enabled'] && !empty($settings['snapchat_pixel_id'])) {
            self::track_snapchat_conversion($transaction_id, $value, $currency, $products);
        }
        
        // Google Analytics conversion tracking
        if (isset($settings['google_analytics_enabled']) && $settings['google_analytics_enabled'] && !empty($settings['google_analytics_id'])) {
            self::track_google_analytics_conversion($transaction_id, $value, $currency, $products);
        }
    }

    /**
     * Track a Facebook Pixel conversion.
     *
     * @since    1.0.0
     * @param    string    $transaction_id    The transaction ID.
     * @param    float     $value             The order value.
     * @param    string    $currency          The currency code.
     * @param    array     $products          The products array.
     */
    private static function track_facebook_conversion($transaction_id, $value, $currency, $products) {
        ?>
        <script>
            fbq('track', 'Purchase', {
                value: <?php echo floatval($value); ?>,
                currency: '<?php echo esc_js($currency); ?>',
                content_type: 'product',
                content_ids: <?php echo json_encode(wp_list_pluck($products, 'id')); ?>,
                contents: <?php echo json_encode($products); ?>,
                order_id: '<?php echo esc_js($transaction_id); ?>'
            });
        </script>
        <?php
    }

    /**
     * Track a TikTok Pixel conversion.
     *
     * @since    1.0.0
     * @param    string    $transaction_id    The transaction ID.
     * @param    float     $value             The order value.
     * @param    string    $currency          The currency code.
     * @param    array     $products          The products array.
     */
    private static function track_tiktok_conversion($transaction_id, $value, $currency, $products) {
        ?>
        <script>
            ttq.track('CompletePayment', {
                value: <?php echo floatval($value); ?>,
                currency: '<?php echo esc_js($currency); ?>',
                content_id: <?php echo json_encode(wp_list_pluck($products, 'id')); ?>,
                content_type: 'product',
                quantity: 1,
                price: <?php echo floatval($value); ?>,
                order_id: '<?php echo esc_js($transaction_id); ?>'
            });
        </script>
        <?php
    }

    /**
     * Track a Snapchat Pixel conversion.
     *
     * @since    1.0.0
     * @param    string    $transaction_id    The transaction ID.
     * @param    float     $value             The order value.
     * @param    string    $currency          The currency code.
     * @param    array     $products          The products array.
     */
    private static function track_snapchat_conversion($transaction_id, $value, $currency, $products) {
        ?>
        <script>
            snaptr('track', 'PURCHASE', {
                'transaction_id': '<?php echo esc_js($transaction_id); ?>',
                'currency': '<?php echo esc_js($currency); ?>',
                'price': <?php echo floatval($value); ?>,
                'item_ids': <?php echo json_encode(wp_list_pluck($products, 'id')); ?>
            });
        </script>
        <?php
    }

    /**
     * Track a Google Analytics conversion.
     *
     * @since    1.0.0
     * @param    string    $transaction_id    The transaction ID.
     * @param    float     $value             The order value.
     * @param    string    $currency          The currency code.
     * @param    array     $products          The products array.
     */
    private static function track_google_analytics_conversion($transaction_id, $value, $currency, $products) {
        ?>
        <script>
            gtag('event', 'purchase', {
                'transaction_id': '<?php echo esc_js($transaction_id); ?>',
                'value': <?php echo floatval($value); ?>,
                'currency': '<?php echo esc_js($currency); ?>',
                'items': <?php echo json_encode(array_map(function($product) {
                    return array(
                        'id' => $product['id'],
                        'name' => $product['name'],
                        'price' => $product['price'],
                        'quantity' => $product['quantity']
                    );
                }, $products)); ?>
            });
        </script>
        <?php
    }
}