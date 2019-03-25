<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://presstigers.com
 * @since      1.0.0
 *
 * @package    Woo_Gf_Variations_Exporter
 * @subpackage Woo_Gf_Variations_Exporter/admin/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<?php

$taxonomy     = 'product_cat';
$orderby      = 'name';  
$show_count   = 0;      // 1 for yes, 0 for no
$pad_counts   = 0;      // 1 for yes, 0 for no
$hierarchical = 0;      // 1 for yes, 0 for no  
$title        = '';  
$empty        = 0;

$args = array(
	   'taxonomy'     => $taxonomy,
	   'orderby'      => $orderby,
	   'show_count'   => $show_count,
	   'pad_counts'   => $pad_counts,
	   'hierarchical' => $hierarchical,
	   'title_li'     => $title,
	   'hide_empty'   => $empty
);
$all_categories = get_categories( $args );
?>

<div class="wrap">
    
    <h2><?php _e('NextBridge Gravity WooCommerce Exporter Options'); ?></h2>
    	    	
	<div>
	
		<div id="post-body" class="columns-3">
			
			<!-- main content -->
			<div id="post-body-content">
				
				<div class="meta-box">
					
					 <form method="post" action="">
						<?php
						wp_nonce_field('export_action');
						?>

    					<h2><?php _e('Settings'); ?></h2>

    					<table class="form-table">
    						<tbody>
    							<tr valign="top">
    								<th scope="row">Select Product Categor(y/ies)</th>
								</tr>
								<tr valign="top">
    								<td><label for="prod_categories">Allows Multiple Selections</label>
									<select name = 'prod_categories[]' multiple >   
										<?php 
    										foreach ( $all_categories  as $category ) {
										       echo '<option value="'. $category->term_id .'" >' . $category->name . '</option>';
											}
										?>
										
									</select> 
									</td>
								</tr>
								<tr>
									<th scope="row">Select Range</th>
								</tr>
								<tr valign="top">
    								<td><label for="range_id_start">Select Start Product ID</label>
										<input type="text" class="regular-text" name="range_id_start" placeholder="From Product ID" onkeypress="javascript:return isNumber(event)">
									</td>
								</tr>
								<tr valign="top">
									<td>
										<label for="range_id_end">Select End Product ID</label>
										<input type="text" class="regular-text" name="range_id_end" placeholder="End Product ID" onkeypress="javascript:return isNumber(event)">
									</td>
    							</tr>
    						
    						</tbody>
    					</table>

    					<?php submit_button("Export Product Pricing Catalogue"); ?>

					</form>
					
				</div>
			</div>
		</div>
		
		<br class="clear">
		
	</div>
			
</div>
