<?php 

add_action( 'wp_enqueue_scripts', 'theme_enqueue_styles' );
function theme_enqueue_styles() {
    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
    wp_enqueue_style( 'child-style',
        get_stylesheet_directory_uri() . '/style.css',
        array('parent-style')
    );
}

function custom_login_form_func($atts) {
	
	if(!is_user_logged_in()) {
		$output = '<div class="login_form">
			<div class="login_wrap">
				<!-- <div class="header_text">
					<p>This is Photoshop\'s version  of Lorem Ipsum. Proin gravida nibh vel velit auctor aliquet. Aenean sollicitudin, lorem quis bibendum auctor, nisi elit consequat ipsum, nec sagittis sem nibh id elit. </p>
				</div> -->
				<div class="clearfix"></div>
				<div class="main_form">
					<div class="sign_in">
						<h2>SIGN IN</h2>
						<form id="login" action="login" method="post">
						<p class="status"></p>
						  <input type="text" name="username" id="user_login" value="" placeholder="Username"/>
						  <input type="password" name="pwd" id="user_pass" placeholder="Password"/>
						  <input type="submit" value="SIGN IN" class="btn btn_red"/>
						  <span><a href="'.wp_lostpassword_url().'" title="Password Lost and Found" class="forgot_pass">Forgot your password?</a></span>
						  '.wp_nonce_field( 'ajax-login-nonce', 'security' ).'
						</form>
					</div>
					<div class="sign_up">
					    <h2>NOT A MEMBER YET?</h2>
						<p class="red">Become a member by signing up</p>
						<p>Find out more by click the Sign Up button below.</p>
						<a href="http://aussietaxtime.com" class="btn btn_black">sign up</a>
					</div>
				</div>
			</div>
		</div>';
		
	} else {
		$dashboard = home_url() . '/wp-admin';
		$output = header('Location: '.$dashboard);
	}
		return $output;
	
}

add_shortcode('custom_login_form', 'custom_login_form_func');

function ajax_login(){

    // First check the nonce, if it fails the function will break
    check_ajax_referer( 'ajax-login-nonce', 'security' );

    // Nonce is checked, get the POST data and sign user on
    $info = array();
    $info['user_login'] = $_POST['username'];
    $info['user_password'] = $_POST['password'];
    $info['remember'] = true;

    $user_signon = wp_signon( $info, false );
    if ( is_wp_error($user_signon) ){
        echo json_encode(array('loggedin'=>false, 'message'=>__('Invalid Login Info')));
    } else {
        echo json_encode(array('loggedin'=>true, 'message'=>__('Login successful, redirecting...')));
    }

    die();
}


function ajax_login_init(){

    wp_register_script('ajax-login-script', get_template_directory_uri() . '/js/ajax-login-script.js', array('jquery') ); 
    wp_enqueue_script('ajax-login-script');

    wp_localize_script( 'ajax-login-script', 'ajax_login_object', array( 
        'ajaxurl' => admin_url( 'admin-ajax.php' ),
        'redirecturl' => home_url() . '/wp-admin',
        'loadingmessage' => __('Checking info, please wait...')
    ));

    // Enable the user with no privileges to run ajax_login() in AJAX
    add_action( 'wp_ajax_nopriv_ajaxlogin', 'ajax_login' );
}

// Execute the action only if the user isn't logged in
if (!is_user_logged_in()) {
    add_action('init', 'ajax_login_init');
}