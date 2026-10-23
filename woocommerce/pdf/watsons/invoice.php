<?php
/**
 * US-17.1 / US-17.2 : facture PDF du Watson's Pub, comme la maquette.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$commande = $this->order;

// Retrait en boutique ou livraison à domicile.
$retrait = $commande->has_shipping_method( 'local_pickup' ) || $commande->has_shipping_method( 'pickup_location' );

// TVA : celle de WooCommerce si les taxes sont activées, sinon 10 % comprise dans les prix (restauration à emporter).
$taux_tva  = 10;
$total_ttc = (float) $commande->get_total();
$total_tva = (float) $commande->get_total_tax() > 0 ? (float) $commande->get_total_tax() : round( $total_ttc - $total_ttc / ( 1 + $taux_tva / 100 ), 2 );
$total_ht  = $total_ttc - $total_tva;

// Adresse de la boutique (WooCommerce > Réglages > Général) : rue, puis code postal et ville sur une ligne.
$rue   = WC()->countries->get_base_address();
$ville = trim( WC()->countries->get_base_postcode() . ' ' . WC()->countries->get_base_city() );
?>
<div class="facture">

	<?php /* En-tête : logo à gauche, "Facture", numéro et date à droite */ ?>
	<table class="facture-entete">
		<tr>
			<td class="facture-entete__logo"><?php $this->header_logo(); ?></td>
			<td class="facture-entete__droite">
				<h1 class="facture-entete__titre">Facture</h1>
				<p class="facture-entete__numero">N° <?php $this->number( 'invoice' ); ?></p>
				<p>Date : <?php $this->date( 'invoice' ); ?></p>
			</td>
		</tr>
	</table>

	<?php /* Le Watson's Pub (réglé dans Factures PDF > Général > Information sur la boutique) et le client */ ?>
	<table class="facture-adresses">
		<tr>
			<td class="facture-adresses__boutique">
				<h2 class="facture-adresses__titre"><?php echo esc_html( $this->get_shop_name() ); ?></h2>
				<p>
					<?php echo esc_html( $rue ); ?><br>
					<?php echo esc_html( $ville ); ?><br>
					<?php echo esc_html( $this->get_shop_phone_number() ); ?><br>
					<?php echo esc_html( $this->get_shop_email_address() ); ?>
					<?php if ( $this->get_shop_coc_number() ) : ?>
						<br>SIRET : <?php echo esc_html( $this->get_shop_coc_number() ); ?>
					<?php endif; ?>
					<?php if ( $this->get_shop_vat_number() ) : ?>
						<br>TVA : <?php echo esc_html( $this->get_shop_vat_number() ); ?>
					<?php endif; ?>
				</p>
			</td>
			<td class="facture-adresses__client">
				<h2 class="facture-adresses__titre">Facturé à</h2>
				<p>
					<?php echo esc_html( $commande->get_formatted_billing_full_name() ); ?><br>
					<?php echo esc_html( $commande->get_billing_address_1() ); ?><br>
					<?php echo esc_html( trim( $commande->get_billing_postcode() . ' ' . $commande->get_billing_city() ) ); ?><br>
					<?php echo esc_html( $commande->get_billing_email() ); ?>
				</p>
			</td>
		</tr>
	</table>

	<?php /* Commande, date, réception, paiement */ ?>
	<table class="facture-infos">
		<tr>
			<td class="facture-infos__case"><span class="facture-infos__libelle">Commande</span>N° <?php echo esc_html( $commande->get_order_number() ); ?></td>
			<td class="facture-infos__case"><span class="facture-infos__libelle">Date de commande</span><?php echo esc_html( wc_format_datetime( $commande->get_date_created(), 'd/m/Y' ) ); ?></td>
			<td class="facture-infos__case"><span class="facture-infos__libelle">Réception</span><?php echo esc_html( $retrait ? 'Retrait en boutique' : 'Livraison à domicile' ); ?></td>
			<td class="facture-infos__case"><span class="facture-infos__libelle">Paiement</span><?php echo esc_html( $commande->get_payment_method_title() ); ?></td>
		</tr>
	</table>

	<?php /* Produits, codes promo, frais de préparation et livraison */ ?>
	<table class="facture-produits">
		<thead>
			<tr>
				<th class="facture-produits__entete">Produit</th>
				<th class="facture-produits__entete facture-produits__entete--nombre">Quantité</th>
				<th class="facture-produits__entete facture-produits__entete--nombre">Prix unit. TTC</th>
				<th class="facture-produits__entete facture-produits__entete--nombre">TVA</th>
				<th class="facture-produits__entete facture-produits__entete--nombre">Total TTC</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ( $commande->get_items() as $article ) : ?>
				<?php
				$quantite = max( 1, (int) $article->get_quantity() );
				$total    = (float) $article->get_subtotal() + (float) $article->get_subtotal_tax();
				?>
				<tr>
					<td class="facture-produits__cellule">
						<?php echo esc_html( $article->get_name() ); ?>
						<?php if ( $article->get_meta( 'Sans' ) ) : ?>
							<span class="facture-produits__options">Sans : <?php echo esc_html( $article->get_meta( 'Sans' ) ); ?></span>
						<?php endif; ?>
					</td>
					<td class="facture-produits__cellule facture-produits__cellule--nombre"><?php echo esc_html( $quantite ); ?></td>
					<td class="facture-produits__cellule facture-produits__cellule--nombre"><?php echo wp_kses_post( wc_price( $total / $quantite ) ); ?></td>
					<td class="facture-produits__cellule facture-produits__cellule--nombre"><?php echo esc_html( $taux_tva ); ?> %</td>
					<td class="facture-produits__cellule facture-produits__cellule--nombre"><?php echo wp_kses_post( wc_price( $total ) ); ?></td>
				</tr>
			<?php endforeach; ?>

			<?php /* US-10 : code promo */ ?>
			<?php foreach ( $commande->get_coupon_codes() as $code ) : ?>
				<tr>
					<td class="facture-produits__cellule" colspan="4">Code <?php echo esc_html( strtoupper( $code ) ); ?></td>
					<td class="facture-produits__cellule facture-produits__cellule--nombre">−<?php echo wp_kses_post( wc_price( $commande->get_discount_total() + $commande->get_discount_tax() ) ); ?></td>
				</tr>
				<?php break; // Un seul code à la fois sur le site. ?>
			<?php endforeach; ?>

			<?php /* US-15 : frais de préparation */ ?>
			<?php foreach ( $commande->get_fees() as $frais ) : ?>
				<tr>
					<td class="facture-produits__cellule" colspan="4"><?php echo esc_html( $frais->get_name() ); ?></td>
					<td class="facture-produits__cellule facture-produits__cellule--nombre"><?php echo wp_kses_post( wc_price( (float) $frais->get_total() + (float) $frais->get_total_tax() ) ); ?></td>
				</tr>
			<?php endforeach; ?>

			<?php /* Retrait en boutique (gratuit) ou livraison */ ?>
			<tr>
				<td class="facture-produits__cellule" colspan="4"><?php echo esc_html( $commande->get_shipping_method() ); ?></td>
				<td class="facture-produits__cellule facture-produits__cellule--nombre">
					<?php $livraison = (float) $commande->get_shipping_total() + (float) $commande->get_shipping_tax(); ?>
					<?php echo $livraison > 0 ? wp_kses_post( wc_price( $livraison ) ) : 'Gratuit'; ?>
				</td>
			</tr>
		</tbody>
	</table>

	<?php /* Totaux HT, TVA et TTC */ ?>
	<table class="facture-totaux">
		<tr>
			<td class="facture-totaux__libelle">Total HT</td>
			<td class="facture-totaux__montant"><?php echo wp_kses_post( wc_price( $total_ht ) ); ?></td>
		</tr>
		<tr>
			<td class="facture-totaux__libelle">TVA <?php echo esc_html( $taux_tva ); ?> %</td>
			<td class="facture-totaux__montant"><?php echo wp_kses_post( wc_price( $total_tva ) ); ?></td>
		</tr>
		<tr class="facture-totaux__ttc">
			<td class="facture-totaux__libelle">Total (TTC)</td>
			<td class="facture-totaux__montant"><?php echo wp_kses_post( wc_price( $total_ttc ) ); ?></td>
		</tr>
	</table>

	<?php /* Facture acquittée : seulement si la commande est payée (virement reçu, ou réglée au comptoir) */ ?>
	<?php if ( $commande->get_date_paid() ) : ?>
		<p class="facture-acquittee"><span class="facture-acquittee__icone">✓</span> Facture acquittée le <?php echo esc_html( wc_format_datetime( $commande->get_date_paid(), 'd/m/Y' ) ); ?> par <?php echo esc_html( mb_strtolower( $commande->get_payment_method_title() ) ); ?></p>
	<?php endif; ?>

	<?php /* Mentions légales : le "Pied de page" réglé dans Factures PDF > Général */ ?>
	<?php if ( $this->get_footer() ) : ?>
		<div class="facture-mentions"><?php $this->footer(); ?></div>
	<?php endif; ?>

	<?php /* Bande rouille en bas de la page, comme le pied des e-mails */ ?>
	<div class="facture-pied">
		<p class="facture-pied__titre"><?php echo esc_html( remove_accents( $this->get_shop_name() ) ); ?></p>
		<p class="facture-pied__texte">
			<?php echo esc_html( mb_strtoupper( $rue . ' ' . $ville ) ); ?><br>
			<?php echo esc_html( $this->get_shop_phone_number() . ' - ' . mb_strtoupper( $this->get_shop_email_address() ) ); ?>
		</p>
	</div>

</div>