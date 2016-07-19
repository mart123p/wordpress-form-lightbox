<?php

$root = dirname(__FILE__);
$position = strrpos($root, "wp-content");
$wp_installation = substr($root, 0 , $position );

include( $wp_installation.'wp-load.php' );

$_POST = array_map( 'stripslashes_deep', $_POST );

$action = isset( $_POST['action'] ) ? $_POST['action'] : false;

switch ( $action ) {

	case 'update_content' : 
	
			update_option( $_POST['update'], $_POST['value']); 
			$start = false;
			$new_options = array();
			foreach($flb->options() as $value){
				if($start)
					$new_options[] = $value;
					
				if($value['type']=='open_ajax' && $value['id'] == $_POST['ajax'])
					$start = true;
					
				if($value['type']=='close_ajax' && $value['id'] == $_POST['ajax'] . "_close")
					break;
			}
			
			$flb->construct_form($new_options);
		break;
		
	case 'update_option' :
			update_option( $_POST['id'], $_POST['value'] );
		break;
		
	case 'delete_option' :
			delete_option( $_POST['id'] );
		break;
}

?>