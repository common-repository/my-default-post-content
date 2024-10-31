<?php
/*
Plugin Name: My Default Post Content
Version: 0.7.3
Plugin URI: http://www.dcac.com/go/DefaultPostContent
Description: Sets default title and content for new blog posts.
Author: Denny Cherry & Associates Consulting
Author URI: http://www.dcac.com/
*/

class defaultpostcontent {

function activation() {

    // Default options
    $options = array (
	'title' => '',
	'content' => '',
	'donate' => '',
	'page_title' => '',
	'page_content' => '',
	
    );

    add_option('defaultpostcontent_options', $options);
}


// Register settings, add sections and fields
function admin_init(){
    register_setting( 'defaultpostcontent_options', 'defaultpostcontent_options', array($this, 'admin_validate'));
    add_settings_section('defaultpostcontent_main', __( 'Default Post Settings', '' ), array($this, 'post_section'), 'defaultpostcontent');
    add_settings_field('title', __( 'Default Post Title: ', '' ), array($this, 'post_title'), 'defaultpostcontent', 'defaultpostcontent_main');
    add_settings_field('content', __( 'Default Post Content: ', ''), array($this, 'post_content'), 'defaultpostcontent', 'defaultpostcontent_main');
	
	add_settings_section('defaultpostcontent_page_main', __( 'Default Page Settings', '' ), array($this, 'page_section'), 'defaultpostcontent');
    add_settings_field('page_title', __( 'Default Page Title: ', '' ), array($this, 'page_title'), 'defaultpostcontent', 'defaultpostcontent_page_main');
    add_settings_field('page_content', __( 'Default Page Content: ', ''), array($this, 'page_content'), 'defaultpostcontent', 'defaultpostcontent_page_main');

// This setting should always be last. Don't move it up.
    add_settings_field('donate', __( '', ''), array($this, 'admin_donate'), 'defaultpostcontent', 'defaultpostcontent_page_main');
}

function page_section() {
    echo '<p>' . __( 'Please enter your default page settings.', '' ) . '</p>';
}
	
function post_section() {
    echo '<p>' . __( 'Please enter your default blog post settings.', '' ) . '</p>';
}

function admin_menu() {
     add_submenu_page('options-general.php', 'My Default Post Content Settings', 'My Default Post Content', 
'manage_options', 'defaultpostcontent', array($this, 'options_page'));

}

// Display options page
function options_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( __('You are not allowed to access this part of the site') );
	}

    ?>
    <div class="wrap">
    <h2>Default Settings</h2>
        
        <form action="options.php" method="post">
            <?php settings_fields('defaultpostcontent_options'); ?>
            <?php do_settings_sections('defaultpostcontent'); ?>
            <p class="submit">
                <input name="submit" type="submit" class="button-primary" value="Save Changes" />
            </p>
        </form>
    </div>
    <?php
}


function post_title () {
	$options = get_option('defaultpostcontent_options');
	echo "<input id='title' name='defaultpostcontent_options[title]' type='text' class='regular-text' value='{$options['title']}' />";
}

function post_content () {
	$options = get_option('defaultpostcontent_options');
	echo "<textarea id='content' name='defaultpostcontent_options[content]' rows='5' cols='60'/>{$options['content']}</textarea>";
}

function page_title () {
	$options = get_option('defaultpostcontent_options');
	echo "<input id='title' name='defaultpostcontent_options[page_title]' type='text' class='regular-text' value='{$options['page_title']}' />";
}

function page_content () {
	$options = get_option('defaultpostcontent_options');
	echo "<textarea id='content' name='defaultpostcontent_options[page_content]' rows='5' cols='60'/>{$options['page_content']}</textarea>";
}

	
function admin_donate() {
    $options = get_option('defaultpostcontent_options');
    if (empty($options['donate'])) {
        echo "<input id='donate' name='defaultpostcontent_options[donate]' type='checkbox' value='yes'/> I have <a href=\"http://dcac.co/go/DefaultPostContent\">donated</a> to the support of this plugin.";
    } else {
        echo "<input id='donate' name='defaultpostcontent_options[donate]' type='hidden' value='yes'/>";
    }

}

function admin_validate( $input) {

	return $input;
}

function return_title($content) {
	$options = get_option('defaultpostcontent_options');
	
	$post_type = get_post_type( );
	
	if (!isset($post_type)) {
		$post_type=$_GET['post_type'];
	}
	
	if ($post_type == 'page') {
		$content = $options['page_title'];
	} else { //Post content
		$content = $options['title'];
	}
	
	return $content;
}

function return_content ($content) {
	$options = get_option('defaultpostcontent_options');
	
	$post_type = get_post_type( );
	
	if (!isset($post_type)) {
		if (isset($_GET['post_type'])) {
			$post_type=$_GET['post_type'];
		}
	}
	
	if ($post_type == 'page') {
		$content = $options['page_content'];
	} else { //Post content
		$content = $options['content'];
	}

	return $content;
}

// Add "Settings" link to the plugins page

function pluginmenu ($links, $file) {
    if ( $file != plugin_basename( __FILE__ ))
        return $links;

	$options = get_option('defaultpostcontent_options');
	if (empty($options['donate'])) {
		$links[] = '<a href="http://dcac.co/go/DefaultPostContent">' . __('Donate','') . '</a>';
	}

	return $links;
}

function action_links( $links, $file ) {
    if ( $file != plugin_basename( __FILE__ ))
        return $links;

    $settings_link = sprintf( '<a href="options-general.php?page=defaultpostcontent">%s</a>', __( 'Settings', '' ) );

    array_unshift( $links, $settings_link );

    return $links;
}



}


$MyClass = new defaultpostcontent();

register_activation_hook(__FILE__, array($MyClass, 'activation'));


add_filter( 'default_title', array($MyClass, 'return_title') );
add_filter( 'default_content', array($MyClass, 'return_content') );
add_filter('plugin_action_links', array($MyClass, 'action_links'),10,2);
add_filter('plugin_row_meta', array($MyClass, 'pluginmenu'),10,2);

add_action('admin_init',array($MyClass, 'admin_init'));
add_action('admin_menu', array($MyClass, 'admin_menu'));