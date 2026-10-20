<?php
/**
 * US-7.2 : page "Compte client" comme sur la maquette
 *
 * @package WooCommerce\Templates
 * @version 9.9.0
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_customer_login_form' );

$inscription = ( 'yes' === get_option( 'woocommerce_enable_myaccount_registration' ) );
// phpcs:disable WordPress.Security.NonceVerification.Missing -- on remet seulement ce que le visiteur avait tapé.
$identifiant_tape = ! empty( $_POST['username'] ) ? sanitize_text_field( wp_unslash( $_POST['username'] ) ) : '';
$email_tape       = ! empty( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
// phpcs:enable
?>

<div class="compte-acces<?php echo $inscription ? '' : ' compte-acces--seul'; ?>">

	<?php /* Se connecter */ ?>
	<section class="compte-cadre">
		<h2 class="compte-cadre__titre">Se connecter</h2>
		<p class="compte-cadre__sous-titre">Vous avez déjà commandé au Watson's Pub ?</p>

		<form class="compte-formulaire" method="post">
			<?php do_action( 'woocommerce_login_form_start' ); ?>

			<?php
			watsons_champ_compte( 'username', 'E-mail', $identifiant_tape, 'text', 'username' );
			watsons_champ_compte( 'password', 'Mot de passe', '', 'password', 'current-password' );
			?>

			<?php do_action( 'woocommerce_login_form' ); ?>

			<label class="compte-case">
				<input class="compte-case__saisie" name="rememberme" type="checkbox" id="rememberme" value="forever">
				Se souvenir de moi
			</label>

			<?php wp_nonce_field( 'woocommerce-login', 'woocommerce-login-nonce' ); ?>
			<button type="submit" class="compte-bouton" name="login" value="Se connecter">Se connecter</button>

			<p class="compte-cadre__lien"><a href="<?php echo esc_url( wp_lostpassword_url() ); ?>">Mot de passe oublié ?</a></p>

			<?php do_action( 'woocommerce_login_form_end' ); ?>
		</form>
	</section>

	<?php if ( $inscription ) : ?>

		<?php /* Créer un compte */ ?>
		<section class="compte-cadre">
			<h2 class="compte-cadre__titre">Creer un compte</h2>
			<p class="compte-cadre__sous-titre">Première commande ? Créez votre compte.</p>

			<form class="compte-formulaire" method="post" <?php do_action( 'woocommerce_register_form_tag' ); ?>>
				<?php do_action( 'woocommerce_register_form_start' ); ?>

				<?php
				// Identifiant seulement si WooCommerce ne le crée pas tout seul à partir de l'e-mail (réglage US-7.1).
				if ( 'no' === get_option( 'woocommerce_registration_generate_username' ) ) {
					watsons_champ_compte( 'username', 'Identifiant', $identifiant_tape, 'text', 'username', 'reg_username' );
				}

				watsons_champ_compte( 'email', 'E-mail', $email_tape, 'email', 'email', 'reg_email' );

				// Mot de passe seulement si WooCommerce n'envoie pas de lien par e-mail (réglage US-7.1).
				if ( 'no' === get_option( 'woocommerce_registration_generate_password' ) ) {
					watsons_champ_compte( 'password', 'Mot de passe', '', 'password', 'new-password', 'reg_password' );
				} else {
					echo '<p class="compte-cadre__info">Un lien pour définir votre mot de passe vous sera envoyé par e-mail.</p>';
				}
				?>

				<?php /* Texte sur les données personnelles (réglé dans WooCommerce > Comptes et confidentialité) */ ?>
				<?php do_action( 'woocommerce_register_form' ); ?>

				<?php wp_nonce_field( 'woocommerce-register', 'woocommerce-register-nonce' ); ?>
				<button type="submit" class="compte-bouton compte-bouton--contour" name="register" value="Créer mon compte">Créer mon compte</button>

				<?php do_action( 'woocommerce_register_form_end' ); ?>
			</form>
		</section>

	<?php endif; ?>

</div>

<?php do_action( 'woocommerce_after_customer_login_form' ); ?>