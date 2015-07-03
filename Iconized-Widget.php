<?php
/**
 * Plugin Name:       Iconized List
 * Plugin URI:        https://github.com/sofyansitorus/Iconized-List
 * Description:       Show unordered list with icon on your WordPress site widget.
 * Version:           1.0.0
 * Author:            Sofyan Sitorus
 * Author URI:        https://github.com/sofyansitorus
 * Text Domain:       agmw
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /lang
 * GitHub Plugin URI: https://github.com/sofyansitorus/Iconized-List
 */
 
 // Prevent direct file access
if ( ! defined ( 'ABSPATH' ) ) {
	exit;
}


class IconizedList extends WP_Widget {

    /**
     * @since    1.0.0
     *
     * @var      string
     */
    protected $widget_slug = 'iconized-list';

    protected $width = 0;

    protected $height = 0;

    private $max_list = 10;

	/*--------------------------------------------------*/
	/* Constructor
	/*--------------------------------------------------*/

	/**
	 * Specifies the classname and description, instantiates the widget,
	 * loads localization files, and includes necessary stylesheets and JavaScript.
	 */
	public function __construct() {

		// load plugin text domain
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

		// Hooks fired when the Widget is activated and deactivated
		register_activation_hook( __FILE__, array( $this, 'activate' ) );
		register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );

		parent::__construct(
			$this->get_slug(),
			$this->get_widget_name(),
			array(
				'classname'  => $this->get_widget_class(),
				'description' => $this->get_widget_description()
			),
			array(
				'width' => $this->width, 
				'height' => $this->height
			)
		);

		// Register admin styles and scripts
		add_action( 'admin_print_styles', array( $this, 'register_admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'register_admin_scripts' ) );

		// Register site styles and scripts
		if(!is_admin() && is_active_widget(false, false, $this->get_slug())){
			add_action( 'wp_enqueue_scripts', array( $this, 'register_widget_styles' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'register_widget_scripts' ) );
		}
	
	} // end constructor


    /**
     * Return the widget slug.
     *
     * @since    1.0.0
     *
     * @return    Plugin slug variable.
     */
    public function get_slug() {
        return $this->widget_slug;
    }

    /**
     * Return the widget name.
     *
     * @since    1.0.0
     *
     * @return    Plugin name variable.
     */
    public function get_widget_name() {
        return __( 'Iconized List', $this->get_slug() );
    }

    /**
     * Return the widget description.
     *
     * @since    1.0.0
     *
     * @return    Plugin description variable.
     */
    public function get_widget_description() {
        return __( 'Show unordered list with icon on your WordPress site widget.', $this->get_slug() );
    }

    /**
     * Return the widget class.
     *
     * @since    1.0.0
     *
     * @return    Plugin class variable.
     */
    public function get_widget_class() {
        return $this->get_slug();
    }
	
	/**
     * Delete widget cache
     */
	public function flush_widget_cache() {
    	wp_cache_delete( $this->get_slug(), 'widget' );
	}

	/*--------------------------------------------------*/
	/* Widget API Functions
	/*--------------------------------------------------*/

	/**
	 * Generates the administration form for the widget.
	 *
	 * @param array instance The array of keys and values for the widget.
	 */
	public function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'max_list' => 1, 'icon_size' => '', 'title' => '') );
		$title = esc_attr( $instance['title'] );
		$list_icon = array();
		for ($i=1; $i <= $this->max_list ; $i++) {
			$list_icon[$i] = isset($instance['list_icon_'.$i]) ? $instance['list_icon_'.$i] : '';
			$list_text[$i] = isset($instance['list_text_'.$i]) ? $instance['list_text_'.$i] : '';
		}
		$font_awesome = json_decode(file_get_contents(plugin_dir_path( __FILE__ ).'font-awesome-data.json'));
	?>
		<div class="iconized-list-container">
		<p>
		<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', $this->get_slug()); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('max_list'); ?>"><?php _e( 'Number of icon list to display:' ); ?></label>
			<select name="<?php echo $this->get_field_name('max_list'); ?>" id="<?php echo $this->get_field_id('max_list'); ?>" class="widefat iconized-list-max">
				<?php
				for ($i=1; $i <= $this->max_list ; $i++) { 
				?>
				<option value="<?php echo $i; ?>"<?php selected( $instance['max_list'], $i ); ?>><?php echo $i; ?></option>
				<?php
				}
				?>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('icon_size'); ?>"><?php _e( 'Icon size:' ); ?></label>
			<select name="<?php echo $this->get_field_name('icon_size'); ?>" id="<?php echo $this->get_field_id('icon_size'); ?>" class="widefat iconized-list-size">
				<option value=""<?php selected( $instance['icon_size'], '' ); ?>><?php _e('Normal', $this->get_slug()); ?></option>
				<option value="fa-lg"<?php selected( $instance['icon_size'], 'fa-lg' ); ?>><?php _e('Large', $this->get_slug()); ?></option>
				<option value="fa-2x"<?php selected( $instance['icon_size'], 'fa-2x' ); ?>><?php _e('2X', $this->get_slug()); ?></option>
				<option value="fa-3x"<?php selected( $instance['icon_size'], 'fa-3x' ); ?>><?php _e('3X', $this->get_slug()); ?></option>
				<option value="fa-4x"<?php selected( $instance['icon_size'], 'fa-4x' ); ?>><?php _e('4X', $this->get_slug()); ?></option>
				<option value="fa-5x"<?php selected( $instance['icon_size'], 'fa-5x' ); ?>><?php _e('5X', $this->get_slug()); ?></option>
			</select>
		</p>
		<?php
		for ($i=1; $i <= $this->max_list ; $i++) {
			$item_class = ($i <= $instance['max_list']) ? ' visible' : ' hidden';
		?>
		<div data-counter="<?php echo $i; ?>" class="iconized-list-item<?php echo $item_class;?>">
		<hr>
		<p>
		<strong><?php echo sprintf(__('List %d:', $this->get_slug()), $i); ?></strong>
		</p>
		<div>
			<label for="<?php echo $this->get_field_id('list_icon_'.$i); ?>"><?php _e( 'Icon:', $this->get_slug() ); ?></label>
			<div class="iconized-list-wrapper">
				<div class="dropdown">
				<select name="<?php echo $this->get_field_name('list_icon_'.$i); ?>" id="<?php echo $this->get_field_id('list_icon_'.$i); ?>" class="widefat iconized">
					<?php
					foreach ($font_awesome as $key => $value) {
					?>
					<option value="<?php echo $key; ?>"<?php selected( $list_icon[$i], $key ); ?>><?php echo $key; ?></option>
					<?php
					}
					?>
				</select>
				</div>
				<div class="preview">
				<i class="fa <?php echo $instance['icon_size']; ?> <?php echo $list_icon[$i]; ?>"></i>
				</div>
			</div>
		</div>
		<p>
			<label for="<?php echo $this->get_field_id('list_text_'.$i); ?>"><?php _e( 'Text:', $this->get_slug() ); ?></label>
			<input type="text" value="<?php echo $list_text[$i]; ?>" name="<?php echo $this->get_field_name('list_text_'.$i); ?>" id="<?php echo $this->get_field_id('list_text_'.$i); ?>" class="widefat" />
			<br />
			<small><?php _e( 'Shortcode is allowed.', $this->get_slug() ); ?></small>
		</p>
		</div>
		<?php
		}
		?>
		</div>
	<?php
	} // end form

	/**
	 * Outputs the content of the widget.
	 *
	 * @param array args  The array of form elements
	 * @param array instance The current instance of the widget
	 */
	public function widget( $args, $instance ) {

		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );
		
		$max_list = empty( $instance['max_list'] ) ? 0 : absint($instance['max_list']);
		$icon_size = empty( $instance['icon_size'] ) ? '' : $instance['icon_size'];

		$output = $args['before_widget'];

		if ( $title ) {
			$output .= $args['before_title'] . $title . $args['after_title'];
		}

		if($max_list){
			$output .= '<ul>';
			for ($i=1; $i <= $max_list ; $i++) { 
				$output .= sprintf(
					'<li><span class="col-icon col-icon-%s"><i class="fa %s %s"></i></span> <span class="col-text">%s</span></li>',
					$icon_size,
					$icon_size,
					empty($instance['list_icon_'.$i]) ? '' : $instance['list_icon_'.$i],
					empty($instance['list_text_'.$i]) ? '' : do_shortcode($instance['list_text_'.$i])
				);
			}
			$output .= '</ul>';
		}

		// Process the widget ouput here
		$output .= $args['after_widget'];

		print $output;

	} // end widget

	/**
	 * Loads the Widget's text domain for localization and translation.
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain( $this->get_slug(), false, plugin_dir_path( __FILE__ ) . 'lang/' );
	} // end load_plugin_textdomain

	/**
	 * Fired when the plugin is activated.
	 *
	 * @param  boolean $network_wide True if WPMU superadmin uses "Network Activate" action, false if WPMU is disabled or plugin is activated on an individual blog.
	 */
	public function activate( $network_wide ) {
		// TODO define activation functionality here
	} // end activate

	/**
	 * Fired when the plugin is deactivated.
	 *
	 * @param boolean $network_wide True if WPMU superadmin uses "Network Activate" action, false if WPMU is disabled or plugin is activated on an individual blog
	 */
	public function deactivate( $network_wide ) {
		// TODO define deactivation functionality here
	} // end deactivate

	/**
	 * Registers and enqueues admin-specific styles.
	 */
	public function register_admin_styles() {
		wp_enqueue_style( 'font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css' );
		wp_enqueue_style( $this->get_slug().'-admin-styles', plugins_url( 'assets/css/admin.css', __FILE__ ) );
	} // end register_admin_styles

	/**
	 * Registers and enqueues admin-specific JavaScript.
	 */
	public function register_admin_scripts() {
		wp_enqueue_script( $this->get_slug().'-admin-script', plugins_url( 'assets/js/admin.js', __FILE__ ), array('jquery') );

	} // end register_admin_scripts

	/**
	 * Registers and enqueues widget-specific styles.
	 */
	public function register_widget_styles() {
		wp_enqueue_style( 'font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css' );
		wp_enqueue_style( $this->get_slug(), plugins_url( 'assets/css/iconized-list.css', __FILE__ ) );
	} // end register_widget_styles

	/**
	 * Registers and enqueues widget-specific scripts.
	 */
	public function register_widget_scripts() {
		wp_enqueue_script( $this->get_slug(), plugins_url( 'assets/js/iconized-list.js', __FILE__ ), array('jquery') );
	} // end register_widget_scripts

} // end class

add_action( 'widgets_init', create_function( '', 'register_widget("IconizedList");' ) );
