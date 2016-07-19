<?php
// File admin-page.php
// Last edited on 2013-11-26

if ( !class_exists('FormLightboxAdmin') ) {

	class FormLightboxAdmin {
		/**
		* @var  string  $prefix  Defines prefix name to make it unique
		*/
		public $prefix = 'flb_';
		/**
		* @var  array  $admin_page  Defines the parent page for admin menu to be displayed 
		*/
		public $admin_page = 'options-general.php';
		/**
		* @var  array  $page_id  Defines id for admin page
		*/
		public $page_id = 'form-lightbox';
		/**
		* PHP 4 Compatible Constructor
		*/
		function FormLightboxAdmin() { $this->__construct(); }
		/**
		* PHP 5 Constructor
		*/
		function __construct() {
			add_action( 'admin_menu', array( &$this, 'add_setting_menu' ) );
			add_action( 'admin_head', array( &$this, 'add_setting_head' ) );
		}
		/*
		* Add setting menu
		*/
		function add_setting_menu() {
		
			if ( $_GET['page'] == $this->page_id ) {
			
				if ( 'save' == $_POST['action'] ) {
			
					$_POST = array_map( 'stripslashes_deep', $_POST );

					foreach ( $this->options() as $value ) {
						$val = ( isset( $_POST[ $value['id'] ] ) ) ? $_POST[ $value['id'] ] : 'false';
							update_option( $value['id'], $val ); 
					}
			
					header("Location: " . $this->admin_page . "?page=" . $this->page_id . "&saved=true");
					die;
			
				} else if( 'reset' == $_POST['action'] ) {
				
					foreach ($this->options() as $value) {
						
						update_option( $value['id'], $value['std'] ); 
					}
				
					header("Location: " . $this->admin_page . "?page=" . $this->page_id . "&reset=true");
					die;
				}
				add_action('admin_print_styles', array( &$this, 'form_lightbox_admin_styles') );
			}
			add_submenu_page( $this->admin_page, 'Form Lightbox', 'Form Lightbox', 'edit_theme_options', $this->page_id, array( &$this, 'display_admin_page' ) );
		}
		/**
		* style
		*/
		function form_lightbox_admin_styles() {
			wp_enqueue_style('form-lightbox-admin', $this->get_url() . "/admin/css/style.css");
		}
		/**
		* Display the admin option page
		*/
		function display_admin_page() {
		
		/**
		* @var  array  $options  Defines the admin option fields to be displayed
		*/
		$i=0;
		
		if ( $_GET['saved'] == 'true' ) echo '<div id="message" class="updated fade"><p><strong>Form Lightbox settings saved.</strong></p></div>';
		if ( $_GET['reset'] == 'true' ) echo '<div id="message" class="updated fade"><p><strong>Form Lightbox settings reset.</strong></p></div>';
		?>
		<div class="wrap rm_wrap">
		<h2>Form Lightbox - Options</h2>
		
		<div class="rm_opts admin_opts">
			<form method="post" action="<?php echo $this->admin_page; ?>?page=<?php echo $this->page_id ?>">
				<?php $this->construct_form( $this->options() ); ?>
				<input type="hidden" name="action" value="save" />
			</form>
			<form method="post" action="<?php echo $this->admin_page; ?>?page=<?php echo $this->page_id ?>">
			    <p class="submit">
			    <input name="reset" type="submit" value="Reset" />
			    <input type="hidden" name="action" value="reset" />
			    </p>
			</form>
		</div>
		<div id="ajax-loading" style="display:none;font-size:25px;font-weight:bold;position:fixed;left:45%;top:45%;"><img src="<?php echo $this->get_url() ?>/admin/images/loading.gif" /></div>
<?php
		}
		/**
		* Add script to admin head
		*/
		function add_setting_head() {
		
		if ( $_GET['page'] !== $this->page_id )
			return;
?>
<script type="text/javascript" src="<?php echo $this->get_url() ?>/admin/js/jquery.js"></script>
<script type="text/javascript" src="<?php echo $this->get_url() ?>/admin/js/rangeinput.min.js"></script>
<script type="text/javascript" src="<?php echo $this->get_url() ?>/admin/js/iphone-style-checkboxes.js"></script>
<script type="text/javascript" src="<?php echo $this->get_url() ?>/admin/js/rm_script.js"></script>
<script type="text/javascript" src="<?php echo $this->get_url() ?>/admin/colorpicker/mColorPicker.js"></script>
<script type="text/javascript">
jQuery(document).ready(function(){
	jQuery.fn.mColorPicker.defaults.imageFolder = "<?php echo $this->get_url() ?>/admin/colorpicker/images/";
	jQuery.fn.mColorPicker.init.showLogo = false;
	jQuery.fn.mColorPicker.init.allowTransparency = false;
	jQuery.fn.mColorPicker.init.enhancedSwatches = true;
});
function updateContent(ajax,update,value){
	jQuery.ajax({
		type: "POST", 
		url:"<?php echo $this->get_url() ?>/ajax.php", 
		data: "action=update_content&ajax=" + ajax + "&update=" + update + "&value=" + value,
		beforeSend: function() {
						jQuery('#ajax-loading').fadeIn("slow")
					},
		success: function(msg) { 
					jQuery('#' + ajax ).html(msg) 
								
					jQuery('#' + ajax + ' .rm_options').slideUp();
					
					jQuery('#' + ajax + ' .rm_section h3').click(function(){
						if(jQuery(this).parent().next('#' + ajax + ' .rm_options').css('display')==='none'){	
							jQuery(this).removeClass('inactive').addClass('active').children('img').removeClass('inactive').addClass('active');
						}else{	
							jQuery(this).removeClass('active').addClass('inactive').children('img').removeClass('active').addClass('inactive');
						}
						jQuery(this).parent().next('#' + ajax + ' .rm_options').slideToggle('slow');
					});
					
					jQuery('#' + ajax + ' .build_range').each(function(){
						jQuery(this).rangeinput();
					});
					jQuery('#' + ajax + ' .onoff:checkbox').each(function(){
						jQuery(this).iphoneStyle();
					});
					jQuery('#ajax-loading').fadeOut("slow")
				}
	});
}
function updateOption(option,value) {
	info = "action=update_option&id="+option+"&value="+value;
	jQuery.ajax({
		type: "POST", 
		url:"<?php echo $this->get_url() ?>/ajax.php", 
		data: info, 
		beforeSend: function() {
						jQuery('#ajax-loading').fadeIn("slow")
					},
		success: function(msg){  
					jQuery('#ajax-loading').fadeOut("slow")
					}
	});
}
function deleteOption(option) {
	info = "action=delete_option&id="+option;
	jQuery.ajax({
		type: "POST", 
		url:"<?php echo $this->get_url() ?>/ajax.php", 
		data: info, 
		beforeSend: function() {
						jQuery('#ajax-loading').fadeIn("slow")
					},
		success: function(msg){  
					jQuery('#ajax-loading').fadeOut("slow")
				}
	});
}
</script>
<?php
		}
		/**
		* Return options array 
		*/
		function options(){
			
			$bool = array( "true"	=> "True",
						   "false"	=> "False");
								
			$options = array ();
			
			$options[] = array( "name" 	=> "General Option",
					    		"type" 	=> "title",
								"desc"	=> "Options set under Default Settings will be applied to all lightbox.",
								);
					    
			$options[] = array( "name" => "Default Settings",
								"type" 	=> "section");

			$options[] = array( "type" => "open");

				$colorbox_style = array( "1"=> "Zebra",
										 "2"=> "Blackend",
										 "3"=> "Clippy",
										 "4"=> "Whiteness",
										 "5"=> "Sharp emboss",
										);
										
				$options[] = array( "name" 	=> "Style",
									"type" 	=> "select",
									"id" 	=> $this->prefix . "_colorbox_style",
									"options" 	=> $colorbox_style,
									"std"	=> "1",
									"onchange"	=> "updateOption('" .$this->prefix . "_colorbox_style',this.value);",
									"desc" 	=> "Select one",
									);
									
				$transition = array( "elastic" 	=> "Elastic",
					   				 "fade"		=> "Fade", 
									 "none"		=> "None");
					
				$options[] = array( "name" 	=> "Transition",
									"type" 	=> "select",
									"id" 	=> $this->prefix . "_colorbox_transition",
									"options" 	=> $transition,
									"std"	=> "1",
									"onchange"	=> "updateOption('" .$this->prefix . "_colorbox_transition',this.value);",
									"desc" 	=> "Set the transition type",
									"status"	=> "option_string",
									);
										
				$options[] = array( "name" 	=> "Speed",
									"id" 	=> $this->prefix . "_colorbox_speed",
									"type" 	=> "range",
									"min" 	=> "50",
									"max" 	=> "1000",
									"step" 	=> "50",
									"unit"	=>  "ms",
									"std"	=>  "350",
									"onchange"	=>	"updateOption('" . $this->prefix . "_colorbox_speed',this.value);",
									"desc" 	=> "Sets the speed of the fade and elastic transitions.",
									"status"	=> "option",
									);
										
				$options[] = array( "name" 	=> "Scrolling",
									"type" 	=> "select",
									"id" 	=> $this->prefix . "_colorbox_scrolling",
									"options" 	=> $bool,
									"std"	=> "true",
									"onchange"	=> "updateOption('" .$this->prefix . "_colorbox_scrolling',this.value);",
									"desc" 	=> "If false, ColorBox will hide scrollbars for overflowing content. This could be used on conjunction with the resize method (see below) for a smoother transition if you are appending content to an already open instance of ColorBox.",
									"status"	=> "option",
									);
										
				$options[] = array( "name" 	=> "Opacity",
									"id" 	=> $this->prefix . "_colorbox_opacity",
									"type" 	=> "range",
									"min" 	=> "0",
									"max" 	=> "1.00",
									"step" 	=> "0.05",
									"std"	=>  "0.85",
									"onchange"	=>	"updateOption('" . $this->prefix . "_colorbox_opacity',this.value);",
									"desc" 	=> "The overlay opacity level.",
									"status"	=> "option",
									);
									
				$options[] = array( "name" 	=> "Return Focus",
									"type" 	=> "select",
									"id" 	=> $this->prefix . "_colorbox_returnFocus",
									"options" 	=> $bool,
									"std"	=> "true",
									"onchange"	=> "updateOption('" .$this->prefix . "_colorbox_returnFocus',this.value);",
									"desc" 	=> "If true, focus will be returned when ColorBox exits to the element it was launched from.",
									"status"	=> "option",
									);
										
				$options[] = array( "name" 	=> "Fast Iframe",
									"type" 	=> "select",
									"id" 	=> $this->prefix . "_colorbox_fastIframe",
									"options" 	=> $bool,
									"std"	=> "true",
									"onchange"	=> "updateOption('" .$this->prefix . "_colorbox_fastIframe',this.value);",
									"desc" 	=> "If false, the loading graphic removal and onComplete event will be delayed until iframe's content has completely loaded.",
									"status"	=> "option",
									);
										
				$options[] = array( "name" 	=> "Close Button",
									"type" 	=> "select",
									"id" 	=> $this->prefix . "_colorbox_closeBtn",
									"options" 	=> $bool,
									"std"	=> "true",
									"onchange"	=> "updateOption('" .$this->prefix . "_colorbox_closeBtn',this.value);",
									"desc" 	=> "If set to true, close button will be displayed.",
									"status"	=> "option",
									);
									
				$options[] = array( "name" 	=> "Esc Key",
									"type" 	=> "select",
									"id" 	=> $this->prefix . "_colorbox_escKey",
									"options" 	=> $bool,
									"std"	=> "true",
									"onchange"	=> "updateOption('" .$this->prefix . "_colorbox_escKey',this.value);",
									"desc" 	=> "If false, will disable closing colorbox on 'esc' key press.",
									"status"	=> "option",
									);
								
			$options[] = array( "type" => "close");

			$options[] = array( "name" => "Menu Caller",
					    		"type" => "title",
								"desc"	=> "This option will enable lightbox call for WP Menu (Appearance > Menu). Please use Custom Links when adding new menu caller and add # to URL input. <br /><em>Note: Click Screen Option and tick CSS Classes checkbox if you don't see the CSS Classes input.</em>",
						);
					    
			$options[] = array( "name" => "Settings",
					"type" 	=> "section");

			$options[] = array( "type" => "open");

			$options[] = array( "name" 	=> "Total Lightbox Object",
								"id" 	=> $this->prefix . "_menu_object_nos",
								"type" 	=> "range",
								"min" 	=> "0",
								"max" 	=> "10",
								"step" 	=> "1",
								"std"	=>  "0",
								"onchange"	=>	"updateContent('lb_menu_object','" . $this->prefix . "_menu_object_nos',this.value);",
								"desc" 	=> "Set how many objects you want.",
								);
										
			$options[] = array( "type" => "close");
			
			$menu_objects_limit = get_option($this->prefix . "_menu_object_nos");
			
			$options[] = array( "type" 	=> "open_ajax",
								"id" 	=> "lb_menu_object",
								);
							
			for($i=0;$i<$menu_objects_limit;$i++){
			
				$options[] = array( "name" 	=> "Menu Object #" . ($i+1),
									"type" 	=> "section",
									);
	
				$options[] = array( "type" => "open");
	
				$options[] = array( "name" 	=> "Lightbox Content",
									"type" 	=> "textarea",
									"id" 	=> $this->prefix . "_menu_obj_" . $i . "_content",
									"desc" 	=> "Enter content for the lightbox #" . ($i+1) . " here. This will be printed at bottom on all pages.",
									);
									
				$options[] = array( "name" 	=> "Class",
									"type" 	=> "text_readonly",
									"id" 	=> $this->prefix . "_menu_obj_" . $i . "_class",
									"std"	=> "fl_box-menu-". $i,
									"desc" 	=> "Copy and paste this to <em>CSS Classes (optional)</em> input.",
									);
									
				$options[] = array( "name" 	=> "Auto Display onLoad",
									"type" 	=> "checkbox",
									"id" 	=> $this->prefix . "_menu_obj_" . $i . "_onload",
									"desc" 	=> "Set lightbox #" . ($i+1) . " to be called automatically.",
									);
									
				$options[] = array( "name" 	=> "Delayed Time",
									"id" 	=> $this->prefix . "_menu_obj_" . $i . "_delay",
									"type" 	=> "range",
									"min" 	=> "1",
									"max" 	=> "20",
									"step" 	=> "1",
									"std"	=>  "3",
									"unit"	=>  "sec",
									"onchange"	=>	"updateOption('" . $this->prefix . "_menu_obj_" . $i . "_delay',this.value);",
									"desc" 	=> "Delayed time for Auto Display onoad in seconds.",
									);
				
									
				$options[] = array( "type" => "close");
			
			}
			
			$options[] = array( "type" 	=> "close_ajax",
								"id" 	=> "lb_menu_object",
								);
				
			return $options;
		}

		/**
		* Display the admin option page
		*/
		function construct_form($options){
			foreach ($options as $value) {
				switch ( $value['type'] ) {
				
				case "open":
					break;
				
				case "close":
				?>
				
				</div>
				</div>
				<br />
				<?php break;
				
				case "open_ajax":
				?>
				<div id="<?php echo $value['id']; ?>">
				<?php break;
					
				case "close_ajax":
				?>
				</div>
				<?php break;

				case "title":
				?>
				<h3><?php echo $value['name']; ?></h3>
                <h5><?php echo $value['desc']; ?></h5>
				
				<?php break;
				
				case 'text':
				?>
				
				<div class="rm_input rm_text">
					<label for="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></label>
				 	<input name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" type="<?php echo $value['type']; ?>" value="<?php echo htmlentities ( stripslashes( get_option( $value['id'], $value['std'] ) ) );  ?>" onchange="updateOption('<?php echo $value['id']; ?>',this.value);" />
				 <small><?php echo $value['desc']; ?></small><div class="clearfix"></div>
				
				 </div>
				<?php
				break;
				
				case 'text_readonly':
				?>
				
				<div class="rm_input rm_text">
					<label for="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></label>
				 	<input onfocus="this.select()" onmouseup="return false" name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" readonly="readonly" type="text" value="<?php echo $value['std'];  ?>" />
				 <small><?php echo $value['desc']; ?></small><div class="clearfix"></div>
				
				 </div>
				<?php
				break;
				
				case 'textarea':
				?>
				
				<div class="rm_input rm_textarea">
					<label for="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></label>
				 	<textarea name="<?php echo $value['id']; ?>" type="<?php echo $value['type']; ?>" cols="" rows="" onchange="updateOption('<?php echo $value['id']; ?>',this.value);"><?php echo htmlentities ( stripslashes( get_option( $value['id'], $value['std'] ) ) );  ?></textarea>
				 <small><?php echo $value['desc']; ?></small><div class="clearfix"></div>
				
				 </div>
				
				<?php
				break;
				
				case 'select':
					$onchange_val = !empty($value['onchange']) ? ' onChange="' . $value['onchange'] . '"' : '';
				?>
				
				<div class="rm_input rm_select">
					<label for="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></label>
				
				<select name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>"<?php echo $onchange_val ?>>
				<?php foreach ( $value['options'] as $val => $opt ) { $val = ( $val == '' ) ? $opt : $val; ?>
						<option <?php if ( get_option( $value['id'], $value['std'] ) == $val) { echo 'selected="selected"'; } ?> value="<?php echo $val; ?>"><?php echo $opt; ?></option><?php } ?>
				</select>
				
					<small><?php echo $value['desc']; ?></small><div class="clearfix"></div>
				</div>
				<?php
				break;
				
				case "checkbox":
				?>
				
				<div class="rm_input rm_checkbox">
					<label for="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></label>
				
				<?php if( get_option( $value['id'], $value['std'] ) == 'true' ){ $checked = "checked=\"checked\""; }else{ $checked = "";} ?>
				<input type="checkbox" name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" value="true" <?php echo $checked; ?> class="onoff" onchange="if(this.checked==true){updateOption('<?php echo $value['id']; ?>','true');}else{deleteOption('<?php echo $value['id']; ?>')}<?php echo $value['onchange']; ?>"  />
				
					<small><?php echo $value['desc']; ?></small><div class="clearfix"></div>
				 </div>
				<?php break;
				
				case "range":
					$step = ( $value['step'] > 0 ) ? ' step="' . $value['step'] . '"' : '';
				?>
				
				<div class="rm_input rm_range">
					<label for="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></label>
				
				<input type="range" name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" min="<?php echo $value['min']; ?>" max="<?php echo $value['max']; ?>" value="<?php echo get_option( $value['id'], $value['std'] );  ?>" class="build_range" <?php echo $step ?> onchange="updateOption('<?php echo $value['id']; ?>',this.value); <?php echo $value['onchange'] ?>" />
				<span><?php echo $value['unit']; ?></span>
					<small><?php echo $value['desc']; ?></small><div class="clearfix"></div>
				 </div>
				<?php break;
				
				case 'color':
				?>
				
				<div class="rm_input rm_color">
					<label for="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></label>
				 	<input name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" type="<?php echo $value['type']; ?>" value="<?php echo htmlentities ( stripslashes( get_option( $value['id'], $value['std'] ) ) );  ?>" data-hex="true" onchange="updateOption('<?php echo $value['id']; ?>',this.value);" />
				 <small><?php echo $value['desc']; ?></small><div class="clearfix"></div>
				
				 </div>
				<?php
				break;
				
				case "section":
				
				$i++;
				
				?>
				
				<div class="rm_section" id="rm_section_<?php echo $i; ?>">
				<div class="rm_title"><h3><img src="<?php echo $this->get_url() ?>/admin/images/trans.png" class="inactive" alt="" /><?php echo $value['name']; ?></h3><span class="submit">
				<input name="save<?php echo $i; ?>" type="submit" value="Save changes" />
				</span><div class="clearfix"></div></div>
				<div class="rm_options" id="rm_option_<?php echo $i; ?>">
				<?php
				break;
				case "save_button":
				
				$i++;
				
				?>
				<br />
				<div id="rm_save"></div>
				<div class="rm_add" id="rm_add_new_link_button">
					<span class="submit">
				<input name="save<?php echo $i; ?>" type="submit" value="Save changes" />
				</span><div class="clearfix"></div></div>
				<?php break;
				
				}
			}
		}  
		/**
		* Return the plugin url
		*/
		function get_url() {
			return WP_PLUGIN_URL . '/' . dirname(plugin_basename(__FILE__));
		} 
		/**
		* Return the plugin dir
		*/
		function get_dir() {
			return WP_PLUGIN_DIR . '/' . dirname(plugin_basename(__FILE__));
		}
		/**
		* Return lightbox option
		*/
		function get_lb_opt($type='colorbox'){
			$options = $this->options();
			$output = array();
			foreach ($options as $value){
				if( strpos($value['id'], '_' . $type . '_') !== false && strpos($value['status'], 'option') !== false ) {
				
					$outputs = str_replace($this->prefix . '_' . $type . '_', '', $value['id']) . " : ";
					
					if ($value['status']=='option'){
						$outputs .= get_option($value['id'],$value['std']);
					} elseif ($value['status']=='option_string'){
						$outputs .= '"' . get_option($value['id'],$value['std']) . '"';
					}
					
					$output[] = $outputs;
				}
			}
			return implode(",", $output);
		}
	} // End Class

} // End if class exists statement

// Instantiate the class
if ( class_exists('FormLightboxAdmin') ) { $flb = new FormLightboxAdmin(); }


?>