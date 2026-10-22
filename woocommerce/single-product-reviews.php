<?php
/**
 * US-14.1 / US-14.2 : avis clients sur la fiche produit, comme la maquette.
 *
 * @package WooCommerce\Templates
 * @version 9.7.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

if ( ! comments_open() ) {
	return;
}

$nombre  = $product->get_review_count();
$moyenne = (float) $product->get_average_rating();

// Le client peut-il laisser un avis ? (réglage "propriétaires vérifiés" de WooCommerce)
$peut_noter = 'no' === get_option( 'woocommerce_review_rating_verification_required' )
	|| ( is_user_logged_in() && wc_customer_bought_product( '', get_current_user_id(), $product->get_id() ) );

// Juste après l'envoi, WordPress ajoute "unapproved" à l'adresse tant que l'avis n'est pas validé.
// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- simple affichage d'un message.
$avis_envoye = isset( $_GET['unapproved'] );
?>

<section id="reviews" class="avis">

	<h2 class="avis__titre">Avis clients</h2>

	<?php if ( $nombre ) : ?>
		<p class="avis__resume">
			<span class="avis__moyenne"><?php echo esc_html( number_format_i18n( $moyenne, 1 ) ); ?>/5</span>
			<?php echo watsons_etoiles( $moyenne ); // phpcs:ignore WordPress.Security.EscapeOutput -- déjà échappé. ?>
			<span class="avis__nombre"><?php echo esc_html( $nombre . ' avis' ); ?></span>
		</p>
	<?php endif; ?>

	<div class="avis__colonnes">

		<?php /* À gauche : la liste des avis */ ?>
		<div class="avis__liste">
			<?php if ( have_comments() ) : ?>
				<ol class="avis-liste">
					<?php wp_list_comments( array( 'callback' => 'watsons_un_avis' ) ); ?>
				</ol>
			<?php else : ?>
				<p class="avis__vide">Aucun avis pour l'instant. Soyez le premier à donner le vôtre !</p>
			<?php endif; ?>
		</div>

		<?php /* À droite : donner mon avis (3 états, comme le composant de la maquette) */ ?>
		<div class="avis__formulaire">
			<?php if ( $avis_envoye ) : ?>

				<div class="avis-info avis-info--merci">
					<p class="avis-info__titre">Merci pour votre avis !</p>
					<p class="avis-info__texte">Il sera publié après validation par le Watson's Pub.</p>
				</div>

			<?php elseif ( $peut_noter ) : ?>

				<?php
				$champs = '';

				if ( wc_review_ratings_enabled() ) {
					// WooCommerce transforme cette liste en 5 étoiles cliquables.
					$champs .= '<p class="comment-form-rating"><label for="rating">Votre note' . ( wc_review_ratings_required() ? '&nbsp;<span class="required">*</span>' : '' ) . '</label>
						<select name="rating" id="rating" required>
							<option value="">Choisir…</option>
							<option value="5">Parfait</option>
							<option value="4">Bien</option>
							<option value="3">Moyen</option>
							<option value="2">Pas terrible</option>
							<option value="1">Décevant</option>
						</select></p>';
				}

				$champs .= '<p class="comment-form-comment"><label for="comment">Votre commentaire&nbsp;<span class="required">*</span></label><textarea id="comment" name="comment" rows="5" placeholder="Qu\'avez-vous pensé de ce produit ?" required></textarea></p>';

				comment_form(
					array(
						'class_container'      => 'avis-form comment-respond',
						'title_reply'          => 'Donner mon avis',
						'title_reply_to'       => 'Donner mon avis',
						'title_reply_before'   => '<p id="reply-title" class="avis-form__titre">',
						'title_reply_after'    => '</p>',
						'logged_in_as'         => '',
						'comment_notes_before' => '',
						'comment_notes_after'  => '',
						'comment_field'        => $champs,
						'label_submit'         => 'Publier mon avis',
						'class_submit'         => 'avis-form__bouton',
					)
				);
				?>

			<?php else : ?>

				<div class="avis-info">
					<p class="avis-info__titre">Donner mon avis</p>
					<p class="avis-info__texte">
						Seuls les clients ayant acheté ce produit peuvent laisser un avis.
						<?php if ( ! is_user_logged_in() ) : ?>
							<a class="avis-info__lien" href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>">Connectez-vous</a> pour donner le vôtre.
						<?php endif; ?>
					</p>
				</div>

			<?php endif; ?>
		</div>

	</div>
</section>