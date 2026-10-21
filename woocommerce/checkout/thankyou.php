<?php
/**
 * US-6.2 : page Confirmation comme la maquette.
 * US-10.3 : ligne du code promo dans le récapitulatif.
 *
 * @package WooCommerce\Templates
 * @version 8.1.0
 *
 * @var WC_Order $order
 */

defined( 'ABSPATH' ) || exit;

// Temps de préparation d'une commande en retrait, en minutes (le même que dans les instructions).
$minutes_preparation = 20;
?>
<div class="woocommerce-order">

	<?php if ( ! $order ) : ?>

		<?php wc_get_template( 'checkout/order-received.php', array( 'order' => false ) ); ?>

	<?php elseif ( $order->has_status( 'failed' ) ) : ?>

		<p class="woocommerce-notice woocommerce-notice--error woocommerce-thankyou-order-failed"><?php esc_html_e( 'Unfortunately your order cannot be processed as the originating bank/merchant has declined your transaction. Please attempt your purchase again.', 'woocommerce' ); ?></p>
		<p class="woocommerce-notice woocommerce-notice--error woocommerce-thankyou-order-failed-actions">
			<a href="<?php echo esc_url( $order->get_checkout_payment_url() ); ?>" class="button pay"><?php esc_html_e( 'Pay', 'woocommerce' ); ?></a>
		</p>

	<?php else : ?>

		<?php
		do_action( 'woocommerce_before_thankyou', $order->get_id() );

		$retrait   = $order->has_shipping_method( 'local_pickup' ) || $order->has_shipping_method( 'pickup_location' );
		$virement  = $order->has_status( 'on-hold' );
		$consignes = trim( (string) get_option( 'watsons_instructions_retrait', '' ) );
		?>

		<p class="merci__sous-titre">
			<?php echo esc_html( $virement ? 'Votre commande sera préparée dès réception de votre virement.' : 'Nous préparons actuellement votre commande.' ); ?>
		</p>

		<div class="merci">

			<?php /* À gauche : le récapitulatif du panier */ ?>
			<section class="merci-recap">
				<h2 class="merci-recap__titre">Recap de votre panier</h2>

				<p class="merci-recap__total">
					<span>Total (TTC)</span>
					<span><?php echo wp_kses_post( $order->get_formatted_order_total() ); ?></span>
				</p>

				<ul class="merci-recap__liste">
					<?php foreach ( $order->get_items() as $article ) : ?>
						<li class="merci-recap__ligne">
							<span class="merci-recap__produit">
								<?php echo esc_html( $article->get_name() ); ?>
								<?php if ( $article->get_quantity() > 1 ) : ?>
									× <?php echo esc_html( $article->get_quantity() ); ?>
								<?php endif; ?>
								<?php if ( $article->get_meta( 'Sans' ) ) : ?>
									<span class="merci-recap__sans">Sans : <?php echo esc_html( $article->get_meta( 'Sans' ) ); ?></span>
								<?php endif; ?>
							</span>
							<span class="merci-recap__prix"><?php echo wp_kses_post( $order->get_formatted_line_subtotal( $article ) ); ?></span>
						</li>
					<?php endforeach; ?>

					<li class="merci-recap__ligne">
						<span class="merci-recap__produit"><?php echo esc_html( $order->get_shipping_method() ); ?></span>
						<span class="merci-recap__prix">
							<?php echo $order->get_shipping_total() > 0 ? wp_kses_post( wc_price( $order->get_shipping_total() ) ) : 'Gratuit'; ?>
						</span>
					</li>

					<?php /* US-10.3 : code promo (les produits sont affichés avant la réduction) */ ?>
					<?php foreach ( $order->get_coupon_codes() as $code_promo ) : ?>
						<li class="merci-recap__ligne merci-recap__ligne--promo">
							<span class="merci-recap__produit">Code <?php echo esc_html( strtoupper( $code_promo ) ); ?></span>
							<span class="merci-recap__prix">−<?php echo wp_kses_post( wc_price( (float) $order->get_discount_total() + (float) $order->get_discount_tax() ) ); ?></span>
						</li>
					<?php endforeach; ?>
				</ul>
			</section>

			<?php /* À droite : le numéro de commande et les infos de retrait ou de livraison */ ?>
			<section class="merci-infos">
				<p class="merci-infos__numero">N° <?php echo esc_html( $order->get_order_number() ); ?></p>

				<?php if ( $retrait ) : ?>

					<?php if ( $consignes ) : ?>
						<div class="merci-infos__texte"><?php echo wpautop( esc_html( $consignes ) ); // phpcs:ignore WordPress.Security.EscapeOutput -- déjà échappé. ?></div>
					<?php endif; ?>

					<?php if ( ! $virement && $order->get_date_created() ) : ?>
						<p class="merci-infos__note">Temps de préparation estimé :</p>
						<p class="merci-minuteur" data-fin="<?php echo esc_attr( $order->get_date_created()->getTimestamp() + $minutes_preparation * 60 ); ?>">
							<svg class="merci-minuteur__icone" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
							<span class="merci-minuteur__temps"><?php echo esc_html( sprintf( '00 : %02d : 00', $minutes_preparation ) ); ?></span>
						</p>
					<?php endif; ?>

				<?php else : ?>

					<p class="merci-infos__texte">Votre commande sera livrée à l'adresse suivante :</p>
					<address class="merci-infos__adresse">
						<?php echo wp_kses_post( $order->get_formatted_shipping_address() ? $order->get_formatted_shipping_address() : $order->get_formatted_billing_address() ); ?>
					</address>

				<?php endif; ?>

				<?php if ( 'bacs' === $order->get_payment_method() ) : ?>

					<?php /* Virement : nos coordonnées bancaires, comme sur la maquette (réglées dans WooCommerce > Réglages > Paiements) */ ?>
					<div class="merci-virement">
						<h3 class="merci-virement__titre">Nos coordonnées bancaires</h3>
						<?php foreach ( (array) get_option( 'woocommerce_bacs_accounts', array() ) as $compte ) : ?>
							<p class="merci-virement__texte">
								<?php echo esc_html( isset( $compte['account_name'] ) ? $compte['account_name'] : '' ); ?>
								<?php if ( ! empty( $compte['iban'] ) ) : ?>
									<br>IBAN : <strong><?php echo esc_html( $compte['iban'] ); ?></strong>
								<?php endif; ?>
								<?php if ( ! empty( $compte['bic'] ) ) : ?>
									<br>BIC : <strong><?php echo esc_html( $compte['bic'] ); ?></strong>
								<?php endif; ?>
							</p>
						<?php endforeach; ?>
						<p class="merci-virement__texte">Référence à indiquer : <strong><?php echo esc_html( $order->get_order_number() ); ?></strong></p>
					</div>

				<?php else : ?>

					<?php /* Paiement au retrait : la consigne réglée dans WooCommerce > Réglages > Paiements */ ?>
					<div class="merci-infos__paiement">
						<?php do_action( 'woocommerce_thankyou_' . $order->get_payment_method(), $order->get_id() ); ?>
					</div>

				<?php endif; ?>
			</section>

		</div>

		<p class="merci__retour">
			<a class="retour-carte" href="<?php echo esc_url( home_url( '/' ) ); ?>"><svg class="retour-carte__icone" viewBox="0 0 9 14" aria-hidden="true"><path d="M8 1 2 7l6 6"/></svg>Retour à l'accueil</a>
		</p>

		<script>
		/* Compte à rebours jusqu'à l'heure où la commande sera prête. */
		( function () {
			var bloc = document.querySelector( '.merci-minuteur' );

			if ( ! bloc ) {
				return;
			}

			var temps = bloc.querySelector( '.merci-minuteur__temps' );
			var fin = parseInt( bloc.dataset.fin, 10 ) * 1000;
			var deuxChiffres = function ( n ) {
				return ( n < 10 ? '0' : '' ) + n;
			};

			function majMinuteur() {
				var reste = Math.max( 0, Math.round( ( fin - Date.now() ) / 1000 ) );

				if ( 0 === reste ) {
					temps.textContent = 'Votre commande est prête !';
					clearInterval( minuteur );
					return;
				}

				temps.textContent = deuxChiffres( Math.floor( reste / 3600 ) ) + ' : ' + deuxChiffres( Math.floor( reste / 60 ) % 60 ) + ' : ' + deuxChiffres( reste % 60 );
			}

			var minuteur = setInterval( majMinuteur, 1000 );
			majMinuteur();
		} )();
		</script>

	<?php endif; ?>

</div>