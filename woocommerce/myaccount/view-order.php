<?php
/**
 * US-9.3 : détail d'une commande (bouton "Voir" dans Mes commandes), comme la maquette.
 * US-10.3 : ligne du code promo dans le récapitulatif.
 * US-15 : ligne des frais de préparation.
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

	<?php /* US-17.3 : télécharger la facture (seulement si elle existe : commande validée) */ ?>
	<?php $lien_facture = watsons_lien_facture( $commande ); ?>
	<?php if ( $lien_facture ) : ?>
		<div class="commande-facture">
			<a class="compte-bouton commande-facture__bouton" href="<?php echo esc_url( $lien_facture ); ?>" target="_blank" rel="noopener">
				<svg class="commande-facture__icone" viewBox="0 0 16 16" aria-hidden="true"><path d="M8 2v8M4.5 6.5 8 10l3.5-3.5M3 13h10"/></svg>Télécharger ma facture (PDF)
			</a>
			<p class="commande-facture__info"><?php echo esc_html( watsons_infos_facture( $commande ) ); ?></p>
		</div>
	<?php endif; ?>

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
					<span><?php echo wp_kses_post( wc_price( $commande->get_line_subtotal( $article, true ), $devise ) ); ?></span>
				</li>
			<?php endforeach; ?>

			<?php /* US-10.3 : code promo (les produits sont affichés avant la réduction) */ ?>
			<?php foreach ( $commande->get_coupon_codes() as $code_promo ) : ?>
				<li class="commande-detail__ligne commande-detail__ligne--promo">
					<span>Code <?php echo esc_html( strtoupper( $code_promo ) ); ?></span>
					<span>−<?php echo wp_kses_post( wc_price( (float) $commande->get_discount_total() + (float) $commande->get_discount_tax(), $devise ) ); ?></span>
				</li>
			<?php endforeach; ?>

			<?php /* US-15 : frais de préparation */ ?>
			<?php foreach ( watsons_lignes_frais( $commande ) as $ligne_frais ) : ?>
				<li class="commande-detail__ligne commande-detail__ligne--promo">
					<span><?php echo esc_html( $ligne_frais['nom'] ); ?></span>
					<span><?php echo wp_kses_post( $ligne_frais['montant'] ); ?></span>
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