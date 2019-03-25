<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://presstigers.com
 * @since      1.0.0
 *
 * @package    Woo_Gf_Variations_Exporter
 * @subpackage Woo_Gf_Variations_Exporter/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Woo_Gf_Variations_Exporter
 * @subpackage Woo_Gf_Variations_Exporter/admin
 * @author     Waqas Hass <waqas.hassan@nxb.com.pk>
 */
class Woo_Gf_Variations_Exporter_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		//wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/woo-gf-variations-exporter-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/woo-gf-variations-exporter-admin.js', array( 'jquery' ), $this->version, false );

	}


	public function register_menu_page(){
		$page_title = 'NextBridge Gravity WooCommerce Product Exporter';
		$menu_title = 'GravWoo';
		$capability = 'manage_options';
		$menu_slug  = 'nb_gravity_woo_exporter_options';
		$function   =  array($this, 'display_page');
		$icon_url   = 'dashicons-media-spreadsheet';
		$position   =  4;

		add_menu_page( $page_title,
					$menu_title, 
					$capability, 
					$menu_slug, 
					$function, 
					$icon_url, 
					$position );

	}

	public function display_page() { 
		require(plugin_dir_path( __FILE__ ).'partials\woo-gf-variations-exporter-admin-display.php'); 
	}

	public function process_csv_request() {

		if ( ! current_user_can( 'activate_plugins' ) )
			return;	

		if(!isset($_POST["submit"]))
			return;
		
		$retrieved_nonce = esc_attr($_REQUEST['_wpnonce']);
		if (!wp_verify_nonce($retrieved_nonce, 'export_action' ) ) 
			die( 'Failed security check' );
		

		$prod_categories 	= isset($_POST['prod_categories']) ? esc_html(trim(implode(",", $_POST['prod_categories']))) : ""; 
		if(!empty($prod_categories))
			$prod_categories 	= explode(",",$prod_categories);
		$start_range 		= (isset($_POST['range_id_start']) && !empty($_POST['range_id_start']) ) ?  (int)filter_var($_POST['range_id_start'], FILTER_SANITIZE_NUMBER_INT) : 0;
		$end_range 			= (isset($_POST['range_id_end']) && !empty($_POST['range_id_end']) ) ? (int)filter_var($_POST['range_id_end'],   FILTER_SANITIZE_NUMBER_INT) : 0;

		set_time_limit(0);
		$this->collect_data_and_save_as_csv($prod_categories, $start_range, $end_range);
			
		die();
		
	}
	
	public function collect_data_and_save_as_csv($prod_categories = array(), $start_range=0, $end_range=0){
			
		$args = array(
			'post_type'      => 'product',
			'posts_per_page' => -1,
			'post_status'    => 'publish',
			'order' => 'ASC'
		);
		if( !empty($prod_categories)){
			$args["tax_query"] =  array(
										array(
											'taxonomy'      => 'product_cat',
											'terms'         => $prod_categories,
											'field'         => 'id',
											'operator'      => "IN",
										),
									);
		}

		if(0 !== $start_range && 0 !== $end_range){
			$args["post__in"]	=	range(  min($start_range, $end_range),
											max($start_range, $end_range)
									);

		}
		$args = apply_filters( 'nb_gravity_woo_query_args', $args );
		$loop = new WP_Query( $args );
		$header = array("id", "title", "link", "price");
		$rows = array();
		while ( $loop->have_posts() ) : $loop->the_post();
			global $product;
			
			if( $product->has_child() ) { 
				//variation
				$variations=$product->get_children();
				foreach ($variations as $value) {
					$single_variation = new WC_Product_Variation($value);
					$row = array();
					$row["id"]=get_the_ID();
					$row["title"]=get_the_title();
					$row["link"]=get_permalink();
					$row["price"]=$single_variation->price;
					foreach($single_variation->get_variation_attributes() as $key => $value){
						$key = str_replace("attribute_","", $key);
						$row["".$key] = $value;
						$key = strtolower($key);
						if (!in_array($key, $header)){
							$header[] = $key; 
						}
					}
					$rows[] = $row;
				}
			}		
			else{
				//simple product
				$row  = array();
				$row["id"]=get_the_ID();
				$row["title"]=get_the_title();
				$row["link"]=get_permalink();
				$row["price"]=$product->price;
				foreach($product->get_attributes() as $key => $value){
					$options = array();
					foreach($value["options"] as $option){
						$options[] = $option;
					}
					$options = implode(",", $options);
					$row["".$value["name"]] = $options;
					$value["name"] = strtolower( $value["name"]);
					if (!in_array($value["name"], $header)){
						$header[] = $value["name"]; 
					}
					
				}
				$rows[] = $row;
			}
		endwhile;
		wp_reset_query();
		do_action("nb_gravity_woo_after_data_creation", $header, $rows );
		$this->save_as_csv($header, $rows);
		
	}

	function save_as_csv($header, $rows){

		$output_filename 	= apply_filters('nb_gravity_woo_csv_file_name', 'Product Pricing Catalogue' .'.csv');
		$output_handle 		= @fopen('php://output', 'w');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Content-Description: File Transfer');
		header('Content-type: text/csv');
		header('Content-Disposition: attachment; filename=' . $output_filename);
		header('Expires: 0');
		header('Pragma: public');

		try{
			//heading row
			fputcsv($output_handle, $header);
			foreach($rows as $row){
				$save_row = array();
				foreach($header as $heading){
					$save_row[]=$row[$heading];
				}
				fputcsv($output_handle, $save_row);
			}
		}
		catch(Exception $e){
			fputcsv($output_handle, array("Some Error Occurred. Kindly consult the admin. Here is the error information: ", $e->getMessage()));
			
		}

		
		fclose($output_handle);
	}

}
