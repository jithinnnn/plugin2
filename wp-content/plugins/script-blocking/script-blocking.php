<?php
/*
 Plugin Name:   Script Blocker 2.0
 Plugin URI:    https://pluginAssignment.test
 Description:   This is a demo version of the script blocker plugin
 Version:       2.0.0
 Author:        Jithin George Jose 
 */
if (!defined('ABSPATH')) {
    exit; 
}

require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );

function sb_initialize_plugin_state() {
    if (get_option('sb_plugin_enabled') === false) {
        update_option('sb_plugin_enabled', true);
    }
}
add_action('init', 'sb_initialize_plugin_state');


function sb_display_frontend_banner(){
    if (get_option('sb_plugin_enabled')){ 
        echo '<div id="sb-frontend-banner"><h3>This site uses scripts.</h3> Accept or reject to proceed. <br><br>
        <button class="btn btn-accept">Accept</button>
        <button class="btn btn-reject">Reject</button>
    </div>';
    echo '<div id="sb-revisit-banner" style="display:none;"><h3>Change your consent settings.</h3> <button class="btn btn-revisit">Revisit Consent</button></div>';
    }
}

function sb_enqueue_frontend_scripts(){
        wp_enqueue_style('sb-frontend-style',plugin_dir_url(__FILE__) . 'css/frontend-banner-style.css'); 
        wp_enqueue_script('sb-frontend-script', plugin_dir_url(__FILE__) . 'js/frontend-script.js', array('jquery'), null, true); 
        wp_localize_script('sb-frontend-script', 'sb_ajax_object', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('sb_nonce')  
        ));
}
add_action('wp_enqueue_scripts', 'sb_enqueue_frontend_scripts');

function sb_add_frontend_banner() {
    sb_display_frontend_banner();
}
if (get_option('sb_plugin_enabled')) {
    add_action('wp_footer', 'sb_add_frontend_banner');
}

function add_my_own_menu() {
    global $wp_admin_bar;

    $custom_menu = array(
        'id' => 'demo_menu',
        'title' => 'Script Blocker 2.0',
        'parent' => 'top-secondary',
        'href' => site_url()
    );

    $wp_admin_bar->add_node($custom_menu);
}
add_action('admin_bar_menu', 'add_my_own_menu');

function add_admin_menu() {
    add_menu_page(
        'Script Blocker 2.0 Settings',
        'Script Blocker 2.0',
        'manage_options',
        'script-blocker',
        'sb_settings_page',
    );

    add_submenu_page(
        'script-blocker',
        'Consent Log',
        'Consent Log',
        'manage_options',
        'sb-consent-log',
        'sb_consent_log_page'
    );

}
add_action('admin_menu', 'add_admin_menu');

function enqueue_bootstrap_cdn($hook) {
    $allowed_screens = ['toplevel_page_script-blocker', 'script-blocker_page_sb-consent-log'];

    if (in_array($hook, $allowed_screens)) {
        wp_enqueue_style('bootstrap-css', 'https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css');
        wp_enqueue_script('bootstrap-js', 'https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js', array('jquery'), null, true);
    }
}
add_action('admin_enqueue_scripts', 'enqueue_bootstrap_cdn');


function my_plugin_enqueue_admin_styles($hook) {
    $allowed_screens = ['toplevel_page_script-blocker', 'script-blocker_page_sb-consent-log'];

    if (in_array($hook, $allowed_screens)) {
        wp_enqueue_style('my-plugin-admin-style', plugin_dir_url(__FILE__) . 'css/style.css');
    }
}
add_action('admin_enqueue_scripts', 'my_plugin_enqueue_admin_styles');


function toggle_plugin(){
    $current_state = get_option('sb_plugin_enabled');
    update_option('sb_plugin_enabled',!$current_state);
}


class SB_Consent_Log_Table extends WP_List_Table {
   
    function __construct() {
        parent::__construct(array(
            'singular' => 'Consent Log',
            'plural'   => 'Consent Logs',
            'ajax'     => false
        ));
    }

    function get_columns() {
        $columns = array(
            'cb'         => '<input type="checkbox" />',
            'id'         => 'ID',
            'consent'    => 'Consent',
            'consent_time' => 'Time'
        );
        return $columns;
    }

    function column_default($item, $column_name) {
        switch($column_name) {
            case 'id':
            case 'consent':
            case 'consent_time':
                return $item[$column_name];
            default:
                return print_r($item, true); 
        }
    }

    
    function get_sortable_columns() {
        $sortable_columns = array(
            'id'         => array('id', true),
            'consent'    => array('consent', true),
            'consent_time' => array('consent_time', true)
        );
        return $sortable_columns;
    }

    
    function prepare_items() {
        global $wpdb;

        $table_name = $wpdb->prefix . 'sb_consent_log';
        $per_page = 10;
        $current_page = $this->get_pagenum();
        $total_items = $wpdb->get_var("SELECT COUNT(id) FROM $table_name");

        $this->set_pagination_args(array(
            'total_items' => $total_items,
            'per_page'    => $per_page
        ));

        $orderby = !empty($_REQUEST['orderby']) ? $_REQUEST['orderby'] : 'consent_time';
        $order = !empty($_REQUEST['order']) ? $_REQUEST['order'] : 'DESC';

        $this->_column_headers = array($this->get_columns(), array(), $this->get_sortable_columns());

        $this->items = $wpdb->get_results(
            $wpdb->prepare("SELECT * FROM $table_name ORDER BY $orderby $order LIMIT %d OFFSET %d", $per_page, ($current_page - 1) * $per_page),
            ARRAY_A
        );
    }
}

function sb_consent_log_page() {
    $sb_consent_log_table = new SB_Consent_Log_Table();
    $sb_consent_log_table->prepare_items();
    ?>
    <div class="wrap my-plugin-admin-page">
        <h1 class="text-danger">Consent Log</h1>
        <form method="post">
            <?php $sb_consent_log_table->display(); ?>
        </form>
    </div>
    <?php
}


function sb_settings_page() {

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_POST['new_keyword'])) {
            $new_keyword = sanitize_text_field($_POST['new_keyword']);
            $keywords = get_option('sb_keywords', '');
            $keywords_array = !empty($keywords) ? explode(',', $keywords) : array();
            $keywords_array[] = $new_keyword;
            $keywords = implode(',', $keywords_array);
            update_option('sb_keywords', $keywords);
        }

        if (isset($_POST['toggle_plugin'])) {
            toggle_plugin(); 
        }   

        if (isset($_POST['keyword_action']) && isset($_POST['keyword'])) {
            $keyword = sanitize_text_field($_POST['keyword']);
            $action = $_POST['keyword_action'];
            if ($action == 'enable') {
                update_option('sb_enable_blocking_' . $keyword, '1');
            } elseif ($action == 'disable') {
                update_option('sb_enable_blocking_' . $keyword, '0');
            }
        }

        if (isset($_POST['remove_keyword'])) {
            $keyword_to_remove = sanitize_text_field($_POST['remove_keyword']);
            $keywords = get_option('sb_keywords', '');
            $keywords_array = !empty($keywords) ? explode(',', $keywords) : array();
            $keywords_array = array_diff($keywords_array, array($keyword_to_remove));
            $keywords = implode(',', $keywords_array);
            update_option('sb_keywords', $keywords);
            delete_option('sb_enable_blocking_' . $keyword_to_remove);
        }
    }

    $plugin_enabled = get_option('sb_plugin_enabled');
    $toggle_button_text = $plugin_enabled ? 'Disable Plugin' : 'Enable Plugin';
    $toggle_button_class = $plugin_enabled ? 'btn-danger' : 'btn-success';

    ?>
    <body style="background-size: cover; background-repeat:no-repeat; background-image: url('<?php echo plugin_dir_url(__FILE__) . 'images/gradient-7258997_640.webp'; ?>');">
        <div class="wrap my-plugin-admin-page container w-75">
            <div class="formwrap">
            <form method="post">
                <div class="form-group">
                    <label for="new_keyword" class="text-danger">Add Keyword</label>
                    <input type="text" id="new_keyword" name="new_keyword" class="form-control" />
                    <button type="submit" class="btn-sm btn-primary mt-2">Add Keyword</button>
                </div>
            </form>
            <div class="wrapping ml-5 mb-4">
                <form method="post" action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>">
                        <button type="submit" value="true" name="toggle_plugin" class="btn-sm <?php echo $toggle_button_class; ?>"><?php echo $toggle_button_text; ?></button>
                    <input type="hidden" name="action" value="toggle_plugin">
                    </form>
            </div>
            </div>
        
            <h4 class="below">Keywords to Block</h4>
            <table class="table w-50 table-striped">
                <thead>
                    <tr>
                        <th>Keyword</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $keywords = get_option('sb_keywords');
                    if (!empty($keywords)) {
                        $keywords = explode(',', $keywords);
                        foreach ($keywords as $keyword) {
                            $is_enabled = get_option('sb_enable_blocking_' . $keyword, '0') == '1';
                            ?>
                            <tr>
                                <td><?php echo esc_html($keyword); ?></td>
                                <td>
                                    <form method="post" style="display:inline;">
                                        <input type="hidden" name="keyword" value="<?php echo esc_attr($keyword); ?>" />
                                        <input type="hidden" name="keyword_action" value="enable" />
                                        <button type="submit" class="btn btn-primary btn-sm"<?php echo $is_enabled ? ' disabled' : ''; ?>>Enable</button>
                                    </form>
                                    <form method="post" style="display:inline;">
                                        <input type="hidden" name="keyword" value="<?php echo esc_attr($keyword); ?>" />
                                        <input type="hidden" name="keyword_action" value="disable" />
                                        <button type="submit" class="btn btn-secondary btn-sm"<?php echo !$is_enabled ? ' disabled' : ''; ?>>Disable</button>
                                    </form>
                                    <form method="post" style="display:inline;">
                                        <input type="hidden" name="remove_keyword" value="<?php echo esc_attr($keyword); ?>">
                                        <button type="submit" class="btn btn-danger  btn-sm ms-3">Remove</button>
                                    </form>
                                </td>
                            </tr>
                            <?php
                        }
                    }
                    ?>
                </tbody>
            </table>    
        </div>
    </body>
    <?php
}

function sb_register_settings() {
    register_setting('sb_settings', 'sb_keywords');
}

add_action('admin_init', 'sb_register_settings');

function sb_block_scripts($buffer) {
    global $plugin_enabled;

    if ($plugin_enabled) {
        $user_consent = isset($_COOKIE['sb_user_consent']) ? $_COOKIE['sb_user_consent'] : 'reject';
        $keywords = explode(',', get_option('sb_keywords'));

        if (empty($keywords)) {
            return $buffer;
        }

        foreach ($keywords as $keyword) {
            $keyword = trim($keyword);
            if (empty($keyword)) {
                continue;
            }
            $escaped_keyword = preg_quote($keyword, '/');

            if (get_option('sb_enable_blocking_' . $keyword, '0') == '1') {
                if ($user_consent === 'accept') {
                    $buffer = preg_replace_callback(
                        '/<script\s+(.*?)type=[\'"]?text\/plain[\'"]?(.*?)src=[\'"]?(.*?' . $escaped_keyword . '.*?)["\']?(.*?)>/',
                        function($matches) {
                            return '<script ' . $matches[1] . 'type="text/javascript" src="' . $matches[3] . '"' . $matches[4] . '>';
                        },
                        $buffer
                    );

                    $buffer = preg_replace_callback(
                        '/<script\s+type="text\/plain">(.*?' . $escaped_keyword . '.*?)<\/script>/is',
                        function($matches) {
                            return '<script type="text/javascript">' . $matches[1] . '</script>';
                        },
                        $buffer
                    );
                } else {
                    $buffer = preg_replace_callback(
                        '/<script\s+(.*?)type=[\'"]?text\/javascript[\'"]?(.*?)src=[\'"]?(.*?' . $escaped_keyword . '.*?)["\']?(.*?)>/',
                        function($matches) {
                            return '<script ' . $matches[1] . 'type="text/plain" src="' . $matches[3] . '"' . $matches[4] . '>';
                        },
                        $buffer
                    );

                    $buffer = preg_replace_callback(
                        '/<script\s*>(.*?' . $escaped_keyword . '.*?)<\/script>/is',
                        function($matches) {
                            return '<script type="text/plain">' . $matches[1] . '</script>';
                        },
                        $buffer
                    );
                }
            }
        }
    }
    return $buffer;
}

add_action('template_redirect', function() {
     global $plugin_enabled;
     $plugin_enabled = get_option('sb_plugin_enabled'); 
    ob_start('sb_block_scripts');
});

function sb_activate() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'sb_consent_log';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        consent varchar(10) NOT NULL,
        consent_time datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
        PRIMARY KEY (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);

    add_option('sb_keywords', '');
}
register_activation_hook(__FILE__, 'sb_activate');

function sb_deactivate() {
    delete_option('sb_keywords');
    $keywords = explode(',', get_option('sb_keywords', ''));
    foreach ($keywords as $keyword) {
        delete_option('sb_enable_blocking_' . $keyword);
    }
}
register_deactivation_hook(__FILE__, 'sb_deactivate');

function sb_handle_user_consent() {
    check_ajax_referer('sb_nonce', 'nonce');

    $consent = isset($_POST['consent']) ? sanitize_text_field($_POST['consent']) : '';

    if ($consent === 'accept') {
        setcookie('sb_user_consent', 'accept', time() + 365*24*60*60, COOKIEPATH, COOKIE_DOMAIN); 
    } else {
        setcookie('sb_user_consent', 'reject', time() + 365*24*60*60, COOKIEPATH, COOKIE_DOMAIN); 
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'sb_consent_log';
    $wpdb->insert(
        $table_name,
        array(
            'consent' => $consent,
        ),
        array(
            '%s'
        )
    );

    wp_send_json_success();
}

add_action('wp_ajax_sb_handle_user_consent', 'sb_handle_user_consent');
add_action('wp_ajax_nopriv_sb_handle_user_consent', 'sb_handle_user_consent');
?>