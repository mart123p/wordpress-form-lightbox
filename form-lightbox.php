<?php
/*
Plugin Name: Form Lightbox
Plugin URI: http://webdesign.myphpmaster.com/form-lightbox
Description: Lightbox for shorcoded form, iframe and inline content.
Version: 2.1
Author: myPHPmaster
Author URI: http://www.myphpmaster.com
License: GPL2
*/
// File form-lightbox.php
// Last edited on 2013-11-26

include ( WP_PLUGIN_DIR . '/' . dirname(plugin_basename(__FILE__)) . '/admin-page.php' );

// Only colorbox available as the fancybox licensing was changed to CC
$lightbox = "colorbox";
	
// call required js and css file
function fl_script() {
	global $flb, $lightbox;
	
	wp_enqueue_script( 'jquery' );

	$colorbox_style = get_option( $flb->prefix . "_colorbox_style", "1" );
	wp_enqueue_script( 'colorbox', plugins_url('/colorbox/jquery.colorbox-min.js', __FILE__), array('jquery'), '1.4.33' );
	wp_enqueue_style( 'colorbox_style', plugins_url('/colorbox/style-' . $colorbox_style . '/colorbox.css', __FILE__) );
	
}

add_action( 'wp_enqueue_scripts', 'fl_script' );

// add edit button in post editor for WP administrator quick usage
function fl_text_editor_button($context){
    $image_btn = plugins_url("/form-lightbox.gif", __FILE__);
    $out = '<a href="#TB_inline?inlineId=fl_form" class="thickbox" title="' . __("Add Form Lightbox", 'form-lightbox') . '"><img src="'.$image_btn.'" alt="' . __("Add Form Lighbox", 'form-lightbox') . '" /></a>';
    return $context . $out;
}
add_action( 'media_buttons_context', 'fl_text_editor_button' );

// Include shortcode
function fl_shortcode( $atts, $content=null, $code="" ) {
	global $fl_id, $post, $flb, $lightbox;
	$fl_id += 1;
	
	extract( shortcode_atts( array(
		'text' => 'Click here',
		'style' => 'padding: 10px; width:350px',
		'title' => 'form lightbox',
		'id'	=> $fl_id,
	), $atts ) );
	
	$title = ( $title !== '' ) ?  ' title="' . $title . '"' : '';

	$output = '<a href="#" class="fl_box-' . $fl_id . '"' . $title . '>' . do_shortcode($text) . '</a>';
	$hidden_output = '<div style="display:none"><div id="form-lightbox-' . $fl_id . '" style="' . $style . '">' . do_shortcode($content) . '</div></div>';
	$hidden_output .= '
		<script type="text/javascript">
			jQuery(document).ready(function() {';
		
	$hidden_output .= 'jQuery(".fl_box-' . $fl_id . '").colorbox({
						inline: true, 
						' . $flb->get_lb_opt('colorbox') . ',
						href:"#form-lightbox-' . $fl_id . '"
					});';
				
	$hidden_output .= '});
		</script>';

	$post_content = $post->post_content . $hidden_output;

	return $output . $hidden_output;
}
add_shortcode( 'formlightbox', 'fl_shortcode' );

// Include shortcode for caller using class tag
function fl_shortcode_call( $atts, $content=null, $code="" ) {
	global $lightbox;
	
	extract( shortcode_atts( array(
		'title' => 'form lightbox',
		'class'	=> '1',
	), $atts ) );
	
	$title = ( $title !== '' ) ?  ' title="' . $title . '"' : '';

	$output = '<a href="#" class="fl_box-' . $class . '"' . $title . '>' . do_shortcode($content) . '</a>';

	return $output;
}
add_shortcode( 'formlightbox_call', 'fl_shortcode_call' );

// Include shortcode for lightbox object using class tag
function fl_shortcode_obj( $atts, $content=null, $code="" ) {
	global $flb, $lightbox;
	
	extract( shortcode_atts( array(
		'style' => '',
		'id'	=> '1',
		'onload' => false,
	), $atts ) );

	if ( $onload == 'false' )
		$onload = false;
		
	$hidden_output = '<div style="display:none"><div id="form-lightbox-' . $id . '" style="' . $style . '">' . do_shortcode($content) . '</div></div>';

	$hidden_output .= '
	<script type="text/javascript">
		var iFrame_' . $id . ' = jQuery("#form-lightbox-' . $id . ' iframe").attr("src");
		jQuery(document).ready(function() {';
	
	$hidden_output .= 'jQuery(".fl_box-' . $id . '").colorbox({
				inline : true,
				href :"#form-lightbox-' . $id . '", 
				' . $flb->get_lb_opt('colorbox') . ',
				onClosed : function(){ jQuery("#form-lightbox-' . $id . ' iframe").attr("src", iFrame_' . $id . '); } 
			}';
			
	$hidden_output .= ');';
	
	if($onload){
		$delay = $onload*1000;
			
			$hidden_output .= '
			setTimeout(function(){
				jQuery.colorbox({
							html : jQuery("#form-lightbox-' . $id . '").html(), 
							' . $flb->get_lb_opt('colorbox') . ',
							onClosed : function(){ jQuery("#form-lightbox-' . $id . ' iframe").attr("src", iFrame_' . $id . ') }
						})
				},' . $delay . ');';
				
	}

	$hidden_output .= '
	});
	</script>';

	return $hidden_output;
}
add_shortcode( 'formlightbox_obj', 'fl_shortcode_obj' );

// Check current page url
$current_page = basename($_SERVER['PHP_SELF']);
 
// Include style and script for edit button in post editor
if( in_array( $current_page, array( 'post.php', 'page.php', 'page-new.php', 'post-new.php' ) ) ){
    add_action( 'admin_head',  'add_fl_style' );
    add_action( 'admin_footer',  'add_fl_popup' );
}

// style for edit button in post editor
function add_fl_style(){
?>
<style type="text/css">
	#form-lightbox td.first {width: 100px;vertical-align: top;}
	#form-lightbox input[type="text"], #form-lightbox textarea {width: 350px;margin-bottom:3px}
	#form-lightbox small {margin-bottom:8px}
</style>
<?php
}
 
// script and form for edit button in post editor
function add_fl_popup(){
?>
<script type="text/javascript">
function InsertLightboxForm(){
	var d=new Date();
	fl_class = d.getTime();
	var fl_form_text = jQuery("#fl_form_text").val();
	var fl_form_title = jQuery("#fl_form_title").val();
	var fl_form_code = jQuery("#fl_form_code").val();
	var fl_form_style = jQuery("#fl_form_style").val();
	var fl_form_onload = jQuery("#fl_form_onload").val();
	var win = window.dialogArguments || opener || parent || top;
	win.send_to_editor('[formlightbox_call title="' + fl_form_title + '" class="' + fl_class + '"]' + fl_form_text + '[/formlightbox_call]\r\n[formlightbox_obj id="' + fl_class + '" style="' + fl_form_style + '" onload="' + fl_form_onload + '"]' + fl_form_code + '[/formlightbox_obj]');
}
</script> 
<div id="fl_form" style="display:none;">
	<div id="form-lightbox">
    	<div style="padding:10px 0">
			<h3><?php _e("Insert Lightbox Form", "form-lightbox"); ?></h3>
			<span><?php _e("", "form-lightbox"); ?></span>
		</div><br>
		<div style="padding:10px 0;">
			<table><tr>
				<td class="first"><label for="fl_form_text"><?php _e("Link Text", "form-lightbox") ?></label></td>
        			<td><input type="text" value="Click here" id="fl_form_text" name="fl_form_text" /><br />
				<small>This is the link text which will be appeared on the page.</small>
				</td>
			</tr><tr>
				<td class="first"><label for="fl_form_title"><?php _e("Link title", "form-lightbox") ?></label></td>
        			<td><input type="text" value="lightbox form" id="fl_form_title" name="fl_form_title" /><br />
				<small>This will be appeared when lightbox opened</small>
				</td>
			</tr><tr>
				<td class="first"><label for="fl_form_code"><?php _e("Form Code", "form-lightbox") ?></label></td>
        			<td><textarea id="fl_form_code" name="fl_form_code" rows="5" cols="20">[form shortcode here]</textarea><br />
				<small>Enter shortcode like [contact-form-7 id="848" title="Contact form 1"] or html code</small>
				</td>
			</tr>
            <tr>
				<td class="first"><label for="fl_form_style"><?php _e("Form Style", "form-lightbox") ?></label></td>
        			<td><input type="text" value="" id="fl_form_style" name="fl_form_style" /><br />
				<small>This which will be applied to div layer contained the form lightbox.</small>
				</td>
			</tr>
            <tr>
				<td class="first"><label for="fl_form_style"><?php _e("Onload Delay", "form-lightbox") ?></label></td>
        			<td><select id="fl_form_onload" name="fl_form_onload">
                    		<option value="false">Off</option>
                    		<option value="1">1 sec</option>
                    		<option value="2">2 sec</option>
                    		<option value="3">3 sec</option>
                    		<option value="4">4 sec</option>
                    		<option value="5">5 sec</option>
                    		<option value="6">6 sec</option>
                    		<option value="7">7 sec</option>
                    		<option value="8">8 sec</option>
                    		<option value="9">9 sec</option>
                    		<option value="10">10 sec</option>
                    		<option value="11">11 sec</option>
                    		<option value="12">12 sec</option>
                    		<option value="13">13 sec</option>
                    		<option value="14">14 sec</option>
                    		<option value="15">15 sec</option>
                    	</select><br />
				<small>Select delayed time in second for auto onload. Set to off to disable.</small>
				</td>
			</tr>
            </table>
		</div>
                <div style="padding:15px;">
                        <input type="button" class="button-primary" value="<?php _e("Insert Lightbox Form", "form-lightbox"); ?>" onclick="InsertLightboxForm();"/>&nbsp;&nbsp;&nbsp;
                    <a class="button" style="color:#bbb;" href="#" onclick="tb_remove(); return false;"><?php _e("Cancel", "form-lightbox"); ?></a>
                </div>
	</div>
</div>
<?php
}

function fl_menu_footer(){
	global $flb, $lightbox;
	
	$menu_objects_limit = get_option($flb->prefix . "_menu_object_nos");
	$hidden_output = '';
	
	for($i=0;$i<$menu_objects_limit;$i++){
		$content = get_option($flb->prefix . "_menu_obj_" . $i . "_content");
		$content = apply_filters('the_content', $content);
		$hidden_output .= '<div style="display:none"><div id="form-lightbox-menu-' . $i . '">';
		$hidden_output .= $content;
		$hidden_output .= '</div></div>';
		
		$hidden_output .= '
		<script type="text/javascript">
			var iFrame_' . $i . ' = jQuery("#form-lightbox-menu-' . $i . ' iframe").attr("src");
			jQuery(document).ready(function() {';
			
		$hidden_output .= 'jQuery(".fl_box-menu-' . $i . '").colorbox({
					inline : true,
					href :"#form-lightbox-menu-' . $i . '", 
					' . $flb->get_lb_opt('colorbox') . ',
					onClosed : function(){ jQuery("#form-lightbox-menu-' . $i . ' iframe").attr("src", iFrame_' . $i . '); } 
				}';
				
		$hidden_output .= ');';
		
		$onload = get_option($flb->prefix . "_menu_obj_" . $i . "_onload");
		
		if( $onload == 'true' ){
			$delay = get_option($flb->prefix . "_menu_obj_" . $i . "_delay", 3)*1000;
				
			
			$hidden_output .= '
				setTimeout(function(){
					jQuery.colorbox({
								html : jQuery("#form-lightbox-menu-' . $i . '").html(), 
								' . $flb->get_lb_opt('colorbox') . ',
								onClosed : function(){ jQuery("#form-lightbox-menu-' . $i . ' iframe").attr("src", iFrame_' . $i . ') }
							})
					},' . $delay . ');';
					
		}
	
		$hidden_output .= '
		});
		</script>';

	}
	
	echo $hidden_output;
}

add_action( 'wp_footer', 'fl_menu_footer', 20 );
?>