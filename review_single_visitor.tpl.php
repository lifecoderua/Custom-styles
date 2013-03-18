<?
	$sr = $visitor['meetingDetail'];
	$params=wssr_get_field_params(wssr_load_field_params('wssr_review_details_form'), 'wssr_review_details_form');
	
	$array__fitlering_option=array();
	$string__search_type="";
    if (intval($_GET['property_id'])!==0)
    	$array__fitlering_option[]='property_id='.$_GET['property_id'];
    if (strtotime($_GET['date']))
    	$array__fitlering_option[]='date='.$_GET['date'];
    if ($_GET['searchType'])
    	$string__search_type=$_GET['searchType'];
	
    if (count($array__fitlering_option)>0)
		$string__fitlering_options='?'.implode('&',$array__fitlering_option);
?>
<div id="review_single_visitor_sr" class="sr_form css_div__review_details css_div__review_single_visitor">
<?
	if (!$sr['srId'])
	{
		$string_html__container=
			'<div class="css__form_container">
	       	<div class="css__form_wrapper">
	       		<div class="css_div__error-id_not_found"><h2>'.str_replace('_id_','<span>'.$sr['requested_sr_id'].'</span>',$params['id_not_found']['#title']).'</h2></div>
			</div>
			<div class="css__bottom_border">&nbsp;</div>
			<div class="submit">
				<div class="css__padding_1">&nbsp;</div>
				<div class="css__padding_2 css__back"><input type="button" class="form-submit" value="Back" id="edit-back" onclick="location.href=\''.base_path().'wsservicerequest/review_visitors/'.$string__search_type.$string__fitlering_options.'\'"></div>
			</div>';

		echo $string_html__container;
	}
	else
	{


    
//    print_r($params);
	$array__table_row=array();

    $fromTime = ws_parse_time($sr['FromDate']);
	$array__create_information=array();
	$array__create_information[]=array('title'=>"ID#",'value'=>$sr['srId']);
	$array__create_information[]=array('title'=>"Property name",'value'=>$sr['property_name']);
	$array__create_information[]=array('title'=>"When",'value'=>$fromTime['week_day'].' '.$fromTime['date'].' at '.$fromTime['time12']);
	$string__phone_number="";
	if ($sr['createdBy']['phone'])
		$string__phone_number=' ('.$sr['createdBy']['phone'].')';
	$array__create_information[]=array('title'=>"Initiated by",'value'=>'<a href="mailto:'.$sr['createdBy']['email'].'">'.$sr['createdBy']['first'].' '.$sr['createdBy']['last'].'</a>,'.$string__phone_number);
	$array__create_information[]=array('title'=>"For",'value'=>$sr['entity']['Description']);
	if ($sr['LocationDescription'])
		$array__create_information[]=array('title'=>"Send To",'value'=>$sr['LocationDescription']);
	$array__location_description=array();
	$array__location_description[]=array('title'=>"Building/Block",'value'=>$sr['space']['block']);
	$array__location_description[]=array('title'=>"Floor",'value'=>$sr['space']['floor']);
	$array__location_description[]=array('title'=>"Suite/Space",'value'=>$sr['space']['space']);

	if (true)
	{
		$array__table_row[]=
			'<tr>
				<td class="css__left_column">
     				<div id="create_info" class="css_div__left_column">'.WSSR_RenderReviewTableBlock($params['create_information']['#title'],$array__create_information).'</div>
       			</td>
				<td class="css__right_column">
       				<div class="css_div__right_column">'.WSSR_RenderReviewTableBlock($params['location_description']['#title'],$array__location_description).'</div>
       			</td>
       		</tr>'.'<tr><td colspan="2"><div class="css_div__h_separator-blank"></div></td></tr>';
	}
	
	if ($sr['MeetingNote'])
		$array__table_row[]='<tr><td class="css__left_column" colspan="2"><div id="comments">'
			.WSSR_RenderReviewTableDescription($params['comments']['#title'],nl2br($sr['MeetingNote'])).'</div></td></tr>'
			.'<tr><td colspan="2"><div class="css_div__h_separator-blank"></div></td></tr>';


    
        
/*	if ($sr['MeetingLocationDescription'])
	{
		$array__table_row[]='<tr><td class="css__left_column" colspan="2"><div id="location_description">'
			.WSSR_RenderReviewTableDescription($params['location_description']['#title'],nl2br($sr['MeetingLocationDescription'])).'</div></td></tr>'
			.'<tr><td colspan="2"><div class="css_div__h_separator-blank"></div></td></tr>';
	}
*/

	if ($sr['participants'])
	{
		if (count($visitor['property_capture_settings'])>0)
		{
			$string_html__temp="";
			foreach ($visitor['property_capture_settings'] as $var__key=>$var__value)
				$string_html__temp.='<input id="participant_'.$var__key.'_image_dimensions" type="hidden" value="'.$var__value['width'].'_'.$var__value['height'].'">';
			$string_html__temp='<div style="display:none">'.$string_html__temp.'</div>';
		}
		$array__table_row[]='<tr><td class="css__left_column" colspan="2"><div id="participants">'
			.WSSR_RenderReviewTableGrid($params['visitors']['#title'],array('First Name', 'Last Name', 'Company', 'Photo', 'Credential', 'Checked In', 'Checked Out'),wssr_flatten_participants_array($sr['participants'])).'</div></td></tr>'
			.'<tr><td colspan="2"><div class="css_div__h_separator-blank"></div>'.$string_html__temp.'</td></tr>';
	}

	if ($visitor['serviceRequestDetail']['RequestHistory'])
	{
		$array__table_row[]='<tr><td class="css__left_column" colspan="2"><div id="sr_history">'
			.WSSR_RenderReviewTableGrid($params['sr_history']['#title'],array('Date','Time','Action','Performed By','Company','Status'),wssr_flatten_history_array($visitor['serviceRequestDetail']['RequestHistory'])).'</div></td></tr>'
			.'<tr><td colspan="2"><div class="css_div__h_separator-blank"></div></td></tr>';
	}
	if (count($sr['sr_comment_list'])>0)
	{
		$array__table_row[]='<tr><td class="css__left_column" colspan="2"><div id="review_history">'
			.WSSR_RenderReviewTableGrid($params['sr_comments']['#title'],array('Date','Time','Performed By','Text'),$sr['sr_comment_list'],array(0=>'78px',1=>'60px',2=>'136px')).'</div></td></tr>'
			.'<tr><td colspan="2"><div class="css_div__h_separator-blank"></div></td></tr>';
	}
	
	$attachments=$visitor['serviceRequestDetail']['attachments'];
	if (is_array($attachments))
	{
		$array__document=array();
		foreach ($attachments as $row)
			$array__document[]=array('url'=>base_path().'wsservicerequest/getdocument/'.$row['id'],'title'=>$row['title']);

		$array__table_row[]=
			'<tr>
				<td class="css__left_column">
     				<div id="related-documents" class="css_div__left_column">'.WSSR_RenderReviewTableDocumentBlock($params['documents']['#title'],$array__document).'</div>
       			</td>
				<td class="css__right_column">
       				<div class="css_div__right_column">'.'</div>
       			</td>
       		</tr>';
	}


	$string_html__container=
		'<div class="css__form_container">
       	<div class="css__form_wrapper">
       		<table class="css__fields" cellpadding="0" cellspacing="0">
       			'.implode('',$array__table_row).'
			</table>
		</div>
		<div class="css__bottom_border">&nbsp;</div>
		<div class="submit">
			<div class="css__padding_1">&nbsp;</div>
			<div class="css__padding_2 css__back"><input type="button" class="form-submit" value="Back" id="edit-back" onclick="location.href=\''.base_path().'wsservicerequest/review_visitors/'.$string__search_type.$string__fitlering_options.'\'"></div>
			</div>
		</div>';

	echo $string_html__container;
	
	}
?>

</div>
