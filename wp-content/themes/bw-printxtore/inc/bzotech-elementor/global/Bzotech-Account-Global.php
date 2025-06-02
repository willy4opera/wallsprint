<?php
namespace Elementor;
if ( ! defined( 'ABSPATH' ) ) exit; 
/**
 * Elementor Account manager
 *
 * Elementor widget for Account manager
 *
 * @since 1.0.0
 */
class Bzotech_Account_Global extends Widget_Base {

	/**
	 * Retrieve the widget name.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'bzotech-account-global';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Account manager (Global)', 'bw-printxtore' );
	}

	/**
	 * Retrieve the widget icon.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-person';
	}

	/**
	 * Retrieve the list of categories the widget belongs to.
	 *
	 * Used to determine where to display the widget in the editor.
	 *
	 * Note that currently Elementor supports only one category.
	 * When multiple categories passed, Elementor uses the first one.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return [ 'aqb-htelement-category' ];
	}

	/**
	 * Retrieve the list of scripts the widget depended on.
	 *
	 * Used to set scripts dependencies required to run the widget.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @return array Widget scripts dependencies.
	 */
	public function get_script_depends() {
		return [ 'hello-world' ];
	}
	public function get_style_depends() {
		return [ 'bzotech-el-account' ];
	}
	public function get_widget_css_config( $widget_name ) { 
	    $file_content_css = get_template_directory() . '/assets/global/css/elementor/account.css';
	    if ( is_file( $file_content_css ) ) {
	        $file_content_css_content = file_get_contents( $file_content_css );
	        echo bzotech_add_inline_style_widget( $file_content_css_content, true );
	    }
	    $direction = is_rtl() ? '-rtl' : '';
	    $has_custom_breakpoints = $this->is_custom_breakpoints_widget();
	    $file_name = 'widget-' . $widget_name . $direction . '.min.css';
	    $file_url = Plugin::$instance->frontend->get_frontend_file_url( $file_name, $has_custom_breakpoints );
	    $file_path = Plugin::$instance->frontend->get_frontend_file_path( $file_name, $has_custom_breakpoints );
	    return [
	        'key' => $widget_name,
	        'version' => ELEMENTOR_VERSION,
	        'file_path' => $file_path,
	        'data' => [
	            'file_url' => $file_url,
	        ],
	    ];
	}
	public function get_roles() {
		global $wp_roles;
        $roles = array();
        if(isset($wp_roles->roles)){
            $roles_data = $wp_roles->roles;
            if(is_array($roles_data)){
                foreach ($roles_data as $key => $value) {
                    $roles[$key] = $value['name'];
                }
            }
        }
        return $roles;
	}

	public function login_form($redirect_to = '') {
		if(empty($redirect_to)) $redirect_to =  apply_filters( 'login_redirect',home_url('/'));
        echo '<input type="hidden" name="popup-form-account-ajax-nonce" class="popup-form-account-ajax-nonce" value="' . wp_create_nonce( 'popup-form-account-ajax-nonce' ) . '" />';
        ?>
        <div class="elbzotech-login-form popup-form active">
            <div class="form-header">
                <h2><?php esc_html_e( 'Sign In','bw-printxtore' ); ?></h2>
                
                <div class="message ms-done ms-default"><?php esc_html_e( 'Registration complete. Please check your email.','bw-printxtore' ); ?></div>
            </div>
            <form name="loginform" id="loginform" action="<?php echo esc_url( home_url( 'wp-login.php', 'login_post' ) ); ?>" method="post">
                <?php do_action( 'woocommerce_login_form_start' ); ?>
                <div class="form-field">
                    <input placeholder="<?php esc_attr_e( 'Username or Email Address','bw-printxtore' ); ?>" type="text" name="log" id="user_login" class="input" size="20" autocomplete="off"/>
                </div>
                <div class="form-field password-input">
                	<input placeholder="<?php esc_attr_e( 'Password','bw-printxtore' ); ?>" type="password" name="pwd" id="user_pass" class="input" value="" size="20" autocomplete="off"/>
                	
                </div>
                <div class="extra-field">
                    <?php 
                        if(class_exists("woocommerce")) do_action( 'woocommerce_login_form' );
                        else do_action( 'login_form' );
                    ?>
                </div>
                
                <div class="submit">
                    <input type="submit" name="wp-submit" class="elbzotech-bt-default elbzotech-bt-full" value="<?php esc_attr_e('Sign In','bw-printxtore'); ?>" />
                    <input type="hidden" name="redirect_to1" value="<?php echo esc_attr($redirect_to); ?>" />
                </div>
                <div class="nav-form flex-wrapper justify_content-space-between align_items-center">
                	<div class="forgetmenot">
	                    <input name="rememberme" type="checkbox" id="remembermep" value="forever" />
	                    <label class="rememberme" for="remembermep"><?php esc_html_e( 'Remember Me','bw-printxtore' ); ?></label>
	                </div>
	                <div class="registerform-lost-pass">
		                <?php if ( get_option( 'users_can_register' ) ) :
		                    echo '<a href="#registerform" class="popup-redirect register-link">'.esc_html__("Register",'bw-printxtore').'</a>';
		                endif;
		                echo '<a href="#lostpasswordform" class="popup-redirect lostpass-link">'.esc_html__("Lost your password?",'bw-printxtore').'</a>';
		                ?>
		            </div>
	            </div>
                
                <?php 
                if(class_exists('NextendSocialLogin', false)) {
                	echo '<div class="nextend-social-login"><h3 class="title20 font-medium text-center"><span>'.esc_html__('Or login With','bw-printxtore').'</span></h3>';
                	echo do_shortcode('[nextend_social_login login="1" link="1" unlink="1"] ');
                	echo'</div>';
                }

                	
			do_action( 'woocommerce_login_form_end' ); ?>
            </form>
            
        </div>
        <?php
	}

	public function register_form($redirect_to = '') {
		if(empty($redirect_to)) $redirect_to = apply_filters( 'registration_redirect', wp_login_url() );
		echo '<input type="hidden" name="popup-form-account-ajax-nonce" class="popup-form-account-ajax-nonce" value="' . wp_create_nonce( 'popup-form-account-ajax-nonce' ) . '" />';
        ?>
        <div class="elbzotech-register-form popup-form">
            <div class="form-header">
                <h2><?php esc_html_e( 'Create an account','bw-printxtore' ); ?></h2>
                <div class="message login_error ms-error ms-default"><?php esc_html_e( 'The user name or email address is not correct.','bw-printxtore' ); ?></div>
                
            </div>
            <form name="registerform" id="registerform" action="<?php echo esc_url( home_url( 'wp-login.php?action=register', 'login_post' ) ); ?>" method="post" novalidate="novalidate">
                <?php do_action( 'woocommerce_register_form_start' ); ?>
                <div class="form-field">
                    <input placeholder="<?php esc_attr_e('Username','bw-printxtore') ?>" type="text" name="user_login" id="user_loginr" class="input" value="" size="20" autocomplete="off"/>
                </div>
                <div class="form-field">
                    <input placeholder="<?php esc_attr_e('Email','bw-printxtore') ?>" type="email" name="user_email" id="user_email" class="input" value="" size="25" autocomplete="off"/>
                </div>
                <?php if ( 'no' === get_option( 'woocommerce_registration_generate_password' ) ){ ?>
                    <div class="form-field password-input">
                        <input placeholder="<?php esc_attr_e( 'Password', 'bw-printxtore' ); ?>" type="password" name="password" id="reg_passwordp" autocomplete="new-password" />
                    </div>
                <?php }?>
                <div class="extra-field">
                    <?php 
                        if(class_exists("woocommerce")) do_action( 'woocommerce_register_form' );
                        else do_action( 'register_form' );
                    ?>
                    <input type="hidden" name="redirect_to1" value="<?php echo esc_attr( $redirect_to ); ?>" />
                </div>                
                <?php if ( 'no' != get_option( 'woocommerce_registration_generate_password' ) ){ ?>
                    <div id="reg_passmail">
                        <?php esc_html_e( 'Registration confirmation will be emailed to you.','bw-printxtore' ); ?>
                    </div>
                <?php }?>
                <div class="submit"><input type="submit" name="wp-submit" class="elbzotech-bt-default elbzotech-bt-full" value="<?php esc_attr_e('Register','bw-printxtore'); ?>" /></div>
                <?php do_action( 'woocommerce_register_form_end' ); ?>
            </form>

            <div class="nav-form">
                <a href="#loginform" class="popup-redirect login-link"><?php esc_html_e( 'Log in','bw-printxtore' ); ?></a>
                <a href="#lostpasswordform" class="popup-redirect lostpass-link"><?php esc_html_e( 'Lost your password?','bw-printxtore' ); ?></a>
            </div>
        </div>
        <?php
	}
	public function lostpass_form($redirect_to = '') {
		if(empty($redirect_to)) $redirect_to =  apply_filters( 'login_redirect',home_url('/'));
		echo '<input type="hidden" name="popup-form-account-ajax-nonce" class="popup-form-account-ajax-nonce" value="' . wp_create_nonce( 'popup-form-account-ajax-nonce' ) . '" />';
        ?>
        <div class="elbzotech-lostpass-form popup-form">
            <div class="form-header">
                <h2><?php esc_html_e( 'Reset password','bw-printxtore' ); ?></h2>
                <div class="message ms-default ms-done"><?php esc_html_e( 'Password reset email has been sent.','bw-printxtore' ); ?></div>
                <div class="message login_error ms-error ms-default"><?php esc_html_e( 'The email could not be sent. Possible reason: your host may have disabled the mail function.','bw-printxtore' ); ?></div>
            </div>
            <form name="lostpasswordform" id="lostpasswordform" action="<?php echo esc_url( home_url( 'wp-login.php?action=lostpassword', 'login_post' ) ); ?>" method="post">
                <div class="form-field">
                    <input placeholder="<?php esc_attr_e( 'Username or Email Address','bw-printxtore' ); ?>" type="text" name="user_login" id="user_loginlp" class="input" value="" size="20" autocomplete="off"/>
                </div>
                <div class="extra-field">
                    <?php do_action( 'lostpassword_form' ); ?>
                    <input type="hidden" name="redirect_to1" value="<?php echo esc_attr( $redirect_to ); ?>" />
                </div>
                <div class="submit"><input type="submit" name="wp-submit" class="elbzotech-bt-default elbzotech-bt-full" value="<?php esc_attr_e('Get New Password','bw-printxtore'); ?>" /></div>
                <div class="desc note"><?php esc_html_e( 'A password will be e-mailed to you.','bw-printxtore' ); ?></div>
            </form>

            <div class="nav-form">
                <a href="#loginform" class="popup-redirect login-link"><?php esc_html_e('Log in','bw-printxtore') ?></a>
                <?php
                if ( get_option( 'users_can_register' ) ) :
                    echo '<a href="#registerform" class="popup-redirect register-link">'.esc_html__("Register",'bw-printxtore').'</a>';
                endif;
                ?>
            </div>
        </div>
        <?php
	}

	/**
	 * Register the widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 *
	 * @access protected
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_style',
			[
				'label' => esc_html__( 'Style', 'bw-printxtore' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'style',
			[
				'label' 	=> esc_html__( 'Style', 'bw-printxtore' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'default',
				'options'   => [
					'default'		=> esc_html__( 'Style 1 (Default)', 'bw-printxtore' ),
					'style2'		=> esc_html__( 'Style 2', 'bw-printxtore' ),
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_button',
			[
				'label' => esc_html__( 'Button', 'bw-printxtore' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'icon',
			[
				'label' => esc_html__( 'Icon', 'bw-printxtore' ),
				'type' => Controls_Manager::ICONS,
				'default' => [
					'value' => 'fas fa-user',
					'library' => 'solid',
				],
			]
		);

		$this->add_control(
			'icon_logged',
			[
				'label' => esc_html__( 'Icon logged', 'bw-printxtore' ),
				'type' => Controls_Manager::ICONS,
				'default' => [
					'value' => 'fas fa-user',
					'library' => 'solid',
				],
			]
		);

		$this->add_responsive_control(
			'align_icon',
			[
				'label' => esc_html__( 'Alignment', 'bw-printxtore' ),
				'type' => Controls_Manager::CHOOSE,
				'default'	=> '',
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', 'bw-printxtore' ),
						'icon' => 'eicon-text-align-left',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'bw-printxtore' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}}' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'account_bttext',
			[
				'label' => esc_html__( 'Add text', 'bw-printxtore' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Type your text to add search button', 'bw-printxtore' ),
			]
		);

		$this->add_control(
			'account_bttext_pos',
			[
				'label' => esc_html__( 'Text position', 'bw-printxtore' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'after-icon',
				'options' => [
					'after-icon'   => esc_html__( 'After icon', 'bw-printxtore' ),
					'before-icon'  => esc_html__( 'Before icon', 'bw-printxtore' ),
				],
				'condition' => [
					'account_bttext!' => '',
					'icon[value]!' => '',
				]
			]
		);
		$this->add_control(
			'account_bt_class_css',
			[
				'label' => esc_html__( 'Add class CSS', 'bw-printxtore' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_links',
			[
				'label' => esc_html__( 'Sub links dropdown', 'bw-printxtore' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);
		
		$repeater_text = new Repeater();
		$repeater_text->add_control(
			'icon',
			[
				'label' => esc_html__( 'Icon', 'bw-printxtore' ),
				'type' => Controls_Manager::ICONS,
				'default' => [
					'value' => 'fas fa-user',
					'library' => 'solid',
				],
			]
		);
		$repeater_text->add_control(
			'text', 
			[
				'label' => esc_html__( 'Text', 'bw-printxtore' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Enter text' , 'bw-printxtore' ),
				'label_block' => true,
			]
		);
		$repeater_text->add_control(
			'link',
			[
				'label' => esc_html__( 'Link', 'bw-printxtore' ),
				'type' => Controls_Manager::URL,
				'placeholder' => esc_html__( 'https://your-link.com', 'bw-printxtore' ),
				'show_external' => true,
				'default' => [
					'url' => '',
					'is_external' => false,
					'nofollow' => false,
				],
			]
		);

		$repeater_text->add_control(
			'roles',
			[
				'label' => esc_html__( 'Show with roles', 'bw-printxtore' ),
				'description' => esc_html__( 'Choose roles to show. Default is show with all roles', 'bw-printxtore' ),
				'type' => Controls_Manager::SELECT2,
				'multiple' => true,
				'options' => $this->get_roles(),
				'default' => [],
			]
		);

		$this->add_control(
			'list_links',
			[
				'label' => esc_html__( 'Add links', 'bw-printxtore' ),
				'type' => Controls_Manager::REPEATER,
				'prevent_empty'=>false,
				'fields' => $repeater_text->get_controls(),
				'title_field' => '{{{ text }}}',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_icon',
			[
				'label' => esc_html__( 'Button', 'bw-printxtore' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
		$start = is_rtl() ? 'right' : 'left';
		$end = is_rtl() ? 'left' : 'right';
		$this->add_responsive_control(
			'flex_direction_button_account',
			[
				'label' => esc_html__( 'Direction', 'bw-printxtore' ),
				'type' => Controls_Manager::CHOOSE,
				'responsive' => true,
				'label_block' => true,
				'options' => [
					'row' => [
						'title' => esc_html_x( 'Row - horizontal', 'Flex Container Control', 'bw-printxtore' ),
						'icon' => 'eicon-arrow-' . $end,
					],
					'column' => [
						'title' => esc_html_x( 'Column - vertical', 'Flex Container Control', 'bw-printxtore' ),
						'icon' => 'eicon-arrow-down',
					],
					'row-reverse' => [
						'title' => esc_html_x( 'Row - reversed', 'Flex Container Control', 'bw-printxtore' ),
						'icon' => 'eicon-arrow-' . $start,
					],
					'column-reverse' => [
						'title' => esc_html_x( 'Column - reversed', 'Flex Container Control', 'bw-printxtore' ),
						'icon' => 'eicon-arrow-up',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .button-account-e' => 'flex-direction: {{VALUE}};',
				],
				'default' => '',
			]
		);
		$this->add_responsive_control(
			'alignment',
			[
				'label' => esc_html__( 'Justify Content', 'bw-printxtore' ),
				'type' => Controls_Manager::CHOOSE,
				'responsive' => true,
				'label_block' => true,
				'options' => [
					'flex-start' => [
						'title' => esc_html_x( 'Start', 'Flex Container Control', 'bw-printxtore' ),
						'icon' => 'eicon-flex eicon-justify-start-h',
					],
					'center' => [
						'title' => esc_html_x( 'Center', 'Flex Container Control', 'bw-printxtore' ),
						'icon' => 'eicon-flex eicon-justify-center-h',
					],
					'flex-end' => [
						'title' => esc_html_x( 'End', 'Flex Container Control', 'bw-printxtore' ),
						'icon' => 'eicon-flex eicon-justify-end-h',
					],
					'space-between' => [
						'title' => esc_html_x( 'Space Between', 'Flex Container Control', 'bw-printxtore' ),
						'icon' => 'eicon-flex eicon-justify-space-between-h',
					],
					'space-around' => [
						'title' => esc_html_x( 'Space Around', 'Flex Container Control', 'bw-printxtore' ),
						'icon' => 'eicon-flex eicon-justify-space-around-h',
					],
					'space-evenly' => [
						'title' => esc_html_x( 'Space Evenly', 'Flex Container Control', 'bw-printxtore' ),
						'icon' => 'eicon-flex eicon-justify-space-evenly-h',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .button-account-e' => 'justify-content: {{VALUE}};',
				],
				'default' => '',
			]
		);
		$this->add_responsive_control(
			'align_items',
			[
				'label' => esc_html__( 'Align Items', 'bw-printxtore' ),
				'type' => Controls_Manager::CHOOSE,
				'responsive' => true,
				'options' => [
					'flex-start' => [
						'title' => esc_html_x( 'Start', 'Flex Container Control', 'bw-printxtore' ),
						'icon' => 'eicon-flex eicon-align-start-v',
					],
					'center' => [
						'title' => esc_html_x( 'Center', 'Flex Container Control', 'bw-printxtore' ),
						'icon' => 'eicon-flex eicon-align-center-v',
					],
					'flex-end' => [
						'title' => esc_html_x( 'End', 'Flex Container Control', 'bw-printxtore' ),
						'icon' => 'eicon-flex eicon-align-end-v',
					],
					'stretch' => [
						'title' => esc_html_x( 'Stretch', 'Flex Container Control', 'bw-printxtore' ),
						'icon' => 'eicon-flex eicon-align-stretch-v',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .button-account-e' => 'align-items: {{VALUE}};',
				],
				'default' => '',
			]
		);
		$this->add_responsive_control(
			'gap_item_button_account',
			[
				'label' => esc_html__( 'Gap', 'bw-printxtore' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .button-account-e' => 'gap: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'flex_wrap_button_account',
			[
				'label' => esc_html__( 'Wrap', 'bw-printxtore' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'nowrap' => [
						'title' => esc_html__( 'No Wrap', 'bw-printxtore' ),
						'icon' => 'eicon-flex eicon-nowrap',
					],
					'wrap' => [
						'title' => esc_html__( 'Wrap', 'bw-printxtore' ),
						'icon' => 'eicon-flex eicon-wrap',
					],
				],
				'description' => esc_html__(
					'Items within the container can stay in a single line (No wrap), or break into multiple lines (Wrap).','bw-printxtore'
				),
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .button-account-e' => 'flex-wrap: {{VALUE}};',
				],
				'responsive' => true,
			]
		);

		$this->add_responsive_control(
			'width_icon',
			[
				'label' => esc_html__( 'Width', 'bw-printxtore' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' , '%'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .button-account-e' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'height_icon',
			[
				'label' => esc_html__( 'Height', 'bw-printxtore' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .button-account-e' => 'line-height: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'size_icon',
			[
				'label' => esc_html__( 'Size icon', 'bw-printxtore' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .button-account-e i' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs( 'icon_account_effects' );

		$this->start_controls_tab( 'icon_account_normal',
			[
				'label' => esc_html__( 'Normal', 'bw-printxtore' ),
			]
		);

		$this->add_control(
			'color_icon',
			[
				'label' => esc_html__( 'Icon Color', 'bw-printxtore' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .button-account-e i' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'color_text',
			[
				'label' => esc_html__( 'Text Color', 'bw-printxtore' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .button-account-e .title-account-e' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'account_text_button_typography',
				'label' => esc_html__( 'Typography button text', 'bw-printxtore' ),
				'selector' => '{{WRAPPER}} .button-account-e .title-account-e',
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'background_icon',
				'label' => esc_html__( 'Background', 'bw-printxtore' ),
				'types' => [ 'classic', 'gradient', 'video' ],
				'selector' => '{{WRAPPER}} .button-account-e',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'shadow_icon',
				'exclude' => [
					'box_shadow_position',
				],
				'selector' => '{{WRAPPER}} .button-account-e',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'border_icon',
				'selector' => '{{WRAPPER}} .button-account-e',
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'border_icon',
			[
				'label' => esc_html__( 'Border Radius', 'bw-printxtore' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .button-account-e' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab( 'icon_account_hover',
			[
				'label' => esc_html__( 'Hover', 'bw-printxtore' ),
			]
		);

		$this->add_control(
			'color_icon_hover',
			[
				'label' => esc_html__( 'Icon Color', 'bw-printxtore' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .button-account-e:hover i' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'color_text_hover',
			[
				'label' => esc_html__( 'Text Color', 'bw-printxtore' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .button-account-e .title-account-e:hover' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'account_text_button_typography:hover',
				'label' => esc_html__( 'Typography button text', 'bw-printxtore' ),
				'selector' => '{{WRAPPER}} .button-account-e .title-account-e:hover',
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'background_icon_hover',
				'label' => esc_html__( 'Background', 'bw-printxtore' ),
				'types' => [ 'classic', 'gradient', 'video' ],
				'selector' => '{{WRAPPER}} .button-account-e:hover',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'shadow_icon_hover',
				'exclude' => [
					'box_shadow_position',
				],
				'selector' => '{{WRAPPER}} .button-account-e:hover',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'border_icon_hover',
				'selector' => '{{WRAPPER}} .button-account-e:hover',
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'border_icon_hover',
			[
				'label' => esc_html__( 'Border Radius', 'bw-printxtore' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .button-account-e:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();	

		$this->add_control(
			'separator_icon_popup',
			[
				'type' => Controls_Manager::DIVIDER,
				'style' => 'thick',
			]
		);

		$this->add_responsive_control(
			'padding_btn_account',
			[
				'label' => esc_html__( 'Padding', 'bw-printxtore' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px' ],
				'selectors' => [
					'{{WRAPPER}} .button-account-e' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'padding_btn_account_icon',
			[
				'label' => esc_html__( 'Padding icon', 'bw-printxtore' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px' ],
				'selectors' => [
					'{{WRAPPER}} .button-account-e i,{{WRAPPER}} .button-account-e img' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'margin_icon',
			[
				'label' => esc_html__( 'Margin', 'bw-printxtore' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px' ],
				'selectors' => [
					'{{WRAPPER}} .button-account-e' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_links',
			[
				'label' => esc_html__( 'Links', 'bw-printxtore' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'width_links',
			[
				'label' => esc_html__( 'Width', 'bw-printxtore' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' , '%'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elbzotech-dropdown-list' => 'width: {{SIZE}}{{UNIT}};max-width: inherit;',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'shadow_links',
				'exclude' => [
					'box_shadow_position',
				],
				'selector' => '{{WRAPPER}} .elbzotech-dropdown-list',
			]
		);

		$this->add_responsive_control(
			'space_links',
			[
				'label' => esc_html__( 'Space', 'bw-printxtore' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' , '%'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elbzotech-dropdown-list li' => 'line-height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
		$this->start_controls_section(
			'section_style_btn_icon',
			[
				'label' => esc_html__( 'Icon', 'bw-printxtore' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
		$this->get_style_type_icon();
		$this->end_controls_section();
	}

	/**
	 * Render the widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 *
	 * @access protected
	 */
	protected function render() {
		$settings = $this->get_settings();?>
		<div class="elbzotech-account-manager-global js-account-popup elbzotech-dropdown-box <?php echo 'elbzotech-account-global-'.esc_attr($settings['style'])?>">
			
			<?php 
			$attr = array(
				'wdata'		=> $this,
				'settings'	=> $settings,
			);
			echo bzotech_get_template_elementor_global('account/account',$settings['style'],$attr);
			
			if(is_user_logged_in()): ?>
				<?php if(!empty($settings['list_links'])){ ?>
					<ul class="elbzotech-dropdown-list">
				    	<?php 
				    	foreach (  $settings['list_links'] as $item ) {
							$target = $item['link']['is_external'] ? ' target="_blank"' : '';
							$nofollow = $item['link']['nofollow'] ? ' rel="nofollow"' : '';
							echo '<li><a href="'.esc_url($item['link']['url']).'"'.$target.$nofollow.' class="elementor-repeater-item-'.$item['_id'].'">';
							Icons_Manager::render_icon( $item['icon'], [ 'aria-hidden' => 'true' ] );
							echo apply_filters('bzotech_output_content',$item['text']);
							echo '</a></li>';
						}
				    	?>
				    </ul>
				<?php } ?>
				
			  <?php else:?>
			  	<div class="login-popup-content-wrap elbzotech-popup-overlay">
			  		
	                <div class="elbzotech-login-popup-content bzotech-scrollbar">
	                	<i class="la la-close elbzotech-close-popup"></i>
	                    <?php
	                    $this->login_form();
	                    $this->register_form();
	                    $this->lostpass_form();
	                    ?>
	                </div>
	                <div class="popup-overlay"></div>
	            </div>
			<?php endif;?>
		</div>
		<?php
	}

	/**
	 * Render the widget output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @since 1.0.0
	 *
	 * @access protected
	 */
	protected function content_template() {
		
	}
	public function get_style_type_icon($key='icon',$class="item-icon-e") {
		$this->start_controls_tabs( $key.'_tabs_style' );
		$this->start_controls_tab(
			$key.'_tab_normal',
			[
				'label' => esc_html__( 'Normal Style', 'bw-printxtore' ),
			]
		);
		$this->add_responsive_control(
			$key.'_size_css',
			[
				'label' => esc_html__( 'Font Size', 'bw-printxtore' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'range' => [
					'px' => [
						'max' => 200,
						'min' => 0,
						'step' => 1,
					],
					'em' => [
						'max' => 200,
						'min' => 0,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .'.$class => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			$key.'_color_css',
			[
				'label' => esc_html__( 'Color', 'bw-printxtore' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .'.$class => 'color: {{VALUE}}',
					'{{WRAPPER}} .'.$class.' .sub-color-e' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			$key.'bg_color_css',
			[
				'label' => esc_html__( 'Background Color', 'bw-printxtore' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .'.$class => 'background-color: {{VALUE}};',
				],
			]
		);
		$this->add_responsive_control(
			$key.'_opacity_css',
			[
				'label' => esc_html__( 'Opacity', 'bw-printxtore' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 1,
						'min' => 0,
						'step' => 0.01,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .'.$class => 'opacity: {{SIZE}};',
				],
			]
		);
		$this->add_responsive_control(
			$key.'_padding_css',
			[
				'label' => esc_html__( 'Padding', 'bw-printxtore' ),
				'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
				'selectors' => [
					'{{WRAPPER}} .'.$class => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
			]
        );

        $this->add_responsive_control(
			$key.'_margin_css',
			[
				'label' => esc_html__( 'Margin', 'bw-printxtore' ),
				'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
				'selectors' => [
					'{{WRAPPER}} .'.$class => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
        );
        $this->add_responsive_control(
			$key.'_width_css',
			[
				'label' => esc_html__( 'Width', 'bw-printxtore' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
				'range' => [
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .'.$class => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			$key.'_height_css',
			[
				'label' => esc_html__( 'Hight', 'bw-printxtore' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vh', 'custom' ],
				'default' => [
					'unit' => 'px',
				],
				'range' => [
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .'.$class => 'height: {{SIZE}}{{UNIT}};',
				],
				'separator' => 'after',
			]
		);
		$start = is_rtl() ? 'right' : 'left';
		$end = is_rtl() ? 'left' : 'right';
		$this->add_responsive_control(
			$key.'_flex_direction',
			[
				'label' => esc_html__( 'Direction', 'bw-printxtore' ),
				'type' => Controls_Manager::CHOOSE,
				'responsive' => true,
				'label_block' => true,
				'options' => [
					'row' => [
						'title' => esc_html_x( 'Row - horizontal', 'Flex Container Control', 'bw-printxtore' ),
						'icon' => 'eicon-arrow-' . $end,
					],
					'column' => [
						'title' => esc_html_x( 'Column - vertical', 'Flex Container Control', 'bw-printxtore' ),
						'icon' => 'eicon-arrow-down',
					],
					'row-reverse' => [
						'title' => esc_html_x( 'Row - reversed', 'Flex Container Control', 'bw-printxtore' ),
						'icon' => 'eicon-arrow-' . $start,
					],
					'column-reverse' => [
						'title' => esc_html_x( 'Column - reversed', 'Flex Container Control', 'bw-printxtore' ),
						'icon' => 'eicon-arrow-up',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .'.$class => 'flex-direction: {{VALUE}};',
				],
				'default' => '',
			]
		);
		$this->add_responsive_control(
			$key.'_alignment',
			[
				'label' => esc_html__( 'Justify Content', 'bw-printxtore' ),
				'type' => Controls_Manager::CHOOSE,
				'responsive' => true,
				'label_block' => true,
				'options' => [
					'flex-start' => [
						'title' => esc_html_x( 'Start', 'Flex Container Control', 'bw-printxtore' ),
						'icon' => 'eicon-flex eicon-justify-start-h',
					],
					'center' => [
						'title' => esc_html_x( 'Center', 'Flex Container Control', 'bw-printxtore' ),
						'icon' => 'eicon-flex eicon-justify-center-h',
					],
					'flex-end' => [
						'title' => esc_html_x( 'End', 'Flex Container Control', 'bw-printxtore' ),
						'icon' => 'eicon-flex eicon-justify-end-h',
					],
					'space-between' => [
						'title' => esc_html_x( 'Space Between', 'Flex Container Control', 'bw-printxtore' ),
						'icon' => 'eicon-flex eicon-justify-space-between-h',
					],
					'space-around' => [
						'title' => esc_html_x( 'Space Around', 'Flex Container Control', 'bw-printxtore' ),
						'icon' => 'eicon-flex eicon-justify-space-around-h',
					],
					'space-evenly' => [
						'title' => esc_html_x( 'Space Evenly', 'Flex Container Control', 'bw-printxtore' ),
						'icon' => 'eicon-flex eicon-justify-space-evenly-h',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .'.$class => 'justify-content: {{VALUE}};',
				],
				'default' => '',
			]
		);

		$this->add_responsive_control(
			$key.'align_items',
			[
				'label' => esc_html__( 'Align Items', 'bw-printxtore' ),
				'type' => Controls_Manager::CHOOSE,
				'responsive' => true,
				'options' => [
					'flex-start' => [
						'title' => esc_html_x( 'Start', 'Flex Container Control', 'bw-printxtore' ),
						'icon' => 'eicon-flex eicon-align-start-v',
					],
					'center' => [
						'title' => esc_html_x( 'Center', 'Flex Container Control', 'bw-printxtore' ),
						'icon' => 'eicon-flex eicon-align-center-v',
					],
					'flex-end' => [
						'title' => esc_html_x( 'End', 'Flex Container Control', 'bw-printxtore' ),
						'icon' => 'eicon-flex eicon-align-end-v',
					],
					'stretch' => [
						'title' => esc_html_x( 'Stretch', 'Flex Container Control', 'bw-printxtore' ),
						'icon' => 'eicon-flex eicon-align-stretch-v',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .'.$class => 'align-items: {{VALUE}};',
				],
				'default' => '',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => $key.'_border_css',
				'selector' => '{{WRAPPER}} .'.$class,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			 $key.'_border_radius_css',
			[
				'label' => esc_html__( 'Border Radius', 'bw-printxtore' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
				'selectors' => [
					'{{WRAPPER}} .'.$class => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' =>  $key.'_box_shadow_css',
				'exclude' => [
					'box_shadow_position',
				],
				'selector' => '{{WRAPPER}} .'.$class,
			]
		);
		$this->end_controls_tab();

		$this->start_controls_tab(
			$key.'_tab_hover',
			[
				'label' => esc_html__( 'Hover Style', 'bw-printxtore' ),
			]
		);
		$this->add_responsive_control(
			$key.'_size_hover_css',
			[
				'label' => esc_html__( 'Size On Hover ', 'bw-printxtore' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'range' => [
					'px' => [
						'max' => 200,
						'min' => 0,
						'step' => 1,
					],
					'em' => [
						'max' => 200,
						'min' => 0,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .'.$class.':hover' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			$key.'_color_hover_css',
			[
				'label' => esc_html__( 'Color On Hover', 'bw-printxtore' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .'.$class.':hover,{{WRAPPER}} .'.$class.':focus' => 'color: {{VALUE}}',
				],
			]
		);
		
		$this->add_control(
			$key.'_bg_hover_css',
			[
				'label' => esc_html__( 'Background Color On Hover', 'bw-printxtore' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .'.$class.':hover, {{WRAPPER}} .'.$class.':focus' => 'background-color: {{VALUE}};',
				],
			]
		);
		$this->add_responsive_control(
			$key.'_opacity_hover_css',
			[
				'label' => esc_html__( 'Opacity On Hover', 'bw-printxtore' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 1,
						'min' => 0,
						'step' => 0.01,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .'.$class.':hover' => 'opacity: {{SIZE}};',
				],
			]
		);
		$this->add_control(
			$key.'_hover_transition_css',
			[
				'label' => esc_html__( 'Transition Duration On Hover', 'bw-printxtore' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 5,
						'step' => 0.1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => '',
				],
				'selectors' => [
					'{{WRAPPER}}  .'.$class => 'transition-duration: {{SIZE}}s',
				],
			]
		);

		$this->add_responsive_control(
			$key.'_transition_css',
			[
				'label' => esc_html__( 'Transition', 'bw-printxtore' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 5,
						'step' => 0.1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .'.$class => 'transition: all {{SIZE}}s ease-out {{SIZE}}s;',
				],
			]
		);

		$this->add_control(
			$key.'_animation_hover_css',
			[
				'label' => esc_html__( 'Animation On Hover', 'bw-printxtore' ),
				'type' => Controls_Manager::HOVER_ANIMATION,
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => $key.'_border_css_hover',
				'label' => esc_html__( 'Border Hover', 'bw-printxtore' ),
				'selector' => '{{WRAPPER}} .'.$class.':hover',
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			 $key.'_border_radius_css_hover',
			[
				'label' => esc_html__( 'Border Radius Hover', 'bw-printxtore' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
				'selectors' => [
					'{{WRAPPER}} .'.$class.':hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' =>  $key.'_box_shadow_css_hover',
				'label' => esc_html__( 'Box Shadow Hover', 'bw-printxtore' ),
				'exclude' => [
					'box_shadow_position',
				],
				'selector' => '{{WRAPPER}} .'.$class.':hover',
			]
		);
		$this->end_controls_tab();/*End Hover Style*/
		$this->end_controls_tabs();
	}
}
