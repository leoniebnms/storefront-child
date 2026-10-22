<?php
/**
 * US-11.3 : page d'un membre de l'équipe, comme la maquette.
 */

get_header();
?>

<div id="primary" class="content-area">
	<main id="main" class="site-main">

		<?php while ( have_posts() ) : ?>
			<?php
			the_post();

			// Les infos en pointillés (une ligne n'est affichée que si le champ est rempli).
			$infos = array(
				"Au Watson's depuis" => watsons_champ_membre( 'arrivee' ),
				'Son plat préféré'   => watsons_champ_membre( 'plat_prefere' ),
				'Spécialité'         => watsons_champ_membre( 'specialite' ),
			);
			$infos = array_filter( $infos );
			?>

			<a class="retour-carte" href="<?php echo esc_url( get_post_type_archive_link( 'membre' ) ); ?>"><svg class="retour-carte__icone" viewBox="0 0 9 14" aria-hidden="true"><path d="M8 1 2 7l6 6"/></svg>Retour à l'équipe</a>

			<article class="membre">
				<div class="membre__photo"><?php watsons_photo_membre(); ?></div>

				<div class="membre__texte">
					<h1 class="membre__nom"><?php the_title(); ?></h1>

					<?php if ( watsons_champ_membre( 'poste' ) ) : ?>
						<p class="membre__poste"><?php echo esc_html( watsons_champ_membre( 'poste' ) ); ?></p>
					<?php endif; ?>

					<div class="membre__presentation"><?php the_content(); ?></div>

					<?php if ( $infos ) : ?>
						<dl class="membre__infos">
							<?php foreach ( $infos as $libelle => $valeur ) : ?>
								<div class="membre__info">
									<dt class="membre__info-libelle"><?php echo esc_html( $libelle ); ?></dt>
									<dd class="membre__info-valeur"><?php echo esc_html( $valeur ); ?></dd>
								</div>
							<?php endforeach; ?>
						</dl>
					<?php endif; ?>
				</div>
			</article>

			<?php /* Le reste de l'équipe : pour passer d'un profil à l'autre */ ?>
			<?php
			$autres = get_posts(
				array(
					'post_type'      => 'membre',
					'posts_per_page' => -1,
					'post__not_in'   => array( get_the_ID() ),
					'orderby'        => array( 'menu_order' => 'ASC', 'title' => 'ASC' ),
				)
			);
			?>
			<?php if ( $autres ) : ?>
				<section class="membre-autres">
					<h2 class="membre-autres__titre">Le reste de l'equipe</h2>
					<ul class="membre-autres__liste">
						<?php foreach ( $autres as $autre ) : ?>
							<li>
								<a class="membre-mini" href="<?php echo esc_url( get_permalink( $autre ) ); ?>">
									<span class="membre-mini__photo"><?php watsons_photo_membre( $autre->ID ); ?></span>
									<span class="membre-mini__texte">
										<span class="membre-mini__nom"><?php echo esc_html( get_the_title( $autre ) ); ?></span>
										<span class="membre-mini__poste"><?php echo esc_html( watsons_champ_membre( 'poste', $autre->ID ) ); ?></span>
									</span>
								</a>
							</li>
						<?php endforeach; ?>
					</ul>
				</section>
			<?php endif; ?>

		<?php endwhile; ?>

	</main>
</div>

<?php
get_footer();