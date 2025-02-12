<?php

/**
 * Plugin Name: WP Google Ad Manager
 * Plugin URI:  https://github.com/akikadigital/wp-google-ads-manager
 * Description: A simple plugin to perform dynamic Ads on Wordpress using Google Ad Manager.
 * Version:     1.2
 * Author:      Akika Digital
 * Author URI:  https://akika.digital
 * License:     GPLv2 or later
 * Text Domain: akika-google-ad-manager
 */

function wp_gam_enqueue_styles()
{
    wp_enqueue_style('wp-google-ads-manager', plugin_dir_url(__FILE__) . 'includes/styles.css');
}
add_action('admin_enqueue_scripts', 'wp_gam_enqueue_styles');


// Register settings
add_action('admin_init', 'wp_google_ads_manager_settings_init');
function wp_google_ads_manager_settings_init()
{
    register_setting('wp-google-ads-manager-group', 'wp_gam_header_js');
    register_setting('wp-google-ads-manager-group', 'wp_gam_homepage_ads');
    register_setting('wp-google-ads-manager-group', 'wp_gam_category_page_ads');
    register_setting('wp-google-ads-manager-group', 'wp_gam_post_page_ads');
    register_setting('wp-google-ads-manager-group', 'wp_gam_target_categories');

    // Fetch target categories
    $target_categories = get_option('wp_gam_target_categories');
    if ($target_categories) {
        $categories = explode(',', $target_categories);
        foreach ($categories as $category) {
            $category = trim($category);

            register_setting('wp-google-ads-manager-group', 'wp_gam_' . $category . '_header_js');
            register_setting('wp-google-ads-manager-group', 'wp_gam_' . $category . '_post_header_js');

            $landing_page_ads = get_option('wp_gam_category_page_ads');
            $post_page_ads = get_option('wp_gam_post_page_ads');

            if ($landing_page_ads) $landing_page_ads = explode(',', $landing_page_ads);
            foreach ($landing_page_ads as $value) {
                register_setting('wp-google-ads-manager-group', 'wp_gam_' . $category . '_page_' . $value);
                // eg wp_gam_business_page_top_banner
            }


            if ($post_page_ads) $post_page_ads = explode(',', $post_page_ads);
            foreach ($post_page_ads as $value) {
                register_setting('wp-google-ads-manager-group', 'wp_gam_' . $category . '_post_' . $value);
                // eg wp_gam_business_post_top_banner
            }
        }
    }
}

// Add settings page to the admin menu
add_action('admin_menu', 'wp_gam_settings_page');
function wp_gam_settings_page()
{
    add_options_page(
        'WP Google Ads Manager', // Page title
        'WP Google Ads',          // Menu title
        'manage_options',      // Capability
        'wp-google-ads-manager-settings', // Slug
        'wp_google_ads_manager_settings_render' // Function to render the settings page
    );
}

// Render the settings page
function wp_google_ads_manager_settings_render()
{
    $target_categories = esc_html(get_option('wp_gam_target_categories'));
    $categories = $target_categories ? explode(',', $target_categories) : [];

    $landing_page_ads = get_option('wp_gam_category_page_ads');
    if ($landing_page_ads) $landing_page_ads = explode(',', $landing_page_ads);

    $post_page_ads = get_option('wp_gam_post_page_ads');
    if ($post_page_ads) $post_page_ads = explode(',', $post_page_ads);
?>
    <div class="wrap">
        <h1>WP Google Ads Manager</h1>
        <p>
            This plugin allows you to manage Google Ads on your Wordpress site. You can add ads to the homepage, category pages, post pages. You can also target all or specific categories.
        </p>
        <p>
            The plugin uses Google Ad Manager to manage the ads. Once you design the ads on Google Ad Manager, you can paste the ad code here.
        </p>
        <form method="post" action="options.php">
            <?php settings_fields('wp-google-ads-manager-group'); ?>
            <?php do_settings_sections('wp-google-ads-manager-group'); ?>

            <div id="accordion">
                <h3><a href="#" class="wp-google-ads-manager-color-dark">General Settings</a></h3>
                <div>
                    <table class="form-table">
                        <tr>
                            <th scope="row"><label for="wp_gam_header_js">Ads Header JS:</label></th>
                            <td>
                                <textarea id="wp_gam_header_js" name="wp_gam_header_js" rows="5" cols="80"><?= esc_html(get_option('wp_gam_header_js')) ?></textarea>
                                <p class="description">Paste the general Header JS code from Google Ad Manager</p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="wp_gam_homepage_ads">Homepage Ads:</label></th>
                            <td>
                                <textarea id="wp_gam_homepage_ads" name="wp_gam_homepage_ads" rows="5" cols="80"><?= esc_html(get_option('wp_gam_homepage_ads')) ?></textarea>
                                <p class="description">Add command separated homepage ads in slug format e.g. homepage_top_banner</p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="wp_gam_category_page_ads">Category Page Ads:</label></th>
                            <td>
                                <textarea id="wp_gam_category_page_ads" name="wp_gam_category_page_ads" rows="5" cols="80"><?= esc_html(get_option('wp_gam_category_page_ads')) ?></textarea>
                                <p class="description">Add command separated category page ads in slug format e.g. top_banner</p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="wp_gam_post_page_ads">Post Page Ads:</label></th>
                            <td>
                                <textarea id="wp_gam_post_page_ads" name="wp_gam_post_page_ads" rows="5" cols="80"><?= esc_html(get_option('wp_gam_post_page_ads')) ?></textarea>
                                <p class="description">Add command separated post page ads in slug format e.g. top_banner</p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="wp_gam_target_categories">Target Categories:</label></th>
                            <td>
                                <textarea id="wp_gam_target_categories" name="wp_gam_target_categories" rows="5" cols="80"><?= esc_html($target_categories) ?></textarea>
                                <p class="description">Add comma separated slugs for the target categories</p>
                            </td>
                        </tr>
                    </table>
                </div>
                <h3><a href="#" class="wp-google-ads-manager-color-dark">Homepage Ads</a></h3>
                <div>
                    <table class="form-table">
                        <tr>
                            <th scope="row"><label for="wp_gam_homepage_header_js">Homepage Header Code:</label></th>
                            <td>
                                <textarea id="wp_gam_homepage_header_js" name="wp_gam_homepage_header_js" rows="5" cols="80"><?= esc_html(get_option('wp_gam_homepage_header_js')) ?></textarea>
                                <p class="description">Paste homepage Header JS code from Google Ad Manager.</p>
                            </td>
                        </tr>

                        <?php
                        $homepage_ads = get_option('wp_gam_homepage_ads');
                        if ($homepage_ads) $homepage_ads = explode(',', $homepage_ads);

                        foreach ($homepage_ads as $value) :
                        ?>
                            <tr>
                                <th scope="row"><label for="wp_gam_homepage_<?= $value; ?>"><?= ucwords(str_replace('_', ' ', $value)); ?>:</label></th>
                                <td>
                                    <textarea id="wp_gam_homepage_<?= $value; ?>" name="wp_gam_homepage_<?= $value; ?>" rows="5" cols="80"><?= esc_html(get_option('wp_gam_homepage_' . $value)) ?></textarea>
                                    <p class="description">Paste <?= strtolower(str_replace('_', ' ', $value)); ?> ad code from Google Ad Manager.</p>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                </div>

                <?php
                foreach ($categories as $category):
                    $category = trim($category);
                ?>
                    <h3><a href="#" class="wp-google-ads-manager-color-dark"><?= ucfirst($category); ?> Page Ads</a></h3>
                    <div>
                        <table class="form-table">
                            <tr>
                                <th scope="row"><label for="wp_gam_<?= $category; ?>_header_js"><?= ucwords($category); ?> Page Header JS:</label></th>
                                <td>
                                    <textarea id="wp_gam_<?= $category; ?>_header_js" name="wp_gam_<?= $category; ?>_header_js" rows="5" cols="80"><?= esc_html(get_option('wp_gam_' . $category . '_header_js')) ?></textarea>
                                    <p class="description">Paste <?= $category; ?> landing page Header JS code from Google Ad Manager.</p>
                                </td>
                            </tr>

                            <?php foreach ($landing_page_ads as $value) : ?>
                                <tr>
                                    <th scope="row"><label for="wp_gam_<?= $category; ?>_page_<?= $value; ?>"><?= ucfirst($category); ?> Page <?= ucwords(str_replace('_', ' ', $value)); ?>:</label></th>
                                    <td>
                                        <textarea id="wp_gam_<?= $category; ?>_page_<?= $value; ?>" name="wp_gam_<?= $category; ?>_page_<?= $value; ?>" rows="5" cols="80"><?= esc_html(get_option('wp_gam_' . $category . '_page_' . $value)) ?></textarea>
                                        <p class="description">Paste <?= $category; ?> page <?= strtolower(str_replace('_', ' ', $value)); ?> ad code from Google Ad Manager.</p>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </table>
                    </div>
                    <h3><a href="#" class="wp-google-ads-manager-color-dark"><?= ucfirst($category); ?> Post Ads</a></h3>
                    <div>
                        <p class="description">Paste the body ad unit tag from Google Ad Manager for this section.</p>
                        <table class="form-table">
                            <tr>
                                <th scope="row"><label for="wp_gam_<?= $category; ?>_post_header_js"><?= ucfirst($category); ?> Post Header JS:</label></th>
                                <td>
                                    <textarea id="wp_gam_<?= $category; ?>_post_header_js" name="wp_gam_<?= $category; ?>_post_header_js" rows="5" cols="80"><?= esc_html(get_option('wp_gam_' . $category . '_post_header_js')) ?></textarea>
                                    <p class="description">Paste <?= $category; ?> post page Header JS code from Google Ad Manager.</p>
                                </td>
                            </tr>

                            <?php foreach ($post_page_ads as $value) : ?>
                                <tr>
                                    <th scope="row"><label for="wp_gam_<?= $category; ?>_post_<?= $value; ?>"><?= ucfirst($category); ?> Post <?= ucwords(str_replace('_', ' ', $value)); ?>:</label></th>
                                    <td>
                                        <textarea id="wp_gam_<?= $category; ?>_post_<?= $value; ?>" name="wp_gam_<?= $category; ?>_post_<?= $value; ?>" rows="5" cols="80"><?= esc_html(get_option('wp_gam_' . $category . '_post_' . $value)) ?></textarea>
                                        <p class="description">Paste <?= $category; ?> post <?= strtolower(str_replace('_', ' ', $value)); ?> ad code from Google Ad Manager.</p>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </table>
                    </div>
                <?php endforeach; ?>

            </div>

            <?php submit_button('Save Changes'); ?>
        </form>
    </div>

    <script>
        jQuery(document).ready(function($) {
            $("#accordion").accordion({
                heightStyle: "content"
            });
        });
    </script>

<?php
}

// Retrieve ad manager content from the database (if needed)
function wp_gam_display_ad($title, $category = null)
{
    if ($category) {
        return get_option('wp_gam_' . $category . '_' . $title);
    }
    return get_option('wp_gam_' . $title);
}
?>