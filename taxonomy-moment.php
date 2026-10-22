<?php
/**
 * US-12.3 : page d'un moment (Le midi, Le goûter, La soirée) avec la grille de ses produits, comme la maquette.
 */

get_header();

$moment  = get_queried_object();
$moments = get_terms(
	array(
		'taxonomy'   => 'moment',
		'hide_empty' => true,
		'orderby'    => 'term_id',
	)
);
?>

<div id="primary" class="content-area">
	<main id="main" class="site-main moment">

		<?php /* Titre sans accent : la police Tall Dark n'en a pas */ ?>
		<header class="moment__entete">
			<p class="moment__sur-titre">Nos moments</p>
			<h1 class="moment__titre"><?php echo esc_html( remove_accents( $moment->name ) ); ?></h1>
			<?php if ( $moment->description ) : ?>
				<p class="moment__intro"><?php echo esc_html( $moment->description ); ?></p>
			<?php endif; ?>
		</header>

		<?php /* Les boutons pour passer d'un moment à l'autre (même style que les filtres de la carte) */ ?>
		<?php if ( ! is_wp_error( $moments ) && count( $moments ) > 1 ) : ?>
			<nav class="moment__onglets" aria-label="Nos moments">
				<?php foreach ( $moments as $autre ) : ?>
					<a class="carte-filtres__lien<?php echo $autre->term_id === $moment->term_id ? ' carte-filtres__lien--actif' : ''; ?>"
						href="<?php echo esc_url( get_term_link( $autre ) ); ?>"
						<?php echo $autre->term_id === $moment->term_id ? 'aria-current="page"' : ''; ?>>
						<?php echo esc_html( remove_accents( $autre->name ) ); ?>
					</a>
				<?php endforeach; ?>
			</nav>
		<?php endif; ?>

		<?php if ( have_posts() ) : ?>
			<ul class="moment__grille">
				<?php while ( have_posts() ) : ?>
					<?php
					the_post();
					$produit = wc_get_product( get_the_ID() );

					if ( ! $produit ) {
						continue;
					}

					// La catégorie en petit au-dessus du nom (Burgers, Boissons...).
					$categories = wc_get_product_terms( $produit->get_id(), 'product_cat', array( 'fields' => 'names' ) );

					// Produit simple : ajouté directement au panier. Produit à options : on va sur sa fiche pour choisir.
					$simple = $produit->is_type( 'simple' ) && $produit->is_purchasable() && $produit->is_in_stock();
					?>
					<li class="produit-carte">
						<a class="produit-carte__photo" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
							<?php echo $produit->get_image( 'woocommerce_thumbnail', array( 'class' => 'produit-carte__image' ) ); // phpcs:ignore WordPress.Security.EscapeOutput -- image WooCommerce. ?>
						</a>

						<?php if ( $categories ) : ?>
							<p class="produit-carte__categorie"><?php echo esc_html( $categories[0] ); ?></p>
						<?php endif; ?>

						<h2 class="produit-carte__nom"><a href="<?php the_permalink(); ?>"><?php echo esc_html( $produit->get_name() ); ?></a></h2>
						<p class="produit-carte__prix"><?php echo wp_kses_post( $produit->get_price_html() ); ?></p>

						<a class="produit-carte__bouton" href="<?php echo esc_url( $simple ? $produit->add_to_cart_url() : get_permalink() ); ?>" rel="nofollow">
							<?php echo $simple ? 'Ajouter' : 'Choisir'; ?><span class="screen-reader-text"> <?php echo esc_html( $produit->get_name() ); ?></span>
						</a>
					</li>
				<?php endwhile; ?>
			</ul>
		<?php else : ?>
			<p class="moment__vide">Aucun produit pour ce moment pour l'instant.</p>
		<?php endif; ?>

	</main>
</div>

<?php
get_footer();