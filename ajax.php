<?php

$root = dirname(__FILE__);
$position = strrpos($root, "wp-content");
$wp_installation = substr($root, 0 , $position );

include( $wp_installation.'wp-load.php' );

$_POST = array_map( 'stripslashes_deep', $_POST );

$action = isset( $_POST['action'] ) ? $_POST['action'] : false;


function validateInput($optionName){
	$requests =
		['_colorbox_style',
		'_colorbox_transition',
		'_colorbox_speed',
		'_colorbox_scrolling',
		'_colorbox_opacity',
		'_colorbox_returnFocus',
		'_colorbox_fastIframe',
		'_colorbox_closeBtn',
		'_colorbox_escKey',
		'_menu_object_nos',
		'_menu_obj_'];
	return (in_array($optionName,$requests));

}

switch ( $action ) {

	case 'update_content' :
			if(validateInput( $_POST['update'])) {
				update_option($_POST['update'], $_POST['value']);
				$start = false;
				$new_options = array();
				foreach ($flb->options() as $value) {
					if ($start)
						$new_options[] = $value;

					if ($value['type'] == 'open_ajax' && $value['id'] == $_POST['ajax'])
						$start = true;

					if ($value['type'] == 'close_ajax' && $value['id'] == $_POST['ajax'] . "_close")
						break;
				}

				$flb->construct_form($new_options);
			}
		break;
		
	case 'update_option' :
		if(validateInput( $_POST['id'])) {
			update_option($_POST['id'], $_POST['value']);
		}
		break;
		
	case 'delete_option' :
		if(validateInput($_POST['id'])) {
			delete_option($_POST['id']);
		}
		break;
}

?>