<?php
class AklamatorTwitchPrWidget
{



    private static $instance = null;

    /**
     * Get singleton instance
     */
    public static function init()
    {

        if (self::$instance == null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public $aklamator_url;
    public $api_data;
    protected $application_id;

    public function __construct()
    {


        $this->aklamator_url = "https://aklamator.com/";
//        $this->aklamator_url = "http://127.0.0.1/aklamator/www/";
        $this->application_id = get_option('aklamatorTwitchApplicationID');

        $this->hooks();

    }

    private function hooks(){

        add_filter( 'plugin_row_meta', array($this, 'aklamatorTwitch_plugin_meta_links'), 10, 2);
        add_filter( "plugin_action_links_".AKLATWITCH_PR_PLUGIN_NAME, array($this, 'aklamatorTwitch_plugin_settings_link') );
        
        
        
        if ($this->application_id != "")
            add_filter('the_content', array($this, 'bottom_of_twitch_every_post'));
            
        

        add_action( 'admin_menu', array($this,"adminMenu") );
        add_action( 'admin_init', array($this,"setOptions") );
        add_action( 'admin_enqueue_scripts', array($this, 'load_custom_twitch_admin_style_script') );
        add_action( 'after_setup_theme', array($this,'vw_setup_vw_widgets_init_aklamatorTwitch') );
    }

    function setOptions()
    {

        register_setting('aklamatorTwitch-options', 'aklamatorTwitchApplicationID');
        register_setting('aklamatorTwitch-options', 'aklamatorTwitchChannelName');
        register_setting('aklamatorTwitch-options', 'aklamatorTwitchGameName');
        register_setting('aklamatorTwitch-options', 'aklamatorTwitchPoweredBy');
        register_setting('aklamatorTwitch-options', 'aklamatorTwitchSingleWidgetID');
        register_setting('aklamatorTwitch-options', 'aklamatorTwitchPageWidgetID');
        register_setting('aklamatorTwitch-options', 'aklamatorTwitchSingleWidgetTitle');

    }


    function aklamatorTwitch_plugin_settings_link($links) {
        $settings_link = '<a href="admin.php?page=aklamator-twitch-plugin">Settings</a>';
        array_unshift($links, $settings_link);
        return $links;
    }

    /*
     * Activation Hook
     */
    function set_up_options() {
        add_option('aklamatorTwitchApplicationID', '');
        add_option('aklamatorTwitchChannelName', '');
        add_option('aklamatorTwitchGameName', '');
        add_option('aklamatorTwitchPoweredBy', '');
        add_option('aklamatorTwitchSingleWidgetID', '');
        add_option('aklamatorTwitchPageWidgetID', '');
        add_option('aklamatorTwitchSingleWidgetTitle', '');
        add_option('aklamatorTwitchWidgets', '');

    }

    /*
     * Uninstall Hook
     */
    function aklamator_uninstall() {
        delete_option('aklamatorTwitchApplicationID');
        delete_option('aklamatorTwitchChannelName');
        delete_option('aklamatorTwitchGameName');
        delete_option('aklamatorTwitchPoweredBy');
        delete_option('aklamatorTwitchSingleWidgetID');
        delete_option('aklamatorTwitchPageWidgetID');
        delete_option('aklamatorTwitchSingleWidgetTitle');
        delete_option('aklamatorTwitchWidgets');

    }

    /*
     * Add rate and review link in plugin section
     */
    function aklamatorTwitch_plugin_meta_links($links, $file)
    {
        $plugin = AKLATWITCH_PR_PLUGIN_NAME;
        // create link
        if ($file == $plugin) {
            return array_merge(
                $links,
                array('<a href="https://wordpress.org/support/plugin/aklamator-digital-pr/reviews" target=_blank>Please rate and review</a>')
            );
        }
        return $links;
    }

    public function adminMenu()
    {
        add_menu_page('Aklamator Twitch', 'Aklamator Twitch', 'manage_options', 'aklamator-twitch-plugin', array($this, 'createAdminPage'), AKLATWITCH_PR_PLUGIN_URL . 'images/aklamator-icon.png');
    }


    public function getSignupUrl()
    {
        $user_info =  wp_get_current_user();

        return $this->aklamator_url . 'login/application_id?utm_source=wordpress&utm_medium=wptwitch&e=' . urlencode(get_option('admin_email')) .
        '&pub=' .  preg_replace('/^www\./','',$_SERVER['SERVER_NAME']).
        '&un=' . urlencode($user_info->user_login). '&fn=' . urlencode($user_info->user_firstname) . '&ln=' . urlencode($user_info->user_lastname) .
        '&pl=twitch&return_uri=' . admin_url("admin.php?page=aklamator-twitch-plugin");

    }

    function load_custom_twitch_admin_style_script($hook) {

        if ( 'toplevel_page_aklamator-twitch-plugin' != $hook ) {
            return;
        }

        /*
         * We are calling api only when we at this plugin page, not for all other pages
         */

     
        if ($this->application_id !== '') {
            $this->api_data = $this->addNewTwitchWebsiteApi();

            $this->populate_with_default();

            if ($this->api_data->flag) {
                update_option('aklamatorTwitchWidgets', $this->api_data);
            }
        }
        

        // Load necessary css files
        wp_enqueue_style('custom-wp-admin', AKLATWITCH_PR_PLUGIN_URL . 'assets/css/admin-style.css', false, '1.0.0' );
        wp_enqueue_style('dataTables-plugin', AKLATWITCH_PR_PLUGIN_URL . 'assets/dataTables/jquery.dataTables.min.css', false, '1.10.5', false );

        // Load script files
        wp_enqueue_script('dataTables_plugin', AKLATWITCH_PR_PLUGIN_URL . 'assets/dataTables/jquery.dataTables.min.js', array('jquery'), '1.10.5', true );
        wp_register_script('my_custom_akla_script', AKLATWITCH_PR_PLUGIN_URL . 'assets/js/main.js', array('jquery'), '1.0', true);

        $data = array(
            'site_url' => $this->aklamator_url
        );
        wp_localize_script('my_custom_akla_script', 'akla_vars', $data);
        wp_enqueue_script('my_custom_akla_script');

    }

    private function populate_with_default(){

        if(isset($this->api_data->data) && $this->api_data->flag){

            if (get_option('aklamatorTwitchSingleWidgetID') !== 'none') {

                if (get_option('aklamatorTwitchSingleWidgetID') == '') {
                    if (isset($this->api_data->data)) {
                        $selected = "";
                        foreach ($this->api_data->data as $item) {
                            if ($item->title == 'Initial Twitch widget created') {
                                $selected = $item->uniq_name;
                            }
                        }
                        if ($selected != "") {
                            update_option('aklamatorTwitchSingleWidgetID', $selected);
                        } else {
                            update_option('aklamatorTwitchSingleWidgetID', $this->api_data->data[0]->uniq_name);
                        }

                    }
                }
            }

            if (get_option('aklamatorTwitchPageWidgetID') !== 'none') {

                if (get_option('aklamatorTwitchPageWidgetID') == '') {
                    if (isset($this->api_data->data)) {
                        $selected = "";
                        foreach ($this->api_data->data as $item) {
                            if ($item->title == 'Initial Twitch widget created') {
                                $selected = $item->uniq_name;
                            }
                        }
                        if ($selected != "") {
                            update_option('aklamatorTwitchPageWidgetID', $selected);
                        } else {
                            update_option('aklamatorTwitchPageWidgetID', $this->api_data->data[0]->uniq_name);
                        }

                    }
                }
            }
        }
    }

    function bottom_of_twitch_every_post($content){

        /*  we want to change `the_content` of posts, not pages
            and the text file must exist for this to work */

        if (is_single()){
            $widget_id = get_option('aklamatorTwitchSingleWidgetID');
        }elseif (is_page()) {
            $widget_id = get_option('aklamatorTwitchPageWidgetID');
        }else{

            /*  if `the_content` belongs to a page or our file is missing
                the result of this filter is no change to `the_content` */

            return $content;
        }

        $return_content = $content;

        if(strlen($widget_id) >=7){
            $title = "";
            if(get_option('aklamatorTwitchSingleWidgetTitle') !== ''){
                $title .= "<h2>". get_option('aklamatorTwitchSingleWidgetTitle'). "</h2>";
            }
            /*  append the text file contents to the end of `the_content` */

            $return_content.=  $title. $this->show_twitch_widget($widget_id);
        }

        return $return_content;
    }

    public function show_twitch_widget($widget_id){

        $code  = '<!-- Start aklamatorTwitch Widget -->';
        $code .= '<div id="akla'.$widget_id.'"></div>';
        $code .= '<script>(function(d, s, id) ';
        $code .= '{ var js, fjs = d.getElementsByTagName(s)[0];';
        $code .= 'if (d.getElementById(id)) return;';
        $code .= 'js = d.createElement(s); js.id = id;';
        $code .= 'js.src = "'.$this->aklamator_url.'widget/'.$widget_id.'";';
        $code .= 'fjs.parentNode.insertBefore(js, fjs);';
        $code .= '}(document, \'script\', \'aklamatorTwitch-'.$widget_id.'\'))</script>';
        $code .= '<!-- end -->';
        return $code;

    }

    public function show_twitch_widgetw($widget_id){

        $code  = '<!-- Start aklamatorTwitch Widget -->';
        $code .= '<div id="akla'.$widget_id.'"></div>';
        $code .= '<script>(function(d, s, id) ';
        $code .= '{ var js, fjs = d.getElementsByTagName(s)[0];';
        $code .= 'if (d.getElementById(id)) return;';
        $code .= 'js = d.createElement(s); js.id = id;';
        $code .= 'js.src = "'.$this->aklamator_url.'widget/'.$widget_id.'";';
        $code .= 'fjs.parentNode.insertBefore(js, fjs);';
        $code .= '}(document, \'script\', \'aklamatorWoos-'.$widget_id.'\'))</script>';
        $code .= '<!-- end -->';
        return $code;

    }

    private function addNewTwitchWebsiteApi()
    {

        if (!is_callable('curl_init')) {
            return;
        }

        $service =$this->aklamator_url . "wp-authenticate/user";
        $p['ip'] = $_SERVER['REMOTE_ADDR'];
        $p['domain'] = site_url();
        $p['source'] = "wordpress";
        $p['AklamatorApplicationID'] = get_option('aklamatorTwitchApplicationID');
        $p['aklamatorTwitchChannelName'] = get_option("aklamatorTwitchChannelName");
        $p['aklamatorTwitchGameName'] = get_option("aklamatorTwitchGameName");

        $data = wp_remote_post( $service, array(
                'method' => 'POST',
                'timeout' => 45,
                'redirection' => 5,
                'httpversion' => '1.0',
                'blocking' => true,
                'headers' => array(),
                'body' => $p,
                'cookies' => array()
            )
        );

        $ret_info = new stdClass();
        if(is_wp_error($data))
        {
            $this->curlfailovao=1;
        }
        else
        {
            $this->curlfailovao=0;
            $ret_info = json_decode($data['body']);
        }

        return $ret_info;

    }

    public function createAdminPage()
    {
       require_once AKLATWITCH_PR_PLUGIN_DIR."views/admin-page.php";
    }

    function vw_setup_vw_widgets_init_aklamatorTwitch() {
        add_action( 'widgets_init', array($this, 'vw_widgets_init_aklamatorTwitch') );
    }
    
    function vw_widgets_init_aklamatorTwitch() {
        register_widget( 'Wp_widget_aklamatorTwitch' );
    }
}