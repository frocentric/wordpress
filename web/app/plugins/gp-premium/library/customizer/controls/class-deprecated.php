<?php
// No direct access, please
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( class_exists( 'WP_Customize_Control' ) && ! class_exists( 'Generate_Customize_Spacing_Slider_Control' ) ) :
/**
 * Create our container width slider control
 * @deprecated 1.3
 */
class Generate_Customize_Spacing_Slider_Control extends WP_Customize_Control
{
	// Setup control type
	public $type = 'gp-spacing-slider';
	public $id = '';
	public $default_value = '';
	public $unit = '';
	public $edit_field = true;
	
	public function to_json() {
		parent::to_json();
		$this->json[ 'link' ] = $this->get_link();
		$this->json[ 'value' ] = $this->value();
		$this->json[ 'id' ] = $this->id;
		$this->json[ 'default_value' ] = $this->default_value;
		$this->json[ 'reset_title' ] = esc_attr__( 'Reset','generate-spacing' );
		$this->json[ 'unit' ] = $this->unit;
		$this->json[ 'edit_field' ] = $this->edit_field;
	}
	
	public function content_template() {
		?>
		<label>
			<p style="margin-bottom:0;">
				<span class="spacing-size-label customize-control-title">
					{{ data.label }}
				</span> 
				<span class="value">
					<input <# if ( '' == data.unit || ! data.edit_field ) { #>style="display:none;"<# } #> name="{{ data.id }}" type="number" {{{ data.link }}} value="{{{ data.value }}}" class="slider-input" /><span <# if ( '' == data.unit || ! data.edit_field ) { #>style="display:none;"<# } #> class="px">{{ data.unit }}</span>
					<# if ( '' !== data.unit && ! data.edit_field ) { #><span class="no-edit-field"><span class="no-edit-value">{{ data.value }}</span>{{ data.unit }}</span><# } #>
				</span>
			</p>
		</label>
		<div class="slider gp-flat-slider <# if ( '' !== data.default_value ) { #>show-reset<# } #>"></div>
		<# if ( '' !== data.default_value ) { #><span style="cursor:pointer;" title="{{ data.reset_title }}" class="gp-spacing-slider-default-value" data-default-value="{{ data.default_value }}"><span class="gp-customizer-icon-undo" aria-hidden="true"></span><span class="screen-reader-text">{{ data.reset_title }}</span></span><# } #>
		<?php
	}
	
	// Function to enqueue the right jquery scripts and styles
	public function enqueue() {
		
		wp_enqueue_script( 'gp-spacing-customizer', trailingslashit( plugin_dir_url( __FILE__ ) )  . 'js/spacing-customizer.js', array( 'customize-controls' ), GENERATE_SPACING_VERSION, true );
		wp_enqueue_style( 'gp-spacing-customizer-controls-css', trailingslashit( plugin_dir_url( __FILE__ ) ) . 'css/customizer.css', array(), GENERATE_SPACING_VERSION );
		
		wp_enqueue_script( 'jquery-ui-core' );
		wp_enqueue_script( 'jquery-ui-slider' );
		
		wp_enqueue_script( 'generate-spacing-slider-js', trailingslashit( plugin_dir_url( __FILE__ ) ) . 'js/spacing-slider.js', array( 'jquery-ui-slider' ), GENERATE_SPACING_VERSION );
		
		wp_enqueue_style('generate-ui-slider', trailingslashit( plugin_dir_url( __FILE__ ) ) . 'css/jquery-ui.structure.css');
		wp_enqueue_style('generate-flat-slider', trailingslashit( plugin_dir_url( __FILE__ ) ) . 'css/range-slider.css');
		
	}
}
endif;

if ( class_exists( 'WP_Customize_Control' ) && ! class_exists( 'Generate_Spacing_Customize_Control' ) ) :
/* 
 * Add our control for our padding options
 * @deprecated 1.2.95
 */ 
class Generate_Spacing_Customize_Control extends WP_Customize_Control {
	public $type = 'spacing';
	public $description = '';
	
	public function enqueue() {
		wp_enqueue_script( 'gp-spacing-customizer', plugin_dir_url( __FILE__ )  . 'js/spacing-customizer.js', array( 'customize-controls' ), GENERATE_SPACING_VERSION, true );
	}
	
	public function to_json() {
		parent::to_json();
		$this->json[ 'link' ] = $this->get_link();
		$this->json[ 'value' ] = absint( $this->value() );
		$this->json[ 'description' ] = esc_html( $this->description );
	}
	
	public function content_template() {
		?>
		<label>
			<# if ( data.label ) { #>
				<span class="customize-control-title">{{ data.label }}</span>
			<# } #>
			
			<input class="generate-number-control" type="number" style="text-align: center;" {{{ data.link }}} value="{{{ data.value }}}" />
			
			<# if ( data.description ) { #>
				<span class="description" style="font-style:normal;">{{ data.description }}</span>
			<# } #>
		</label>
		<?php
	}
}
endif;

if ( class_exists( 'WP_Customize_Control' ) && ! class_exists( 'Generate_Spacing_Customize_Misc_Control' ) ) :
/* 
 * Add a class to display headings
 * @deprecated 1.2.95
 */ 
class Generate_Spacing_Customize_Misc_Control extends WP_Customize_Control {
    public $settings = 'generate_spacing_headings';
    public $description = '';
	public $areas = '';
 
    public function render_content() {
        switch ( $this->type ) {
            default:
            case 'text' : ?>
				<label>
					<span class="customize-control-title"><?php echo $this->description;?></span>
				</label>
			<?php break;
 
            case 'spacing-heading':
                if ( ! empty( $this->label ) ) echo '<span class="customize-control-title spacing-title">' . esc_html( $this->label ) . '</span>';
				if ( ! empty( $this->description ) ) echo '<span class="spacing-title-description">' . esc_html( $this->description ) . '</span>';
				if ( ! empty( $this->areas ) ) :
					echo '<div style="clear:both;display:block;"></div>';
					foreach ( $this->areas as $value => $label ) :
						echo '<span class="spacing-area">' . esc_html( $label ) . '</span>';
					endforeach;
				endif;
			break;
 
            case 'line' :
                echo '<hr />';
			break;
        }
    }
}
endif;

if ( class_exists( 'WP_Customize_Control' ) && ! class_exists( 'Generate_Backgrounds_Customize_Control' ) ) :
/*
 * @deprecated 1.3
 */
class Generate_Backgrounds_Customize_Control extends WP_Customize_Control {
	public function render() {}
}
endif;

if ( class_exists( 'WP_Customize_Control' ) && ! class_exists( 'Generate_Backgrounds_Customize_Misc_Control' ) ) :
/*
 * No longer used
 * Kept for back compat purposes
 * @deprecated 1.2.95
 */
class Generate_Backgrounds_Customize_Misc_Control extends WP_Customize_Control {
	public function render() {}
}
endif;

if ( class_exists( 'WP_Customize_Control' ) && ! class_exists( 'Generate_Blog_Customize_Control' ) ) :
/**
 * Add our number input field for the featured image width
 * @deprecated 1.3
 */
class Generate_Blog_Customize_Control extends WP_Customize_Control {
	public $type = 'gp-post-image-size';
	public $placeholder = '';
	
	public function enqueue() {
		wp_enqueue_script( 'gp-blog-customizer', trailingslashit( plugin_dir_url( __FILE__ ) ) . 'js/blog-customizer.js', array( 'customize-controls' ), GENERATE_BLOG_VERSION, true );
	}
	
	public function to_json() {
		parent::to_json();
		$this->json[ 'link' ] = $this->get_link();
		$this->json[ 'value' ] = $this->value();
		$this->json[ 'placeholder' ] = $this->placeholder;
	}
	public function content_template() {
		?>
		<label>
			<span class="customize-control-title">{{{ data.label }}}</span>
			<input class="blog-size-input" placeholder="{{{ data.placeholder }}}" style="max-width:75px;text-align:center;" type="number" {{{ data.link }}} value="{{ data.value }}" />px
		</label>
		<?php
	}
}
endif;

if ( class_exists( 'WP_Customize_Control' ) && ! class_exists( 'Generate_Blog_Number_Customize_Control' ) ) :
/**
 * Add a regular number input control
 * @deprecated 1.3
 */
class Generate_Blog_Number_Customize_Control extends WP_Customize_Control {
	public $type = 'gp-blog-number';
	public $placeholder = '';
	
	public function enqueue() {
		wp_enqueue_script( 'gp-blog-customizer', trailingslashit( plugin_dir_url( __FILE__ ) )  . 'js/blog-customizer.js', array( 'customize-controls' ), GENERATE_BLOG_VERSION, true );
	}
	
	public function to_json() {
		parent::to_json();
		$this->json[ 'link' ] = $this->get_link();
		$this->json[ 'value' ] = $this->value();
		$this->json[ 'placeholder' ] = $this->placeholder;
	}
	public function content_template() {
		?>
		<label>
			<span class="customize-control-title">{{{ data.label }}}</span>
			<input class="blog-size-input" placeholder="{{{ data.placeholder }}}" type="number" {{{ data.link }}} value="{{ data.value }}" />
		</label>
		<?php
	}
}
endif;

if ( class_exists( 'WP_Customize_Control' ) && ! class_exists( 'Generate_Post_Image_Save' ) ) :
/**
 * Add a button to initiate refresh when changing featured image sizes
 * @deprecated 1.3
 */
class Generate_Post_Image_Save extends WP_Customize_Control {
	public function render() {}
}
endif;

if ( class_exists( 'WP_Customize_Control' ) && ! class_exists( 'Generate_Blog_Text_Control' ) ) :
/**
 * Add a control to display simple text
 * @deprecated 1.3
 */
class Generate_Blog_Text_Control extends WP_Customize_Control {
	public function render() {}
}
endif;

if ( ! class_exists( 'Generate_Customize_Alpha_Color_Control' ) ) :
/**
 * @deprecated 1.3
 */
class Generate_Customize_Alpha_Color_Control extends WP_Customize_Control {
	public function render() {}
}
endif;

if ( ! class_exists( 'Generate_Copyright_Textarea_Custom_Control' ) ) :
/**
 * Class to create a custom tags control
 * @deprecated 1.3
 */
class Generate_Copyright_Textarea_Custom_Control extends WP_Customize_Control {
	public function render() {}
}
endif;

if ( class_exists( 'WP_Customize_Control' ) && ! class_exists( 'Generate_Blog_Page_Header_Image_Save' ) ) :
/**
 * Add a control without a button to refresh the frame
 * This kicks in our image dimension settings
 *
 * @deprecated 1.3
 */
class Generate_Blog_Page_Header_Image_Save extends WP_Customize_Control {
	public $type = 'page_header_image_save';
	
	public function to_json() {
		parent::to_json();
		$this->json[ 'text' ] = __( 'Apply image sizes','page-header' );
	}
	
	public function content_template() {
		?>
		<a class="button save-post-images" onclick="wp.customize.previewer.refresh();" href="#">{{{ data.text }}}</a>
		<?php
	}
}
endif;

if ( ! class_exists( 'Generate_Hidden_Input_Control' ) ) :
/**
 * Create our hidden input control
 * @deprecated 1.3
 */
class Generate_Hidden_Input_Control extends WP_Customize_Control
{
	// Setup control type
	public $type = 'gp-hidden-input';
	public $id = '';
	
	public function to_json() {
		parent::to_json();
		$this->json[ 'link' ] = $this->get_link();
		$this->json[ 'value' ] = $this->value();
		$this->json[ 'id' ] = $this->id;
	}
	
	public function content_template() {
		?>
		<input name="{{ data.id }}" type="text" {{{ data.link }}} value="{{{ data.value }}}" class="gp-hidden-input" />
		<?php
	}
}
endif;

if ( ! class_exists( 'Generate_Text_Transform_Custom_Control' ) ) :
/**
 * A class to create a dropdown for text-transform
 * @deprecated 1.3
 */
class Generate_Text_Transform_Custom_Control extends WP_Customize_Control
{
    public function __construct($manager, $id, $args = array(), $options = array())
    {
        parent::__construct( $manager, $id, $args );
    }
    /**
     * Render the content of the category dropdown
     *
     * @return HTML
     */
    public function render_content()
    {
        ?>
        <label>
			<select <?php $this->link(); ?>>
				<?php 
				printf('<option value="%s" %s>%s</option>', 'none', selected($this->value(), 'none', false), 'none');
				printf('<option value="%s" %s>%s</option>', 'capitalize', selected($this->value(), 'capitalize', false), 'capitalize');
				printf('<option value="%s" %s>%s</option>', 'uppercase', selected($this->value(), 'uppercase', false), 'uppercase');
				printf('<option value="%s" %s>%s</option>', 'lowercase', selected($this->value(), 'lowercase', false), 'lowercase');
				?>
            </select>
			<p class="description"><?php echo esc_html( $this->label ); ?></p>
        </label>
        <?php
    }
}
endif;

if ( ! class_exists( 'Generate_Font_Weight_Custom_Control' ) ) :
/**
 * A class to create a dropdown for font weight
 * @deprecated 1.3
 */
class Generate_Font_Weight_Custom_Control extends WP_Customize_Control
{
    public function __construct($manager, $id, $args = array(), $options = array())
    {
        parent::__construct( $manager, $id, $args );
    }
    /**
     * Render the content of the category dropdown
     *
     * @return HTML
     */
    public function render_content()
    {
        ?>
        <label>
			<select <?php $this->link(); ?>>
				<?php 
				printf('<option value="%s" %s>%s</option>', 'normal', selected($this->value(), 'normal', false), 'normal');
				printf('<option value="%s" %s>%s</option>', 'bold', selected($this->value(), 'bold', false), 'bold');
				printf('<option value="%s" %s>%s</option>', '100', selected($this->value(), '100', false), '100');
				printf('<option value="%s" %s>%s</option>', '200', selected($this->value(), '200', false), '200');
				printf('<option value="%s" %s>%s</option>', '300', selected($this->value(), '300', false), '300');
				printf('<option value="%s" %s>%s</option>', '400', selected($this->value(), '400', false), '400');
				printf('<option value="%s" %s>%s</option>', '500', selected($this->value(), '500', false), '500');
				printf('<option value="%s" %s>%s</option>', '600', selected($this->value(), '600', false), '600');
				printf('<option value="%s" %s>%s</option>', '700', selected($this->value(), '700', false), '700');
				printf('<option value="%s" %s>%s</option>', '800', selected($this->value(), '800', false), '800');
				printf('<option value="%s" %s>%s</option>', '900', selected($this->value(), '900', false), '900');	
				?>
            </select>
			<p class="description"><?php echo esc_html( $this->label ); ?></p>
        </label>
        <?php
    }
}
endif;

if ( class_exists( 'WP_Customize_Control' ) && ! class_exists( 'GeneratePress_Backgrounds_Customize_Control' ) ) :
/**
 * @deprecated 1.4
 */
class GeneratePress_Backgrounds_Customize_Control extends WP_Customize_Control {
	public function render() {}
}
endif;