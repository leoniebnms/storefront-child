<?php
/**
 * US-9.3 : e-mail "Remboursement effectué", comme la maquette.
 *
 * @package WooCommerce\Templates\Emails
 * @version 10.0.0
 */

defined( 'ABSPATH' ) || exit;

$devise         = array( 'currency' => $order->get_currency() );
$remboursements = $order->get_refunds();
$dernier        = $remboursements ? reset( $remboursements ) : null;
$date           = $dernier ? $dernier->get_date_created() : $order->get_date_modified();

do_action( 'woocommerce_email_header', 'Remboursement effectué', $email );

watsons_mail_intro( $order, 'Nous vous avons remboursé votre commande.', 'Le remboursement de votre commande annulée a bien été effectué.' );

// Cadre rouille "Remboursement" : mode, date et montant.
watsons_mail_cadre(
	'Remboursement',
	'<table class="mail-recap" role="presentation" width="100%" cellpadding="0" cellspacing="0">'
		. '<tr class="mail-recap__info"><td>Mode de remboursement</td><td class="mail-recap__prix">Virement bancaire</td></tr>'
		. '<tr class="mail-recap__info"><td>Date</td><td class="mail-recap__prix">' . esc_html( wc_format_datetime( $date, 'd/m/Y' ) ) . '</td></tr>'
		. '<tr class="mail-recap__total"><td>Montant remboursé</td><td class="mail-recap__prix">' . wc_price( $order->get_total_refunded(), $devise ) . '</td></tr>'
		. '</table>'
		. '<p class="mail-cadre__texte">Le virement peut mettre quelques jours à apparaître sur votre compte.</p>',
	true
);

watsons_mail_recapitulatif( $order );

do_action( 'woocommerce_email_footer', $email );