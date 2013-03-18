<?php
global $theme_key;
if (!isset($theme_key)) init_theme();
/*$filesPath=base_path().file_directory_path().'/';
$path=base_path().file_directory_path().'/'.$theme_key.'/';*/
$anyfilePath=file_directory_path().'/';
$filePath=file_directory_path().'/'.$theme_key.'/';
$path='';

$elements=array(
	//'name'. type:css/var/checkbox/file. additional css param - (LINK) path for files ++ default value, maybe.
//--->> Style section
	//Example: 'some_style' => array('type'=>'css','selector'=>'#a','attr'=>'background-color'),
	
	'sr_forms_head_title_font_type' => array('type'=>'css', 'selector'=>'.css__form_container .css__form_header table', 'attr'=>'font-family'),
	'sr_forms_head_title_font_size' => array('type'=>'css', 'selector'=>'.css__form_container .css__form_header table', 'attr'=>'font-size'),
	'sr_forms_head_title_font_color' => array('type'=>'css', 'selector'=>'.css__form_container .css__form_header table', 'attr'=>'color'),
    'sr_forms_font_type' => array('type'=>'css', 'selector'=>'.css__form_container', 'attr'=>'font-family'),
	'sr_forms_font_size' => array('type'=>'css', 'selector'=>'.css__form_container', 'attr'=>'font-size'),
        'sr_forms_font_color' => array('type'=>'css', 'selector'=>'.css__form_container', 'attr'=>'color'),
    'sr_forms_title_font_type' => array('type'=>'css', 'selector'=>'.css__form_container label', 'attr'=>'font-family'),
    'sr_forms_title_font_size' => array('type'=>'css', 'selector'=>'.css__form_container label', 'attr'=>'font-size'),
	'sr_forms_title_font_color' => array('type'=>'css', 'selector'=>'.css__form_container label', 'attr'=>'color'),

    'creation_background_color' => array('type'=>'css', 'selector'=>'.sr_form_creation .css__form_container', 'attr'=>'background-color'),
    'origin_title_background_color' => array('type'=>'css', 'selector'=>'div.css__change_origin_title', 'attr'=>'background-color'),
    'origin_property_color' => array('type'=>'css', 'selector'=>'#origin-tree-wrapper .treeview li.property  span.property_title', 'attr'=>'color'),
    'origin_structure_color' => array('type'=>'css', 'selector'=>'#origin-tree-wrapper .treeview li.building span', 'attr'=>'color'),
    'origin_background_color' => array('type'=>'css', 'selector'=>'.sr_form_origin #origin-tree-wrapper', 'attr'=>'background-color'),
//    'origin_tree_property_item_color' => array('type'=>'css', 'selector'=>'#origin-tree-wrapper .treeview li.property  span.property_title', 'attr'=>'color'),
    'review_background_color' => array('type'=>'css', 'selector'=>'.sr_form_review .css__form_container', 'attr'=>'background-color'),
    'confirmation_background_color' => array('type'=>'css', 'selector'=>'.sr_form_confirmation .css__form_container', 'attr'=>'background-color'),


    'sr_select_group_font_color' => array('type'=>'css', 'selector'=>'.sr_form .css__group_list .sr_group_select', 'attr'=>'color'),
    'sr_select_group_background_color' => array('type'=>'css', 'selector'=>'.sr_group_select', 'attr'=>'background-color'),
    'sr_select_group_selected_background_color' => array('type'=>'css', 'selector'=>'td.sr_group_selected', 'attr'=>'background-color'),
    'sr_select_group_border_color' => array('type'=>'css', 'selector'=>'.sr_group_select', 'attr'=>'border-color'),

	'sr_select_category_font_color' => array('type'=>'css', 'selector'=>'.sr_form #sr_categories_list td', 'attr'=>'color'),
    'sr_select_category_background_color' => array('type'=>'css', 'selector'=>'#sr_categories_list td', 'attr'=>'background-color'),
    'sr_select_category_selected_background_color' => array('type'=>'css', 'selector'=>'#sr_categories_list .sr_cat_selected td', 'attr'=>'background-color'),
    'sr_select_category_border_color' => array('type'=>'css', 'selector'=>'#sr_categories_list td', 'attr'=>'border-color'),

    'sr_category_arrow_color' => array('type'=>'css', 'selector'=>'.sr_form #sr_categories_list .arrow', 'attr'=>'color'),
    'sr_input_border_color' => array('type'=>'css', 'selector'=>'.css__form_container .form-item .form-select, .css__form_container .form-item .form-textarea, .css__form_container .form-item .form-text', 'attr'=>'border-color'),

//sr history list (control)
//{link, row, selected row} font, head + property bg/font, img's. Borders?
    'sr_history_header_font_type' => array('type'=>'css', 'selector'=>'.flexigrid div.hDiv tr th div', 'attr'=>'font-family'),
	'sr_history_header_font_size' => array('type'=>'css', 'selector'=>'.flexigrid div.hDiv tr th div', 'attr'=>'font-size'),
	'sr_history_header_font_color' => array('type'=>'css', 'selector'=>'.flexigrid div.hDiv tr th div', 'attr'=>'color'),
    'sr_history_header_background_color' =>  array('type'=>'css', 'selector'=>'.sr_form .flexigrid div.hDiv', 'attr'=>'background-color'),

    'sr_history_property_font_type' => array('type'=>'css', 'selector'=>'.flexigrid .flexiheader', 'attr'=>'font-family'),
	'sr_history_property_font_size' => array('type'=>'css', 'selector'=>'.flexigrid .flexiheader', 'attr'=>'font-size'),
	'sr_history_property_font_color' => array('type'=>'css', 'selector'=>'.flexigrid table th', 'attr'=>'color'),
    'sr_history_property_background_color' =>  array('type'=>'css', 'selector'=>'.flexigrid .flexiheader', 'attr'=>'background-color'),

    'sr_history_data_font_type' => array('type'=>'css', 'selector'=>'.sr_form .flexigrid', 'attr'=>'font-family'),
	'sr_history_data_font_size' => array('type'=>'css', 'selector'=>'.sr_form .flexigrid', 'attr'=>'font-size'),
	'sr_history_data_font_color' => array('type'=>'css', 'selector'=>'.sr_form .flexigrid', 'attr'=>'color'),

	'sr_history_field_label_font_type' => array('type'=>'css', 'selector'=>'.sr_form .review_sr_list label', 'attr'=>'font-family'),
	'sr_history_field_label_font_size' => array('type'=>'css', 'selector'=>'.sr_form .review_sr_list label', 'attr'=>'font-size'),
	'sr_history_field_label_font_color' => array('type'=>'css', 'selector'=>'.sr_form .review_sr_list label', 'attr'=>'color'),

	'sr_history_field_font_type' => array('type'=>'css', 'selector'=>'.sr_form .review_sr_list .form-item input,.sr_form .review_sr_list .form-item select,.sr_form .review_sr_list .form-item textarea', 'attr'=>'font-family'),
	'sr_history_field_font_size' => array('type'=>'css', 'selector'=>'.sr_form .review_sr_list .form-item input,.sr_form .review_sr_list .form-item select,.sr_form .review_sr_list .form-item textarea', 'attr'=>'font-size'),
	'sr_history_field_font_color' => array('type'=>'css', 'selector'=>'.sr_form .review_sr_list .form-item input,.sr_form .review_sr_list .form-item select,.sr_form .review_sr_list .form-item textarea', 'attr'=>'color'),

    'sr_history_links_font_color' => array('type'=>'css', 'selector'=>'.sr_form .flexigrid table a:link, .sr_form .flexigrid table a:visited', 'attr'=>'color'),

    'sr_history_row_background_color' => array(
        'type'=>'css',
        'selector'=>'.sr_form .flexigrid div.bDiv',
        'attr'=>'background-color'
    ),

    'sr_history_selected_row_font_color' => array('type'=>'css', 'selector'=>'
    		.sr_form .flexigrid div.bDiv tr:hover td, 
			.sr_form .flexigrid div.bDiv tr:hover td.sorted,
			.sr_form .flexigrid div.bDiv tr.trOver td.sorted, 
			.sr_form .flexigrid div.bDiv tr.trOver td,
			.sr_form .flexigrid div.bDiv tr.trSelected:hover td, 
			.sr_form .flexigrid div.bDiv tr.trSelected:hover td.sorted,
			.sr_form .flexigrid div.bDiv tr.trOver.trSelected td.sorted, 
			.sr_form .flexigrid div.bDiv tr.trOver.trSelected td,
			.sr_form .flexigrid tr.trSelected td.sorted, 
			.sr_form .flexigrid tr.trSelected td', 'attr'=>'color'),
    
    'sr_history_selected_row_background_color' => array(
        'type'=>'css',
        'selector'=>'
            .sr_form .flexigrid div.bDiv tr:hover td, 
			.sr_form .flexigrid div.bDiv tr:hover td.sorted,
			.sr_form .flexigrid div.bDiv tr.trOver td.sorted, 
			.sr_form .flexigrid div.bDiv tr.trOver td,
			.sr_form .flexigrid div.bDiv tr.trSelected:hover td, 
			.sr_form .flexigrid div.bDiv tr.trSelected:hover td.sorted,
			.sr_form .flexigrid div.bDiv tr.trOver.trSelected td.sorted, 
			.sr_form .flexigrid div.bDiv tr.trOver.trSelected td,
			.sr_form .flexigrid tr.trSelected td.sorted, 
			.sr_form .flexigrid tr.trSelected td
	        ',
        'attr'=>'background-color'
    ),
    'sr_history_page_background_color' => array(
        'type'=>'css',
        'selector'=>'.sr_form .css__form_container,.sr_form .review_sr_list',
        'attr'=>'background-color'
    ),
    
    'sr_history_page_background_image_flag' => array('type'=>'cb-file-nocolor','selector'=>'.sr_form .css__form_container,.sr_form .review_sr_list','attr'=>'background-image','file'=>'sr_history_page_background_image'),
    'sr_history_page_background_image' => array('type'=>'file','path'=>$path.'sr_history_page_background.png'),
    
    
    'sr_history_navigation_control_next_icon_flag' => array('type'=>'cb-file-nocolor','selector'=>'.sr_form .flexigrid .pNext','attr'=>'background-image','file'=>'sr_history_navigation_control_next_icon'),
    'sr_history_navigation_control_next_icon' => array('type'=>'file','path'=>$path.'sr_history_navigation_control_next_icon.png'),
    'sr_history_navigation_control_previous_icon_flag' => array('type'=>'cb-file-nocolor','selector'=>'.sr_form .flexigrid .pPrev','attr'=>'background-image','file'=>'sr_history_navigation_control_previous_icon'),
    'sr_history_navigation_control_previous_icon' => array('type'=>'file','path'=>$path.'sr_history_navigation_control_previous_icon.png'),
    'sr_history_navigation_control_first_icon_flag' => array('type'=>'cb-file-nocolor','selector'=>'.sr_form .flexigrid .pFirst','attr'=>'background-image','file'=>'sr_history_navigation_control_first_icon'),
    'sr_history_navigation_control_first_icon' => array('type'=>'file','path'=>$path.'sr_history_navigation_control_first_icon.png'),
    'sr_history_navigation_control_last_icon_flag' => array('type'=>'cb-file-nocolor','selector'=>'.sr_form .flexigrid .pLast','attr'=>'background-image','file'=>'sr_history_navigation_control_last_icon'),
    'sr_history_navigation_control_last_icon' => array('type'=>'file','path'=>$path.'sr_history_navigation_control_last_icon.png'),
    
    'sr_history_navigation_control_reload_icon_flag' => array('type'=>'cb-file-nocolor','selector'=>'.sr_form .flexigrid .pReload','attr'=>'background-image','file'=>'sr_history_navigation_control_reload_icon'),
    'sr_history_navigation_control_reload_icon' => array('type'=>'file','path'=>$path.'sr_history_navigation_control_reload_icon.png'),
    'sr_history_navigation_control_loading_icon_flag' => array('type'=>'cb-file-nocolor','selector'=>'.sr_form .flexigrid .pReload.loading','attr'=>'background-image','file'=>'sr_history_navigation_control_loading_icon'),
    'sr_history_navigation_control_loading_icon' => array('type'=>'file','path'=>$path.'sr_history_navigation_control_loading_icon.png'),

    /*
     * Timeframes selector
     */
    'timeframes_header_font_type' => array('type'=>'css', 'selector'=>'.css_div__schedule_calendar .sticky-header th', 'attr'=>'font-family'),
	'timeframes_header_font_size' => array('type'=>'css', 'selector'=>'.css_div__schedule_calendar .sticky-header th', 'attr'=>'font-size'),
	'timeframes_header_font_color' => array('type'=>'css', 'selector'=>'.css_div__schedule_calendar .sticky-header th', 'attr'=>'color'),
    'timeframes_header_background_color' =>  array('type'=>'css', 'selector'=>'.css_div__schedule_calendar .sticky-header th, .css_div__schedule_calendar .css_div__time_intervals_column_overlay', 'attr'=>'background-color'),
    

    'timeframes_time_intervals_column_font_type' => array('type'=>'css', 'selector'=>'.css_table__time_intervals_column td', 'attr'=>'font-family'),
	'timeframes_time_intervals_column_font_size' => array('type'=>'css', 'selector'=>'.css_table__time_intervals_column td', 'attr'=>'font-size'),
	'timeframes_time_intervals_column_font_color' => array('type'=>'css', 'selector'=>'.css_table__time_intervals_column td', 'attr'=>'color'),
    'timeframes_time_intervals_column_background_color' =>  array('type'=>'css', 'selector'=>'.css_table__time_intervals_column td', 'attr'=>'background-color'),

    'timeframes_control_links_font_color' => array('type'=>'css', 'selector'=>'
    		#timeframes-pager p,
    		.css_div__schedule_calendar a,
            .css_div__schedule_calendar a:link,
            .css_div__schedule_calendar a:visited,
            .css_div__schedule_calendar a:hover
        ', 'attr'=>'color'),
	    
    'timeframes_row_odd_background_color' => array(
        'type'=>'css',
        'selector'=>'#timeframe-select tr.odd',
        'attr'=>'background-color'
    ),
    'timeframes_row_even_background_color' => array(
        'type'=>'css',
        'selector'=>'#timeframe-select tr.even',
        'attr'=>'background-color'
    ),

    'timeframes_selecting_cell_background_color' => array(
        'type'=>'css',
        'selector'=>'
            table #timeframe-select .ui-selecting
	        ',
        'attr'=>'background-color'
    ),

    'timeframes_selected_cell_background_color' => array(
        'type'=>'css',
        'selector'=>'
            #timeframe-select .ui-selected,
            #timeframe-select .altui-selected,
            .css_div__schedule_calendar .css_div__legend .css_div__icon-selected div
	        ',
        'attr'=>'background-color'
    ),

    'timeframes_past_cell_background_color' => array('type'=>'css', 'selector'=>'
            #timeframe-select tr.odd .past_date, #timeframe-select tr.even .past_date, .css_div__schedule_calendar .css_div__legend .css_div__icon-past_date div
        ', 'attr'=>'background-color'),
    
    'timeframes_reservation_conflict_cell_background_color' => array('type'=>'css', 'selector'=>'
            #timeframe-select tr.odd .reservation_conflict,
			#timeframe-select tr.even .reservation_conflict
        ', 'attr'=>'background-color'),
    	
    'timeframes_reloading_image' => array('type'=>'file','path'=>$path.'loading_icon-big.gif'),
    'timeframes_reloading_image_flag' => array('type'=>'cb-file-nocolor','selector'=>'.css_div__loading_icon_layer .css_div__icon','attr'=>'background-image','file'=>'timeframes_reloading_image'),
    
    //dont know, if we need this. Now this background is not used
    //'timeframes_locked_cell_background_color' => array('type'=>'css', 'selector'=>'body .flexigrid div.bDiv', 'attr'=>'background-color'),

//--->> Variables section
	//'Example: some_var' => array('type'=>'var','varName'=>'variable_name_in_storage'),
    'review_page_title' => array('type'=>'var','varName'=>'review_page_title'),
    'confirmation_page_success' => array('type'=>'var','varName'=>'confirmation_page_success'),
    'confirmation_page_error' => array('type'=>'var','varName'=>'confirmation_page_error'),
    'origin_title' => array('type'=>'var','varName'=>'origin_title'),

	'origin_popup_layer_width' => array('type'=>'var','varName'=>'int__origin_popup_layer_width'),
	'origin_popup_layer_height' => array('type'=>'var','varName'=>'int__origin_popup_layer_height'),

    'sr_time_limit' => array('type'=>'var','varName'=>'sr_time_limit'),

    //checkboxes state storage
    'inherit_sr_standard' => array('type'=>'var','varName'=>'inherit_sr_standard'),
    'inherit_sr_visitor' => array('type'=>'var','varName'=>'inherit_sr_visitor'),
    'inherit_sr_hvac' => array('type'=>'var','varName'=>'inherit_sr_hvac'),
    'inherit_sr_shared' => array('type'=>'var','varName'=>'inherit_sr_shared'),
    'inherit_sr_review' => array('type'=>'var','varName'=>'inherit_sr_review'),
    'inherit_sr_review_visitor' => array('type'=>'var','varName'=>'inherit_sr_review_visitor'),
    'inherit_sr_review_details' => array('type'=>'var','varName'=>'inherit_sr_review_details'),
    

//--->> Files section
	//Example: 'some_file' => array('type'=>'file','path'=>$path.'filename.ext'),
	'sr_forms_form_bg_image' => array('type'=>'file','path'=>$path.'sr_forms_form_bg_image.png'),
    
    'creation_background_image' => array('type'=>'file','path'=>$path.'creation_background_image.png'),
	
	'header_icon_image' => array('type'=>'file','path'=>$path.'form_header_icon.png'),
	/*'header_icon_image' => array('type'=>'file','path'=>$path.'header_icon.png'),
	'header_icon_image' => array('type'=>'file','path'=>$path.'header_icon.png'),*/
	
	'review_header_icon_image' => array('type'=>'file','path'=>$path.'review-form_header_icon.png'),
	'confirmation_success_header_icon_image' => array('type'=>'file','path'=>$path.'confirm_done-form_header_icon.png'),
	'confirmation_failure_header_icon_image' => array('type'=>'file','path'=>$path.'confirm_error-form_header_icon.png'),
	
	
    'creation_create_button_image' => array('type'=>'file','path'=>$path.'creation_create_button_image.png'),
//    'creation_origin_button_image' => array('type'=>'file','path'=>$path.'creation_origin_button_image.png'),

    'origin_background_image' => array('type'=>'file','path'=>$path.'origin_background_image.png'),
    'origin_save_button_image' => array('type'=>'file','path'=>$path.'origin_save_button_image.png'),

	'origin_tree_item_close_icon' => array('type'=>'file','path'=>$path.'minus-building.gif'),
	'origin_tree_item_open_icon' => array('type'=>'file','path'=>$path.'plus-building.gif'),

//	'origin_tree_property_item_close_icon' => array('type'=>'file','path'=>$path.'origin_tree_property_item_close_icon.png'),
//	'origin_tree_property_item_open_icon' => array('type'=>'file','path'=>$path.'origin_tree_property_item_open_icon.png'),
	'origin_tree_building_item_close_icon' => array('type'=>'file','path'=>$path.'origin_tree_building_item_close_icon.png'),
	'origin_tree_building_item_open_icon' => array('type'=>'file','path'=>$path.'origin_tree_building_item_open_icon.png'),
	'origin_tree_block_item_close_icon' => array('type'=>'file','path'=>$path.'origin_tree_block_item_close_icon.png'),
	'origin_tree_block_item_open_icon' => array('type'=>'file','path'=>$path.'origin_tree_block_item_open_icon.png'),
	'origin_tree_floor_item_close_icon' => array('type'=>'file','path'=>$path.'origin_tree_floor_item_close_icon.png'),
	'origin_tree_floor_item_open_icon' => array('type'=>'file','path'=>$path.'origin_tree_floor_item_open_icon.png'),
	'origin_tree_suite_item_icon' => array('type'=>'file','path'=>$path.'suite.gif'),

    'review_background_image' => array('type'=>'file','path'=>$path.'review_background_image.png'),
    'review_back_button_image' => array('type'=>'file','path'=>$path.'review_back_button_image.png'),
    'review_confirm_button_image' => array('type'=>'file','path'=>$path.'review_confirm_button_image.png'),

    'confirmation_background_image' => array('type'=>'file','path'=>$path.'confirmation_background_image.png'),
    'confirmation_back_button_image' => array('type'=>'file','path'=>$path.'confirmation_back_button_image.png'),
    'confirmation_confirm_button_image' => array('type'=>'file','path'=>$path.'confirmation_confirm_button_image.png'),

    'button_bg_image' => array('type'=>'file','path'=>$path.'button_bg_image.png'),
	'button_bg_image_long' => array('type'=>'file','path'=>$path.'button_bg_image_long.png'),
    
    
    'available_timeframe_icon_image' => array('type'=>'file','path'=>$path.'available_timeframe_icon.png'),
	'close_timeframe_icon_image' => array('type'=>'file','path'=>$path.'close_timeframe_icon.png'),
	'reserved_timeframe_icon_image' => array('type'=>'file','path'=>$path.'reserved_timeframe_icon.png'),
    
	

	'origin_tree_shared_item_icon' => array('type'=>'file','path'=>$path.'shared.gif'),
//--->> Files with custom path




//--->> Checkbox section for background-image and other file-related properties
	//Example: 'some_checkbox' => array('type'=>'cb-file','selector'=>'#a','attr'=>'background-image','file'=>'map_file_name'),
	//'sr_forms_form_bg_image_flag' => array('type'=>'cb-file','selector'=>'.sr_form_creation','attr'=>'background-image','file'=>'sr_forms_form_bg_image'),

	'creation_background_flag' => array('type'=>'cb-file-nocolor','selector'=>'.sr_form_creation .css__form_container','attr'=>'background-image','file'=>'creation_background_image'),
	
	'header_icon_flag' => array('type'=>'cb-file-nocolor','selector'=>'.css__form_header .css__icon','attr'=>'background-image','file'=>'header_icon_image', 'filter'=>true),
	
	'review_header_icon_flag' => array('type'=>'cb-file-nocolor','selector'=>'.sr_form_review .css__form_header .css__review_icon','attr'=>'background-image','file'=>'review_header_icon_image', 'filter'=>true),
	'confirmation_success_header_icon_flag' => array('type'=>'cb-file-nocolor','selector'=>'.sr_form_confirmation .css__form_header .css__confirm_done_icon','attr'=>'background-image','file'=>'confirmation_success_header_icon_image', 'filter'=>true),
	'confirmation_failure_header_icon_flag' => array('type'=>'cb-file-nocolor','selector'=>'.sr_form_confirmation .css__form_header .css__confirm_error_icon','attr'=>'background-image','file'=>'confirmation_failure_header_icon_image', 'filter'=>true),

    'button_bg_image_flag' => array('type'=>'cb-file-nocolor','selector'=>'.css__form_container .submit input','attr'=>'background-image','file'=>'button_bg_image'),
	'button_bg_image_long_flag' => array('type'=>'cb-file-nocolor','selector'=>'.css__form_container .submit input.css__long_button','attr'=>'background-image','file'=>'button_bg_image_long'),
	
//	'creation_create_button_image_flag' => array('type'=>'cb-file-nocolor','selector'=>'.sr_form_creation .submit input','attr'=>'background-image','file'=>'creation_create_button_image'),
//	'creation_origin_button_image_flag' => array('type'=>'cb-file-nocolor','selector'=>'.sr_form_creation .css__origin_expander','attr'=>'background-image','file'=>'creation_origin_button_image'),

    'origin_background_flag' => array('type'=>'cb-file-nocolor','selector'=>'.sr_form_origin #origin-tree-wrapper','attr'=>'background-image','file'=>'origin_background_image'),
//	'origin_save_button_image_flag' => array('type'=>'cb-file-nocolor','selector'=>'.sr_form_origin .submit input','attr'=>'background-image','file'=>'origin_save_button_image'),

	'origin_tree_item_close_icon_flag' => array('type'=>'cb-file-nocolor','selector'=>'#origin-tree-wrapper .treeview li.closed.collapsable .hitarea','attr'=>'background-image','file'=>'origin_tree_item_close_icon'),
	'origin_tree_item_open_icon_flag' => array('type'=>'cb-file-nocolor','selector'=>'#origin-tree-wrapper .treeview li.closed.expandable .hitarea','attr'=>'background-image','file'=>'origin_tree_item_open_icon'),
    'origin_tree_shared_item_icon_flag' => array('type'=>'cb-file-nocolor','selector'=>'#origin-tree-wrapper .treeview span.shared_title','attr'=>'background-image','file'=>'origin_tree_shared_item_icon'),

//	'origin_tree_property_item_close_icon_flag' => array('type'=>'cb-file-nocolor','selector'=>'#origin-tree-wrapper .treeview li.closed.collapsable .property-hitarea','attr'=>'background-image','file'=>'origin_tree_property_item_close_icon'),
//	'origin_tree_property_item_open_icon_flag' => array('type'=>'cb-file-nocolor','selector'=>'#origin-tree-wrapper .treeview li.closed.expandable .property-hitarea','attr'=>'background-image','file'=>'origin_tree_property_item_open_icon'),
	/*'origin_tree_building_item_close_icon_flag' => array('type'=>'cb-file-nocolor','selector'=>'#origin-tree-wrapper .treeview li.closed.collapsable .building-hitarea','attr'=>'background-image','file'=>'origin_tree_building_item_close_icon'),
	'origin_tree_building_item_open_icon_flag' => array('type'=>'cb-file-nocolor','selector'=>'#origin-tree-wrapper .treeview li.closed.expandable .building-hitarea','attr'=>'background-image','file'=>'origin_tree_building_item_open_icon'),
	'origin_tree_block_item_close_icon_flag' => array('type'=>'cb-file-nocolor','selector'=>'#origin-tree-wrapper .treeview li.closed.collapsable .block-hitarea','attr'=>'background-image','file'=>'origin_tree_block_item_close_icon'),
	'origin_tree_block_item_open_icon_flag' => array('type'=>'cb-file-nocolor','selector'=>'#origin-tree-wrapper .treeview li.closed.expandable .block-hitarea','attr'=>'background-image','file'=>'origin_tree_block_item_open_icon'),
	'origin_tree_floor_item_close_icon_flag' => array('type'=>'cb-file-nocolor','selector'=>'#origin-tree-wrapper .treeview li.closed.collapsable .floor-hitarea','attr'=>'background-image','file'=>'origin_tree_floor_item_close_icon'),
	'origin_tree_floor_item_open_icon_flag' => array('type'=>'cb-file-nocolor','selector'=>'#origin-tree-wrapper .treeview li.closed.expandable .floor-hitarea','attr'=>'background-image','file'=>'origin_tree_floor_item_open_icon'),*/
	'origin_tree_suite_item_icon_flag' => array('type'=>'cb-file-nocolor','selector'=>'#origin-tree-wrapper .treeview li.suite','attr'=>'background-image','file'=>'origin_tree_suite_item_icon'),




    'review_background_flag' => array('type'=>'cb-file-nocolor','selector'=>'.sr_form_review .css__form_container','attr'=>'background-image','file'=>'review_background_image'),
//    'review_back_button_image_flag' => array('type'=>'cb-file-nocolor','selector'=>'.sr_form_review .submit .css__back input','attr'=>'background-image','file'=>'review_back_button_image'),
//    'review_confirm_button_image_flag' => array('type'=>'cb-file-nocolor','selector'=>'.sr_form_review .submit .css__confirm input','attr'=>'background-image','file'=>'review_confirm_button_image'),

    'confirmation_background_flag' => array('type'=>'cb-file-nocolor','selector'=>'.sr_form_confirmation .css__form_container','attr'=>'background-image','file'=>'confirmation_background_image'),	
//	'confirmation_back_button_image_flag' => array('type'=>'cb-file-nocolor','selector'=>'.sr_form_confirmation .submit .css__back input','attr'=>'background-image','file'=>'confirmation_back_button_image'),
//    'confirmation_confirm_button_image_flag' => array('type'=>'cb-file-nocolor','selector'=>'.sr_form_confirmation .submit .css__done input','attr'=>'background-image','file'=>'confirmation_confirm_button_image'),

    
    'available_timeframe_icon_flag' => array('type'=>'cb-file-nocolor','selector'=>'.css_div__schedule_calendar .css_div__legend .css_div__icon-available div, #timeframe-select .available','attr'=>'background-image','file'=>'available_timeframe_icon_image', 'filter'=>true),
    'close_timeframe_icon_flag' => array('type'=>'cb-file-nocolor','selector'=>'.css_div__schedule_calendar .css_div__legend .css_div__icon-close div, #timeframe-select .close','attr'=>'background-image','file'=>'close_timeframe_icon_image', 'filter'=>true),
    'reserved_timeframe_icon_flag' => array('type'=>'cb-file-nocolor','selector'=>'.css_div__schedule_calendar .css_div__legend .css_div__icon-reserved div, #timeframe-select .reserved','attr'=>'background-image','file'=>'reserved_timeframe_icon_image', 'filter'=>true),


/*	CALENDAR ->	*/
/*	'calendar_picker_bg_color' => array('type'=>'css', 'selector'=>'div.dp-popup', 'attr'=>'background-color'),
	'calendar_picker_border_color' => array('type'=>'css', 'selector'=>'table.jCalendar', 'attr'=>'border-color'),

	'calendar_picker_month_name_row_bg_color' => array('type'=>'css', 'selector'=>'div.dp-popup h2', 'attr'=>'background-color'),
	'calendar_picker_month_name_row_font_color' => array('type'=>'css', 'selector'=>'div.dp-popup h2', 'attr'=>'color'),
 
	'calendar_picker_week_day_name_row_bg_color' => array('type'=>'css', 'selector'=>'table.jCalendar th', 'attr'=>'background-color'),
	'calendar_picker_week_day_name_row_font_color' => array('type'=>'css', 'selector'=>'table.jCalendar th', 'attr'=>'color'),
		
	'calendar_picker_today_bg_color' => array('type'=>'css', 'selector'=>'table.jCalendar td.today', 'attr'=>'background-color'),
	'calendar_picker_today_font_color' => array('type'=>'css', 'selector'=>'table.jCalendar td.today', 'attr'=>'color'),

	'calendar_picker_highlighted_today_bg_color' => array('type'=>'css', 'selector'=>'table.jCalendar td.today.dp-hover', 'attr'=>'background-color'),
	'calendar_picker_highlighted_today_font_color' => array('type'=>'css', 'selector'=>'table.jCalendar td.today.dp-hover', 'attr'=>'color'),

	'calendar_picker_selected_bg_color' => array('type'=>'css', 'selector'=>'table.jCalendar td.selected', 'attr'=>'background-color'),
	'calendar_picker_selected_font_color' => array('type'=>'css', 'selector'=>'table.jCalendar td.selected', 'attr'=>'color'),

	'calendar_picker_highlighted_selected_bg_color' => array('type'=>'css', 'selector'=>'table.jCalendar td.selected.dp-hover', 'attr'=>'background-color'),
	'calendar_picker_highlighted_selected_font_color' => array('type'=>'css', 'selector'=>'table.jCalendar td.selected.dp-hover', 'attr'=>'color'),

	'calendar_picker_week_day_bg_color' => array('type'=>'css', 'selector'=>'table.jCalendar td', 'attr'=>'background-color'),
	'calendar_picker_week_day_font_color' => array('type'=>'css', 'selector'=>'table.jCalendar td', 'attr'=>'color'),
	
	'calendar_picker_highlighted_week_day_bg_color' => array('type'=>'css', 'selector'=>'table.jCalendar td.dp-hover', 'attr'=>'background-color'),
	'calendar_picker_highlighted_week_day_font_color' => array('type'=>'css', 'selector'=>'table.jCalendar td.dp-hover', 'attr'=>'color'),

	'calendar_picker_week_disabled_day_bg_color' => array('type'=>'css', 'selector'=>'table.jCalendar td.disabled, table.jCalendar td.disabled.dp-hover', 'attr'=>'background-color'),
	'calendar_picker_week_disabled_day_font_color' => array('type'=>'css', 'selector'=>'table.jCalendar td.disabled, table.jCalendar td.disabled.dp-hover', 'attr'=>'color'),

	'calendar_picker_other_month_bg_color' => array('type'=>'css', 'selector'=>'table.jCalendar td.other-month', 'attr'=>'background-color'),
	'calendar_picker_other_month_font_color' => array('type'=>'css', 'selector'=>'table.jCalendar td.other-month', 'attr'=>'color'),

	'calendar_picker_holiday_bg_color' => array('type'=>'css', 'selector'=>'table.jCalendar td.weekend', 'attr'=>'background-color'),
	'calendar_picker_holiday_font_color' => array('type'=>'css', 'selector'=>'table.jCalendar td.weekend', 'attr'=>'color')
*/
/*	CALENDAR <-	*/
	);

global $styles_inherit;

$styles_inherit = array(
    'sr_standard' => array(
        'wrapped' => array(
            'wrapper' => '.sr_standard',
            'elems' => wsc_full_mapping($elements),
        ),
        'variables' => array(
            'prefix' => 'sr_standard_',
            'names' => array(
                'review_page_title', 'origin_title', 'confirmation_page_success', 'confirmation_page_error',
            ),
        ),
        /* Could be used for linking different styles, even in the same form, or without form field at all. 
        'custom' => array(
            array('element'=>'sr_forms_title_font_color', 'selector'=>'.sr_standard .sr_form', 'attr'=>'color'),
            // etc...
        ),
        */
    ),

    'sr_visitor' => array(
        'wrapped' => array(
            'wrapper' => '.sr_visitor',
            'elems' => wsc_full_mapping($elements),
        ),
        'variables' => array(
            'prefix' => 'sr_visitor_',
            'names' => array(
                'review_page_title', 'origin_title', 'confirmation_page_success', 'confirmation_page_error',
            ),
        ),
    ),

    'sr_hvac' => array(
        'wrapped' => array(
            'wrapper' => '.sr_hvac',
            'elems' => wsc_full_mapping($elements),
        ),
        'variables' => array(
            'prefix' => 'sr_hvac_',
            'names' => array(
                'review_page_title', 'origin_title', 'confirmation_page_success', 'confirmation_page_error',
            ),
        ),
    ),
    
    'sr_shared' => array(
        'wrapped' => array(
            'wrapper' => '.sr_shared',
            'elems' => wsc_full_mapping($elements),
        ),
        'variables' => array(
            'prefix' => 'sr_shared_',
            'names' => array(
                'review_page_title', 'origin_title', 'confirmation_page_success', 'confirmation_page_error',
            ),
        ),
    ),
);


global $glob_elements;
$glob_elements+=$elements;
unset($elements);

?>