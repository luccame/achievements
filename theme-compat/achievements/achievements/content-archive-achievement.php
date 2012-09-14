<?php
/**
 * Archive Achievement content part
 *
 * @package Achievements
 * @subpackage ThemeCompatibility
 */
?>

<div id="dpa-achievements">

	<?php dpa_breadcrumb(); ?>

	<?php do_action( 'dpa_template_before_achievements_index' ); ?>

	<?php if ( dpa_has_achievements() ) : ?>

		<?php dpa_get_template_part( 'pagination', 'achievements' ); ?>

		<?php dpa_get_template_part( 'loop', 'achievements'       ); ?>

		<?php dpa_get_template_part( 'pagination', 'achievements' ); ?>

	<?php else : ?>

		<?php dpa_get_template_part( 'feedback', 'no-achievements' ); ?>

	<?php endif; ?>

	<?php do_action( 'dpa_template_after_achievements_index' ); ?>

</div>