<?php
/**
 * US-7.2 : page "infos persos" comme la maquette.
 * US-9.3 : tableau "Mes commandes" entre les deux formulaires.
 *
 * @package WooCommerce\Templates
 * @version 4.4.0
 */

defined( 'ABSPATH' ) || exit;

$utilisateur = wp_get_current_user();
$client      = new WC_Customer( $utilisateur->ID );
$civilite    = get_user_meta( $utilisateur->ID, 'watsons_civilite', true );

// Le nom affiché ne doit pas être une adresse e-mail (WooCommerce le refuse).
$nom_affiche = is_email( $utilisateur->display_name ) ? $utilisateur->user_login : $utilisateur->display_name;
?>

<div class="compte">

	<?php /* Informations personnelles (enregistrées par functions.php) */ ?>
	<section class="compte-cadre">
		<h2 class="compte-cadre__titre">Informations personnelles</h2>

		<form class="compte-formulaire" method="post">
			<fieldset class="compte-civilite">
				<legend class="compte-civilite__titre">Civilité</legend>
				<label class="compte-civilite__option">
					<input class="compte-civilite__radio" type="radio" name="civilite" value="mme" <?php checked( $civilite, 'mme' ); ?>> Mme
				</label>
				<label class="compte-civilite__option">
					<input class="compte-civilite__radio" type="radio" name="civilite" value="m" <?php checked( $civilite, 'm' ); ?>> M.
				</label>
			</fieldset>

			<div class="compte-formulaire__ligne">
				<?php
				watsons_champ_compte( 'billing_last_name', 'Nom', $client->get_billing_last_name() ? $client->get_billing_last_name() : $client->get_last_name(), 'text', 'family-name' );
				watsons_champ_compte( 'billing_first_name', 'Prénom', $client->get_billing_first_name() ? $client->get_billing_first_name() : $client->get_first_name(), 'text', 'given-name' );
				?>
			</div>

			<?php
			watsons_champ_compte( 'billing_phone', 'Téléphone', $client->get_billing_phone(), 'tel', 'tel' );
			watsons_champ_compte( 'billing_address_1', 'Numéro et nom de rue', $client->get_billing_address_1(), 'text', 'address-line1' );
			?>

			<div class="compte-formulaire__ligne">
				<?php
				watsons_champ_compte( 'billing_postcode', 'Code postal', $client->get_billing_postcode(), 'text', 'postal-code' );
				watsons_champ_compte( 'billing_city', 'Ville', $client->get_billing_city(), 'text', 'address-level2' );
				?>
			</div>

			<?php wp_nonce_field( 'watsons_infos_perso', 'watsons-infos-nonce' ); ?>
			<button type="submit" class="compte-bouton" name="watsons_infos_perso" value="1">Enregistrer</button>
		</form>
	</section>

	<?php /* US-9.3 : mes commandes (les 10 dernières), avec le bouton "Voir" vers le détail */ ?>
	<?php
	$commandes = wc_get_orders(
		array(
			'customer' => $utilisateur->ID,
			'limit'    => 10,
			'orderby'  => 'date',
			'order'    => 'DESC',
		)
	);
	?>
	<section class="compte-cadre compte-cadre--tableau" aria-label="Mes commandes">
		<?php if ( $commandes ) : ?>
			<table class="compte-commandes">
				<thead>
					<tr>
						<th class="compte-commandes__entete">N°</th>
						<th class="compte-commandes__entete compte-commandes__entete--date">Date</th>
						<th class="compte-commandes__entete">Statut</th>
						<th class="compte-commandes__entete">Total</th>
						<th class="compte-commandes__entete compte-commandes__entete--voir">Voir</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( $commandes as $commande ) : ?>
						<tr class="compte-commandes__ligne">
							<td class="compte-commandes__cellule">
								<a class="compte-commandes__numero" href="<?php echo esc_url( $commande->get_view_order_url() ); ?>"><?php echo esc_html( $commande->get_order_number() ); ?></a>
							</td>
							<td class="compte-commandes__cellule compte-commandes__cellule--date"><?php echo esc_html( wc_format_datetime( $commande->get_date_created(), 'd/m/Y' ) ); ?></td>
							<td class="compte-commandes__cellule"><?php echo watsons_badge_statut( $commande ); // phpcs:ignore WordPress.Security.EscapeOutput -- déjà échappé. ?></td>
							<td class="compte-commandes__cellule"><?php echo wp_kses_post( $commande->get_formatted_order_total() ); ?></td>
							<td class="compte-commandes__cellule compte-commandes__cellule--voir">
								<a class="compte-commandes__bouton" href="<?php echo esc_url( $commande->get_view_order_url() ); ?>">Voir</a>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		<?php else : ?>
			<p class="compte-commandes__vide">Vous n'avez pas encore passé de commande.</p>
		<?php endif; ?>
	</section>

	<?php /* Identifiant et mot de passe (enregistrés par WooCommerce) */ ?>
	<section class="compte-cadre">
		<h2 class="compte-cadre__titre">Changer mon identifiant et mon mot de passe</h2>

		<form class="compte-formulaire" method="post">
			<?php watsons_champ_compte( 'account_email', 'E-mail', $utilisateur->user_email, 'email', 'email' ); ?>

			<?php /* WooCommerce a besoin du nom et du prénom : on renvoie ceux déjà enregistrés */ ?>
			<input type="hidden" name="account_first_name" value="<?php echo esc_attr( $utilisateur->first_name ); ?>">
			<input type="hidden" name="account_last_name" value="<?php echo esc_attr( $utilisateur->last_name ); ?>">
			<input type="hidden" name="account_display_name" value="<?php echo esc_attr( $nom_affiche ); ?>">

			<?php /* Le bouton "Modifier mon mot de passe" déplie les champs du mot de passe */ ?>
			<details class="compte-mdp">
				<summary class="compte-bouton">Modifier mon mot de passe</summary>

				<?php
				watsons_champ_compte( 'password_current', 'Mot de passe actuel', '', 'password', 'current-password' );
				watsons_champ_compte( 'password_1', 'Nouveau mot de passe', '', 'password', 'new-password' );
				watsons_champ_compte( 'password_2', 'Confirmer le nouveau mot de passe', '', 'password', 'new-password' );
				?>

				<?php wp_nonce_field( 'save_account_details', 'save-account-details-nonce' ); ?>
				<input type="hidden" name="action" value="save_account_details">
				<button type="submit" class="compte-bouton" name="save_account_details" value="Enregistrer">Enregistrer</button>
			</details>
		</form>
	</section>

	<a class="button compte-bouton compte-bouton--contour compte__deconnexion" href="<?php echo esc_url( wc_logout_url() ); ?>">Me déconnecter</a>

</div>