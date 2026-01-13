<?php
/**
 * Default page
 *
 * @package bstr\admin\pages
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * BSTGR Boost data page callback function
 *
 * @return void
 */
function bstr_boost_data_page() {
	?>
<div id="wp-script">
	<div class="content-tabs">
		<?php WPSCORE()->display_logo(); ?>
		<?php WPSCORE()->display_tabs(); ?>
		<div class="tab-content">
			<div class="tab-pane fade in active" id="boost-data">
				<!-- empty div for auto margin -->
				<div></div>
				<div id="bstr-boost-data"></div>
			</div>
		</div>
		<?php WPSCORE()->display_footer(); ?>
	</div>
</div>
	<?php
}

