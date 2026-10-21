<?php
/**
 * US-9.3 : détail d'une commande (bouton "Voir" dans Mes commandes), comme la maquette.
 *
 * @package WooCommerce\Templates
 * @version 3.0.0
 */

defined( 'ABSPATH' ) || exit;

$commande = wc_get_order( $order_id );

if ( ! $commande ) {
	return;
}

$numero  = $commande->get_order_number();
$devise  = array( 'currency' => $commande->get_currency() );
$total   = wc_price( $commande->get_total(), $devise );
$retrait = watsons_commande_retrait( $commande );
// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- simple affichage du message.
$annulee = isset( $_GET['annulee'] ) && $commande->has_status( 'cancelled' );
?>

<div class="compte commande-detail">

	<?php /* Message après l'annulation */ ?>
	<?php if ( $annulee ) : ?>
		<p class="commande-detail__message">
			Votre commande N°<?php echo esc_html( $numero ); ?> a bien été annulée.
			<?php if ( 'oui' === $commande->get_meta( '_watsons_a_rembourser' ) ) : ?>
				Le remboursement de <?php echo wp_kses_post( $total ); ?> sera effectué par virement sur votre compte.
			<?php endif; ?>
			Vous allez recevoir un e-mail de confirmation.
		</p>
	<?php endif; ?>

	<?php /* "Passée le 21/10/2026 – En cours – Livraison à domicile – Virement bancaire" */ ?>
	<p class="commande-detail__infos">
		Passée le <?php echo esc_html( wc_format_datetime( $commande->get_date_created(), 'd/m/Y' ) ); ?> –
		<?php echo watsons_badge_statut( $commande ); // phpcs:ignore WordPress.Security.EscapeOutput -- déjà échappé. ?>
		<?php if ( $commande->get_shipping_method() ) : ?>
			– <?php echo esc_html( $commande->get_shipping_method() ); ?>
		<?php endif; ?>
		– <?php echo esc_html( $commande->get_payment_method_title() ); ?>
	</p>

	<?php /* Récapitulatif (sans accent dans le titre : la police Tall Dark n'en a pas) */ ?>
	<section class="compte-cadre">
		<h2 class="compte-cadre__titre commande-detail__titre">Recapitulatif de la commande</h2>

		<ul class="commande-detail__liste">
			<?php foreach ( $commande->get_items() as $article ) : ?>
				<?php
				$quantite = $article->get_quantity();
				$options  = wc_display_item_meta(
					$article,
					array(
						'before'       => '',
						'after'        => '',
						'separator'    => ' – ',
						'label_before' => '',
						'label_after'  => ' : ',
						'echo'         => false,
						'autop'        => false,
					)
				);
				?>
				<li class="commande-detail__ligne commande-detail__ligne--produit">
					<span class="commande-detail__nom">
						<?php echo esc_html( ( $quantite > 1 ? $quantite . ' × ' : '' ) . $article->get_name() ); ?>
						<?php if ( $options ) : ?>
							<span class="commande-detail__options"><?php echo esc_html( wp_strip_all_tags( $options ) ); ?></span>
						<?php endif; ?>
					</span>
					<span><?php echo wp_kses_post( wc_price( $commande->get_line_total( $article, true ), $devise ) ); ?></span>
				</li>
			<?php endforeach; ?>

			<?php if ( $commande->get_shipping_method() ) : ?>
				<?php $frais = (float) $commande->get_shipping_total() + (float) $commande->get_shipping_tax(); ?>
				<li class="commande-detail__ligne">
					<span><?php echo esc_html( $commande->get_shipping_method() ); ?></span>
					<span><?php echo $frais > 0 ? wp_kses_post( wc_price( $frais, $devise ) ) : 'Gratuit'; ?></span>
				</li>
			<?php endif; ?>

			<li class="commande-detail__ligne">
				<span>Mode de paiement</span>
				<span><?php echo esc_html( $commande->get_payment_method_title() ); ?></span>
			</li>

			<li class="commande-detail__ligne commande-detail__ligne--total">
				<span>Total (TTC)</span>
				<span><?php echo wp_kses_post( $total ); ?></span>
			</li>
		</ul>
	</section>

	<?php /* Adresse de livraison (pas pour un retrait en boutique) */ ?>
	<?php if ( ! $retrait ) : ?>
		<section class="compte-cadre">
			<h2 class="compte-cadre__titre commande-detail__titre">Adresse de livraison</h2>
			<p class="commande-detail__adresse">
				<?php echo wp_kses( $commande->get_formatted_shipping_address() ? $commande->get_formatted_shipping_address() : $commande->get_formatted_billing_address(), array( 'br' => array() ) ); ?>
			</p>
		</section>
	<?php endif; ?>

	<?php /* Annulation : seulement si la commande n'est pas encore prête ni expédiée */ ?>
	<?php if ( watsons_commande_annulable( $commande ) ) : ?>
		<button type="button" class="compte-bouton compte-bouton--contour commande-detail__annuler" popovertarget="confirmer-annulation">Annuler ma commande</button>
		<p class="commande-detail__aide">Vous pouvez annuler votre commande tant qu'elle n'a pas été expédiée. Si vous avez déjà payé, vous êtes remboursé par virement.</p>

		<?php /* Fenêtre de confirmation */ ?>
		<div id="confirmer-annulation" class="commande-confirmation" popover>
			<p class="commande-confirmation__titre">Annuler la commande N°<?php echo esc_html( $numero ); ?></p>
			<p class="commande-confirmation__texte">
				Votre commande ne sera pas préparée.
				<?php if ( watsons_commande_payee( $commande ) ) : ?>
					Le montant payé (<?php echo wp_kses_post( $total ); ?>) vous sera remboursé par virement.
				<?php endif; ?>
			</p>

			<form class="commande-confirmation__boutons" method="post">
				<button type="button" class="compte-bouton compte-bouton--contour" popovertarget="confirmer-annulation" popovertargetaction="hide">Non, garder ma commande</button>
				<?php wp_nonce_field( 'watsons_annuler_commande', 'watsons-annulation-nonce' ); ?>
				<button type="submit" class="compte-bouton" name="watsons_annuler_commande" value="<?php echo esc_attr( $commande->get_id() ); ?>">Oui, annuler ma commande</button>
			</form>
		</div>
	<?php endif; ?>

</div>