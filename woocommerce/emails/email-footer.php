<?php
/**
 * US-8.2 : bas de tous les e-mails, bandeau rouille comme sur la maquette.
 *
 * L'adresse et le téléphone viennent du "Texte du pied de page" (WooCommerce > Réglages > E-mails).
 *
 * @package WooCommerce\Templates\Emails
 * @version 10.0.0
 */

defined( 'ABSPATH' ) || exit;

$texte_pied = apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) );
?>
						</td>
					</tr>
					<tr>
						<td class="mail__pied" align="center">
							<p class="mail__pied-titre">Le Watson's Pub</p>
							<div class="mail__pied-texte"><?php echo wp_kses_post( wpautop( wptexturize( $texte_pied ) ) ); ?></div>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
</body>
</html>