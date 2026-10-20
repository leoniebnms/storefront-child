<?php
/**
 * US-5.3 : bas de la page Panier, comme sur la maquette avec un bloc "Réception" : choix du mode de réception et un bloc "Total (TTC)" avec un bouton "Valider le panier".
 *
 * @package WooCommerce\Templates
 * @version 2.3.6
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="cart_totals <?php echo ( WC()->customer->has_calculated_shipping() ) ? 'calculated_shipping' : ''; ?>">

	<?php do_action( 'woocommerce_before_cart_totals' ); ?>

	<?php if ( WC()->cart->needs_shipping() ) : ?>
		<section class="panier-reception">
			<h2 class="panier-reception__titre">Reception</h2>

			<?php
			$colis = WC()->shipping()->get_packages();
			$modes_trouves = false;

			foreach ( $colis as $index => $paquet ) :
				$modes = isset( $paquet['rates'] ) ? $paquet['rates'] : array();

				if ( empty( $modes ) ) {
					continue;
				}

				$modes_trouves = true;
				$choisi = isset( WC()->session->chosen_shipping_methods[ $index ] ) ? WC()->session->chosen_shipping_methods[ $index ] : '';

				// Si rien n'est encore choisi, le premier mode (Retrait en boutique) est coché.
				if ( ! isset( $modes[ $choisi ] ) ) {
					$choisi = current( $modes )->id;
				}
				?>
				<ul class="panier-reception__liste">
					<?php
					foreach ( $modes as $mode ) :
						$id_champ = 'shipping_method_' . $index . '_' . sanitize_title( $mode->id );
						$est_choisi = ( $mode->id === $choisi );

						// Prix affiché TTC si la boutique affiche ses prix TTC.
						$cout = (float) $mode->get_cost();
						if ( WC()->cart->display_prices_including_tax() ) {
							$cout += (float) $mode->get_shipping_tax();
						}

						$est_retrait = in_array( $mode->get_method_id(), array( 'local_pickup', 'pickup_location' ), true );
						?>
						<li class="panier-reception__option<?php echo $est_choisi ? ' panier-reception__option--actif' : ''; ?>">
							<input
								class="panier-reception__radio shipping_method"
								type="radio"
								name="shipping_method[<?php echo esc_attr( $index ); ?>]"
								data-index="<?php echo esc_attr( $index ); ?>"
								id="<?php echo esc_attr( $id_champ ); ?>"
								value="<?php echo esc_attr( $mode->id ); ?>"
								<?php checked( $est_choisi ); ?>
							>
							<label class="panier-reception__label" for="<?php echo esc_attr( $id_champ ); ?>">
								<span class="panier-reception__nom">
									<?php echo esc_html( $mode->get_label() ); ?>
									<?php if ( $est_retrait ) : ?>
										<span class="panier-reception__adresse">
											<?php echo esc_html( trim( get_option( 'woocommerce_store_address' ) . ', ' . get_option( 'woocommerce_store_city' ), ', ' ) ); ?>
										</span>
									<?php endif; ?>
								</span>
								<span class="panier-reception__prix">
									<?php echo $cout > 0 ? wp_kses_post( wc_price( $cout ) ) : 'Gratuit'; ?>
								</span>
							</label>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endforeach; ?>

			<?php if ( ! $modes_trouves ) : ?>
				<p class="panier-reception__message">Les modes de réception vous seront proposés à l'étape suivante.</p>
			<?php endif; ?>
		</section>
	<?php endif; ?>

	<div class="panier-total">
		<span class="panier-total__libelle">Total (TTC)</span>
		<span class="panier-total__montant"><?php echo wp_kses_post( WC()->cart->get_total() ); ?></span>
		<a class="button alt panier-total__bouton" href="<?php echo esc_url( wc_get_checkout_url() ); ?>">Valider le panier</a>
	</div>

	<?php do_action( 'woocommerce_after_cart_totals' ); ?>

</div>