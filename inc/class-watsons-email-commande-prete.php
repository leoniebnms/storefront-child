<?php
/**
 * US-9.2 : e-mail "Votre commande est prête !".
 */

defined( 'ABSPATH' ) || exit;

class Watsons_Email_Commande_Prete extends WC_Email {

	public function __construct() {
		$this->id             = 'watsons_commande_prete';
		$this->customer_email = true;
		$this->title          = 'Commande prête';
		$this->description    = 'Envoyé au client quand la commande passe au statut « Prête » (retrait en boutique).';
		$this->template_html  = 'emails/customer-ready-order.php';
		$this->template_plain = 'emails/customer-ready-order.php';
		$this->placeholders   = array(
			'{order_date}'   => '',
			'{order_number}' => '',
		);

		// Déclenché par le passage au statut "Prête" (voir woocommerce_email_actions dans functions.php).
		add_action( 'woocommerce_order_status_prete_notification', array( $this, 'trigger' ), 10, 2 );

		parent::__construct();
	}

	// Objet et titre par défaut (modifiables dans les réglages).
	public function get_default_subject() {
		return "Votre commande n° {order_number} au Watson's Pub est prête !";
	}

	public function get_default_heading() {
		return 'Votre commande est prête !';
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

	// Version texte (si le client a désactivé le HTML) : le même contenu sans les balises.
	public function get_content_plain() {
		return wp_strip_all_tags( $this->get_content_html() );
	}
}