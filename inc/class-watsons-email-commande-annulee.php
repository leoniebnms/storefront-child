<?php
/**
 * US-9.3 : e-mail "Commande annulée", envoyé au client.
 */

defined( 'ABSPATH' ) || exit;

class Watsons_Email_Commande_Annulee extends WC_Email {

	public function __construct() {
		$this->id             = 'watsons_commande_annulee';
		$this->customer_email = true;
		$this->title          = 'Commande annulée (client)';
		$this->description    = 'Envoyé au client quand sa commande passe au statut « Annulée ».';
		$this->template_html  = 'emails/customer-commande-annulee.php';
		$this->template_plain = 'emails/customer-commande-annulee.php';
		$this->placeholders   = array(
			'{order_date}'   => '',
			'{order_number}' => '',
		);

		// Passage de "En attente de paiement", "En attente" ou "En cours" à "Annulée".
		add_action( 'woocommerce_order_status_pending_to_cancelled_notification', array( $this, 'trigger' ), 10, 2 );
		add_action( 'woocommerce_order_status_on-hold_to_cancelled_notification', array( $this, 'trigger' ), 10, 2 );
		add_action( 'woocommerce_order_status_processing_to_cancelled_notification', array( $this, 'trigger' ), 10, 2 );

		parent::__construct();
	}

	// Objet et titre par défaut (modifiables dans les réglages).
	public function get_default_subject() {
		return "Votre commande n° {order_number} au Watson's Pub a été annulée";
	}

	public function get_default_heading() {
		return 'Commande annulée';
	}

	// Envoi du mail.
	public function trigger( $order_id, $order = false ) {
		$this->setup_locale();

		if ( $order_id && ! is_a( $order, 'WC_Order' ) ) {
			$order = wc_get_order( $order_id );
		}

		if ( is_a( $order, 'WC_Order' ) ) {
			$this->object                         = $order;
			$this->recipient                      = $order->get_billing_email();
			$this->placeholders['{order_date}']   = wc_format_datetime( $order->get_date_created() );
			$this->placeholders['{order_number}'] = $order->get_order_number();
		}

		if ( $this->is_enabled() && $this->get_recipient() ) {
			$this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
		}

		$this->restore_locale();
	}

	// Contenu HTML : notre template.
	public function get_content_html() {
		return wc_get_template_html(
			$this->template_html,
			array(
				'order'              => $this->object,
				'email_heading'      => $this->get_heading(),
				'additional_content' => $this->get_additional_content(),
				'sent_to_admin'      => false,
				'plain_text'         => false,
				'email'              => $this,
			)
		);
	}

	// Version texte : le même contenu sans les balises.
	public function get_content_plain() {
		return wp_strip_all_tags( $this->get_content_html() );
	}
}