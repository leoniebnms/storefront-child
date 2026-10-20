<?php
/**
 * US-7.2 : page "mdp oublié" comme la maquette.
 *
 * @package WooCommerce\Templates
 * @version 9.2.0
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_lost_password_form' );
?>

<section class="compte-cadre compte-cadre--centre">
	<h2 class="compte-cadre__titre">Mot de passe oublie</h2>
	<p class="compte-cadre__sous-titre">Saisissez votre e-mail : vous recevrez un lien pour créer un nouveau mot de passe.</p>

	<form class="compte-formulaire woocommerce-ResetPassword lost_reset_password" method="post">
		<?php watsons_champ_compte( 'user_login', 'E-mail', '', 'text', 'username' ); ?>

		<?php do_action( 'woocommerce_lostpassword_form' ); ?>

		<input type="hidden" name="wc_reset_password" value="true">
		<?php wp_nonce_field( 'lost_password', 'woocommerce-lost-password-nonce' ); ?>
		<button type="submit" class="compte-bouton" value="Réinitialiser le mot de passe">Réinitialiser le mot de passe</button>

		<p class="compte-cadre__lien"><a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>">Retour à la connexion</a></p>
	</form>
</section>

<?php do_action( 'woocommerce_after_lost_password_form' ); ?>