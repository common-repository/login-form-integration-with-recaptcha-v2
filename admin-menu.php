<?php
add_action('admin_menu', 'recap_v2_menu');

function recpv2_admin_page() {
    ?>
    <div class="wrap">
        <h2>Recaptcha Keys</h2>
        <form method="post" action="options.php">
            <?php
            settings_fields('recap_v2_settings');
            do_settings_sections('recap_v2');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

function recap_v2_menu() {
    add_menu_page(
        'Recaptcha V2',
        'Integrate V2 Keys',
        'manage_options',
        'recap_v2',
        'recpv2_admin_page',
        'dashicons-lock',
        6
    );
}

add_action('admin_init', 'recapv2_settings_fields');

function recapv2_settings_fields() {
    $page_slug = 'recap_v2';
    $option_group = 'recap_v2_settings';
    
    add_settings_section(
        'site_key_section',
        '',
        '',
        $page_slug
    );
    
    register_setting($option_group, 'secret_key');
    register_setting($option_group, 'site_key');
    
    add_settings_field(
        'site_key',
        'Site Key',
        'recpv2_site_key',
        $page_slug,
        'site_key_section'
    );
    
    add_settings_field(
        'secret_key_field',
        'Secret Key',
        'recpv2_secret_key',
        $page_slug,
        'site_key_section',
        array(
            'label_for' => 'secret_key',
            'class'     => 'hello'
        )
    );
}

function recpv2_secret_key($args) {
    $secret_key = get_option('secret_key');
    ?>
    <label>
        <input type="text" name="secret_key" value="<?php echo esc_attr($secret_key); ?>" />
    </label>
    <?php
}

function recpv2_site_key($args) {
    $site_key = get_option('site_key');
    ?>
    <label>
        <input type="text" name="site_key" value="<?php echo esc_attr($site_key); ?>" />
    </label>
    <?php
}
?>
