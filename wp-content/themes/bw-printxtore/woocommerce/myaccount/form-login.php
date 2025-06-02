<?php
/**
 * Login Form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/form-login.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 9.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; 
}

do_action( 'woocommerce_before_customer_login_form' ); ?>
<div class="myaccount_registration_login tab-wrap">
    <div class="bzotech-row">
        <div class="bzotech-col-lg-6 bzotech-col-md-8 bzotech-col-sm-12 bzotech-col-xs-12">
            <div class="myaccount-tab-title">
                <ul class="list-none list-inline" role="tablist">
                    <li class="active">
                        <a class="title20 color-title text-uppercase" href="#tab-login" data-toggle="tab" aria-expanded="false">
                            <?php esc_html_e( 'Login', 'bw-printxtore' ); ?></a>
                    </li>
                    <?php if ( 'yes' === get_option( 'woocommerce_enable_myaccount_registration' ) ) : ?>
                        <li><a class="title20 color-title text-uppercase" href="#tab-register" data-toggle="tab" aria-expanded="false">
                                <?php esc_html_e( 'Register', 'bw-printxtore' ); ?></a></li>
                    <?php endif; ?>
                </ul>
            </div>
            <div class="myaccount-tab-content">
                <div class="tab-content">
                    <div class="tab-pane active" id="tab-login">
                        <form class="woocommerce-form woocommerce-form-login login" method="post">

                            <?php do_action( 'woocommerce_login_form_start' ); ?>

                            <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                                <input type="text" placeholder="<?php echo esc_attr__('Username or email address*','bw-printxtore')?>" class="woocommerce-Input woocommerce-Input--text input-text" name="username" id="username" autocomplete="username" value="<?php echo ( ! empty( $_POST['username'] ) ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; ?>" />
                            </p>
                            <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                                <input class="woocommerce-Input woocommerce-Input--text input-text"  placeholder="<?php echo esc_attr__('Password*','bw-printxtore')?>" type="password" name="password" id="password" autocomplete="current-password" />
                            </p>

                            <?php do_action( 'woocommerce_login_form' ); ?>

                            <p class="form-row">
                                <?php wp_nonce_field( 'woocommerce-login', 'woocommerce-login-nonce' ); ?>
                                <button type="submit" class="woocommerce-button button woocommerce-form-login__submit" name="login" value="<?php esc_attr_e( 'Log in', 'bw-printxtore' ); ?>"><?php esc_html_e( 'Log in', 'bw-printxtore' ); ?></button>
                            </p>
                            <div class="rememberme-lost_password">
                                <label class="woocommerce-form__label woocommerce-form__label-for-checkbox woocommerce-form-login__rememberme">
                                    <input class="woocommerce-form__input woocommerce-form__input-checkbox" name="rememberme" type="checkbox" id="rememberme" value="forever" /> <span><?php esc_html_e( 'Remember me', 'bw-printxtore' ); ?></span>
                                </label>
                                <p class="woocommerce-LostPassword lost_password">
                                    <a href="<?php echo esc_url( wp_lostpassword_url() ); ?>"><?php esc_html_e( 'Lost your password?', 'bw-printxtore' ); ?></a>
                                </p>
                            </div>

                            <?php do_action( 'woocommerce_login_form_end' ); ?>

                        </form>
                    </div>
                    <?php if ( 'yes' === get_option( 'woocommerce_enable_myaccount_registration' ) ) : ?>
                        <div class="tab-pane " id="tab-register">
                            <form method="post" class="woocommerce-form woocommerce-form-register register" <?php do_action( 'woocommerce_register_form_tag' ); ?> >

                                <?php do_action( 'woocommerce_register_form_start' ); ?>

                                <?php if ( 'no' === get_option( 'woocommerce_registration_generate_username' ) ) : ?>

                                    <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                                        <input type="text" placeholder="<?php esc_attr_e( 'Username*', 'bw-printxtore' ); ?>" class="woocommerce-Input woocommerce-Input--text input-text" name="username" id="reg_username" autocomplete="username" value="<?php echo ( ! empty( $_POST['username'] ) ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; ?>" />
                                    </p>

                                <?php endif; ?>

                                <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                                    <input type="email" placeholder="<?php esc_attr_e( 'Email address*', 'bw-printxtore' ); ?>" class="woocommerce-Input woocommerce-Input--text input-text" name="email" id="reg_email" autocomplete="email" value="<?php echo ( ! empty( $_POST['email'] ) ) ? esc_attr( wp_unslash( $_POST['email'] ) ) : ''; ?>" />
                                </p>

                                <?php if ( 'no' === get_option( 'woocommerce_registration_generate_password' ) ) : ?>

                                    <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                                        <input type="password" placeholder="<?php esc_attr_e( 'Password*', 'bw-printxtore' ); ?>" class="woocommerce-Input woocommerce-Input--text input-text" name="password" id="reg_password" autocomplete="new-password" />
                                    </p>

                                <?php else : ?>

                                    <p><?php esc_html_e( 'A password will be sent to your email address.', 'bw-printxtore' ); ?></p>

                                <?php endif; ?>

                                <?php do_action( 'woocommerce_register_form' ); ?>

                                <p class="woocommerce-FormRow form-row">
                                    <?php wp_nonce_field( 'woocommerce-register', 'woocommerce-register-nonce' ); ?>
                                    <button type="submit" class="woocommerce-Button button myacc-bt-register" name="register" value="<?php esc_attr_e( 'Register', 'bw-printxtore' ); ?>"><?php esc_html_e( 'Register', 'bw-printxtore' ); ?></button>
                                </p>

                                <?php do_action( 'woocommerce_register_form_end' ); ?>

                            </form>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php do_action( 'woocommerce_after_customer_login_form' ); ?>
