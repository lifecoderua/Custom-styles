	
	function ChangeOrigin()
	{
        $("#id__change_origin").toggle();
	}
	
	function SaveChangeOrigin()
	{
        selectSuite();
        HidePopupLayer();
	}
	
	function SelectSR(object_dom__sr_group)
        {
                }


	var int__added_visitor_amount=0;
	
	function add_visitor_string()
	{
		int__added_visitor_amount++;
		while (document.getElementById('id__visitor_'+int__added_visitor_amount))
			int__added_visitor_amount++;
		var string_html__output='';
		string_html__output+='<table id="id__visitor_'+int__added_visitor_amount+'" class="css__group css__new" cellspacing="0" cellpadding="0"><tr><td><div class="css__group_container">';
//	old code
		//<label class="css__block">'+string__visitor_title.first_name+': <span title="This field is required." class="form-required">*</span></label>
		string_html__output+='<div class="css__field_wrapper"><div class="form-item"><input type="text" maxlength="35" name="visitor['+int__added_visitor_amount+'][first_name]" value="" class="form-text required"/></div></div>';
		string_html__output+='<div class="css__field_wrapper"><div class="form-item"><input type="text" maxlength="35" name="visitor['+int__added_visitor_amount+'][last_name]" value="" class="form-text required"/></div></div>';

		if (string__visitor_access['company']!='0')
			string_html__output+='<div class="css__field_wrapper"><div class="form-item"><input type="text" maxlength="255" name="visitor['+int__added_visitor_amount+'][company]" value="" class="form-text"></div></div>';
		if (string__visitor_access['email']!='0')
			string_html__output+='<div class="css__field_wrapper last"><div class="form-item"><input type="text" maxlength="100" name="visitor['+int__added_visitor_amount+'][email]" value="" class="form-text"/></div></div>';
		
		string_html__output+='<a class="css__deletion_link" href="javascript:RemoveVisitor('+int__added_visitor_amount+')"></a>';
		string_html__output+='</div></td></tr></table>';
		
        $('#visitor_data').append(string_html__output);
    }
	function RemoveVisitor(int__index)
	{
		document.getElementById('visitor_data').removeChild(document.getElementById('id__visitor_'+int__index));
	}
	
//	var array__selected_date=[];
	function DeployTimePicker(string__time_picker_layer_id,string__input_field_id/*string__date_field_id*/)
	{
		if (document.getElementById(string__input_field_id+'-wrapper'))
		{
//			array__selected_date=document.getElementById(string__date_field_id).value.split('/');
			document.getElementById(string__input_field_id+'-wrapper').innerHTML+=
				'<div class=\"css__time_picker\"><div class=\"css__time_picker_trigger\" onclick=\"ShowTimePicker(this,\''+string__time_picker_layer_id+'\',\''+string__input_field_id+'\')\"></div></div>';
//<div id=\"'+string__time_picker_layer_id+'\" class=\"css__time_picker_layer\" style=\"display:none\"></div>
			var obejct_dom__div=document.createElement('div');
			obejct_dom__div.id=string__time_picker_layer_id;
			obejct_dom__div.className='css__time_picker_layer';
			obejct_dom__div.style.display='none';
			document.body.appendChild(obejct_dom__div);
		}
	}
	
	
	var object__time_picker_code_status={};
	
	var int__time_picker_minute_interval=15;
	
	function GenerateTimePickerCode(string__input_field_id)
	{
		object__time_picker_code_status[string__input_field_id]=1;
		
		var object__date=new Date();
		
		var string__am_hour="";
		for (var int__index=1;int__index<=12;int__index++)
/*			if (array__selected_date[1]>object__date.getDate())
				string__am_hour+='<div class="css__hour_item" onclick="SelectTime(\''+string__input_field_id+'\',this,\''+int__index+'\',\'h\',\'am\')"><a>'+int__index+'</a></div>';
			else if (object__date.getHours()>int__index)
				string__am_hour+='<div class="css__hour_item css__disabled"><a>'+int__index+'</a></div>';
			else*/
				string__am_hour+='<div class="css__hour_item" onclick="SelectTime(\''+string__input_field_id+'\',this,\''+int__index+'\',\'h\',\'am\')"><a>'+int__index+'</a></div>';
		
		var string__pm_hour="";
		for (var int__index=1;int__index<=12;int__index++)
/*			if (array__selected_date[1]>object__date.getDate())
				string__pm_hour+='<div class="css__hour_item" onclick="SelectTime(\''+string__input_field_id+'\',this,\''+int__index+'\',\'h\',\'pm\')"><a>'+int__index+'</a></div>';
			else if ((object__date.getHours())>int__index+12)
				string__pm_hour+='<div class="css__hour_item css__disabled"><a>'+int__index+'</a></div>';
			else*/
				string__pm_hour+='<div class="css__hour_item" onclick="SelectTime(\''+string__input_field_id+'\',this,\''+int__index+'\',\'h\',\'pm\')"><a>'+int__index+'</a></div>';
		
		var string__minute="";
		for (var int__index=0;int__index<60;int__index+=int__time_picker_minute_interval)
/*			if (array__selected_date[1]>object__date.getDate())
				string__minute+='<div class="css__minute_item" onclick="SelectTime(\''+string__input_field_id+'\',this,\''+(int__index<10?int__index+'0':int__index)+'\',\'m\')"><a>'+(int__index<10?int__index+'0':int__index)+'</a></div>';
			else if ((object__date.getMinutes())>int__index)
				string__minute+='<div class="css__minute_item css__disabled"><a>'+(int__index<10?int__index+'0':int__index)+'</a></div>';
			else*/
				string__minute+='<div class="css__minute_item" onclick="SelectTime(\''+string__input_field_id+'\',this,\''+(int__index<10?int__index+'0':int__index)+'\',\'m\')"><a>'+(int__index<10?int__index+'0':int__index)+'</a></div>';
			
		var string__output='<table cellpadding="0" cellspacing="0">'
			+'<tr>'
				+'<td>'
					+'<table class="css__time_picker_container" cellpadding="0" cellspacing="0">'
						+'<tr class="css__header">'
							+'<td>Hour</td>'
							+'<td>Minute</td>'
						+'</tr>'
						+'<tr class="css__header_separator">'
							+'<td></td>'
							+'<td></td>'
						+'</tr>'
						+'<tr>'
							+'<td class="css__hour_column">'
								+'<div class="css__am_container">'
									+'<div class="css__period_item">AM</div>'
									+string__am_hour
								+'</div>'
								+'<div class="css__pm_container">'
									+'<div class="css__period_item">PM</div>'
									+string__pm_hour
								+'</div>'
							+'</td>'
							+'<td class="css__minute_column">'
								+string__minute
							+'</td>'
						+'</tr>'
					+'</table>'
				+'</td>'
				+'<td valign="top"><a class="css__close" onclick="HideTimePicker()"></a></td>'
			+'</tr>'
		+'</table>';

		return string__output;
	}
	var object_dom__last_opened_time_picker_layer,
		object_dom__last_opened_time_picker_input_field_id;
	function GetElementAbsoluteXCoordinate(object_dom___element,string___element_limiter_id)
	{
		var integer___x=0;
		if (!string___element_limiter_id)
		{
			do
				integer___x+=parseInt(object_dom___element.offsetLeft);
			while (object_dom___element=object_dom___element.offsetParent);
			return integer___x;
		}
		else
		{
			do
			{
				if (object_dom___element.id==string___element_limiter_id)
					return integer___x;
				integer___x+=parseInt(object_dom___element.offsetLeft);
			}
			while (object_dom___element=object_dom___element.offsetParent);
			return integer___x;
		}
	}
	function GetElementAbsoluteYCoordinate(object_dom___element,string___element_limiter_id)
	{
		var integer___y=0;
		if (!string___element_limiter_id)
		{
			do
				integer___y+=parseInt(object_dom___element.offsetTop);
			while (object_dom___element=object_dom___element.offsetParent);
			return integer___y;
		}
		else
		{
			do
			{
				if (object_dom___element.id==string___element_limiter_id)
					return integer___y;
				integer___y+=parseInt(object_dom___element.offsetTop);
			}
			while (object_dom___element=object_dom___element.offsetParent);
			return integer___y;
		}
	}
	function ShowTimePicker(object_dom__element,string__time_picker_layer_id,string__input_field_id)
	{
		
		document.getElementById(string__time_picker_layer_id).style.left=GetElementAbsoluteXCoordinate(object_dom__element)+'px';
		document.getElementById(string__time_picker_layer_id).style.top=GetElementAbsoluteYCoordinate(object_dom__element)+'px';
		
		if (object_dom__last_opened_time_picker_layer)
			object_dom__last_opened_time_picker_layer.style.display='none';
		
		object_dom__last_opened_time_picker_layer=document.getElementById(string__time_picker_layer_id);
		object_dom__last_opened_time_picker_input_field_id=string__input_field_id;
		
		document.getElementById(string__time_picker_layer_id).style.display='block';
		
		if (!object__time_picker_code_status[string__input_field_id])
		{
			document.getElementById(string__time_picker_layer_id).innerHTML=GenerateTimePickerCode(string__input_field_id);
		}
	}
	var int__hour_selection_status=0,
		int__minute_selection_status=0,
//		string__selected_hour="",
//		string__selected_minute="",
//		string__selected_period="",
		object_object_dom__last_selected_hour_element={},
		object_object_dom__last_selected_minute_element={},
		object_string__selected_hour={},
		object_string__selected_minute={},
		object_string__selected_period={};
		
	function SelectTime(string__input_field_id,object_dom__element,string__value,varchar__mode,varchar__period)
	{
		if (varchar__mode=='h')
		{
			int__hour_selection_status=1;
			object_string__selected_hour[string__input_field_id]=string__value;
//			string__selected_hour=string__value;
			object_string__selected_period[string__input_field_id]=varchar__period;
//			string__selected_period=varchar__period;
			
			if (object_object_dom__last_selected_hour_element[string__input_field_id])
				object_object_dom__last_selected_hour_element[string__input_field_id].className='css__hour_item';
			object_dom__element.className='css__hour_item css__selected';
			object_object_dom__last_selected_hour_element[string__input_field_id]=object_dom__element;
		}
		if (varchar__mode=='m')
		{
			int__minute_selection_status=1;
//			string__selected_minute=string__value;
			object_string__selected_minute[string__input_field_id]=string__value;
			if (object_object_dom__last_selected_minute_element[string__input_field_id])
				object_object_dom__last_selected_minute_element[string__input_field_id].className='css__minute_item';
			object_dom__element.className='css__minute_item css__selected';
			object_object_dom__last_selected_minute_element[string__input_field_id]=object_dom__element;
		}
		
		if (int__hour_selection_status==1 && int__minute_selection_status==1)
		{
			int__hour_selection_status=0;
			int__minute_selection_status=0;
			document.getElementById(string__input_field_id).value=object_string__selected_hour[string__input_field_id]+':'+(object_string__selected_minute[string__input_field_id]||'00')+' '+object_string__selected_period[string__input_field_id];
			object_dom__last_opened_time_picker_layer.style.display='none';
		}
		if (int__time_picker_closing_status==1)
		{
			int__hour_selection_status=0;
			int__minute_selection_status=0;
			document.getElementById(string__input_field_id).value=object_string__selected_hour[string__input_field_id]+':'+(object_string__selected_minute[string__input_field_id]||'00')+' '+object_string__selected_period[string__input_field_id];
			int__time_picker_closing_status=0;
		}
	}
	var int__time_picker_closing_status=0;
	function HideTimePicker(int__mode,object_dom__element,string__element_limiter_class_name)
	{	
		if (!int__mode)
		{
			if (object_string__selected_hour[object_dom__last_opened_time_picker_input_field_id])
			{
				int__time_picker_closing_status=1;
				SelectTime(object_dom__last_opened_time_picker_input_field_id);
			}
			object_dom__last_opened_time_picker_layer.style.display='none';
		}
		else if (int__mode==1)
		{
			if (!CheckpParentElement(object_dom__element,string__element_limiter_class_name))
				if (object_dom__last_opened_time_picker_layer)
				{
					if (object_string__selected_hour[object_dom__last_opened_time_picker_input_field_id])
					{
						int__time_picker_closing_status=1;
						SelectTime(object_dom__last_opened_time_picker_input_field_id);
					}
					object_dom__last_opened_time_picker_layer.style.display='none';
				}
		}
//		document.getElementById('id__time_picker_layer').style.display='none';
	}
	function CheckpParentElement(object_dom__element,string__element_limiter_class_name)
	{
		do
		{
			if (object_dom__element.className==string__element_limiter_class_name)
				return true;
		}
		while (object_dom__element=object_dom__element.offsetParent);
		return false;

	}
	
    function jeval(json){
        return eval('(' +  json + ')');
    }

    function ajax_update_form_data(propId){
        ajax_update_sr(propId);
    }

    function ajax_update_sr(propId){
        var success = false;
        var jqxhr = $.ajax({ url: "/wsservicerequest/ajax/category_control/" + propId,
            success: function(json) {
                var data = jeval(json);
                if(data['reqList'] == 'null'){
                    $("#edit-review").attr('disabled', 'disabled');
                    $("#edit-review").attr('value', 'N/A');
                } else {
                    $("#edit-review").attr('disabled', false);
                    $("#edit-review").attr('value', 'Submit');
                }
                if(typeof(requestList) == "undefined"){
                    //console.log("undef control"); //so we shouldn't work with sr control
                    return;
                }
                $("#sr_types_control").html(data['table']);
                requestList = data['reqList'];
                sr_control_add_onclick();
                currentSrId = null;
                $(".sr_group_selected").click();
            },
            //success: function(html) { $("#sr_types_control").html(html); success = true; }
            error:function() {
                $("#edit-review").attr('disabled', 'disabled');
                $("#edit-review").attr('value', 'N/A');
                if(typeof(requestList) == "undefined"){
                    //so we shouldn't work with sr control
                    return;
                }
                $("#sr_types_control").html('<tr><td><span class="ajax_error">Request error.</span></td></tr>');
            }
            /*complete: function() {
                if(success){
                    alert("complete"); 
                }
            }*/
        });
    }
	
    function sr_control_add_onclick(){
        $(".sr_group_select").click(function(){
                    if( !$(this).hasClass("sr_group_selected") ){
                        currentSrId = null;
                        $(".sr_cat_selected").toggleClass("sr_cat_selected");
                    }
                    $(".sr_group_selected").toggleClass("sr_group_selected");
                    $(this).toggleClass("sr_group_selected");
                    var key = $(this).text();
                    $("#sr_categories_list").html(" ");
                    $.each(requestList[key], function(id, val){
                        $("#sr_categories_list").append("<tr name=\""+ id +"\"><td class=\"short_description\">"+val["description"]+"</td><td class=\"arrow\">&rarr;<!--<img src=\"'.base_path().drupal_get_path('module', 'wsservicerequest').'/img/div_arrow.gif'.'\" />--></td><td class=\"extensive_description\">"+val["extensiveDescription"]+"</td></tr>");
                        if(currentSrId == null)
                            currentSrId = id;
                    });

                    $("#sr_categories_list tr").click(function(){
                        $("#edit-sr-category").attr("value",$(this).attr("name").substr(3));
                        $(".sr_cat_selected").toggleClass("sr_cat_selected");
                        $(this).toggleClass("sr_cat_selected");
                        currentSrId = $(this).attr("name");
                    });

                    $("#sr_categories_list tr[name=\'" + currentSrId + "\']").click();
                });
    }
	
    function datetime_shift(){
        var date=new Date();
        var hours = date.getHours();
        var currentMinutes = date.getMinutes();
        var minutes = Math.ceil(currentMinutes / int__time_picker_minute_interval) * int__time_picker_minute_interval;
	
        //if minutes multiply interval we should add one.
        if (minutes == currentMinutes){
            minutes += int__time_picker_minute_interval;
        }

        //return time shift for next step
        return date.getTime() + (minutes - currentMinutes) * 60000;
    }

	//init_timepicker("#edit-start-time");
	function init_timepicker(elemId, timestamp){
        if($(elemId).val()){ return; }

        date=new Date(timestamp);

        hours = date.getHours();
        minutes = date.getMinutes();

        var postfix = " AM";
        if (minutes < 10){
            minutes = "0" + minutes;
        }

        if(hours > 11){
            hours-=12;
            postfix = " PM";
        }

        if(hours == 12){postfix = " AM";}
        if(hours == 0){hours = 12;}


        var defTime = hours+":"+minutes+postfix;
        $(elemId).val(defTime);
    }

    //init_datepicker("#edit-start-date");
    function init_datepicker(elemId, timestamp){
        if($(elemId).val()){ return; }
        var date=new Date(timestamp);
        var defDate = (date.getMonth()+1) + '/' + date.getDate() + '/'  + date.getFullYear();
        $(elemId).val(defDate);
    }

    function clear_form_errors(){
        if(!$(".sr_form_errors").length == 0){
            $(".sr_form_errors").remove();
            $(".sr_form_error").removeClass("error").removeClass("sr_form_error");
        }
    }

    function set_form_error(fields, message){
        if(message){
            if($(".sr_form_errors").length == 0)
                $("form.sr_form").prepend('<div class="messages error sr_form_errors"><ul></ul></div>');
            $(".sr_form_errors ul").append('<li>'+message+'</li>');
        }
        //need more workaround for removing added error classes.
        if(fields){
            for(c in fields)
            $(fields[c]).addClass('error sr_form_error');
        }
    }


    function check_required(fields, message){
        var flag = true;
        for(c in fields){
            if($(fields[c]['id']).val() == ''){
                set_form_error(Array(fields[c]['id']), message + ' ' + fields[c]['name']);
                flag = false;
            }
        }
        return flag;
    }

    function validate_visitor(form){
        clear_form_errors();

        var required = Array(
            {'id': '#edit-start-date', 'name': 'Start Date'},
            {'id': '#edit-start-time', 'name': 'Start Time'}
        );
        if(!check_required(required, 'Please enter the'))
            return false;

        var userTime = Date.parse($("#edit-start-date").val()+' '+$("#edit-start-time").val());
        var currentTime = new Date;
        if(userTime + get_property_time_shift() <= currentTime.getTime()){
            set_form_error(Array("#edit-start-date", "#edit-start-time"), "Date and Time can't be in the past.");
            return false;
        }
        return true;
    }

    function validate_hvac(form){
        var errorFlag = true;
        clear_form_errors();

        var required = Array(
            {'id': '#edit-start-date', 'name': 'Start Date'},
            {'id': '#edit-start-time', 'name': 'Start Time'},
            {'id': '#edit-end-date', 'name': 'End Date'},
            {'id': '#edit-end-time', 'name': 'End Time'}
        );
        if(!check_required(required, 'Please enter the'))
            return false;

        var startTime = Date.parse($("#edit-start-date").val()+' '+$("#edit-start-time").val());
        var endTime = Date.parse($("#edit-end-date").val()+' '+$("#edit-end-time").val());
        var currentTime = new Date;
        if($("#edit-start-time").val() && (startTime + get_property_time_shift() <= currentTime.getTime())){
            set_form_error(Array("#edit-start-date", "#edit-start-time"), "Date and Time can't be in the past.");
            errorFlag = false;
        }
        if(startTime > endTime){
            if($("#edit-start-date").val() == $("#edit-end-date").val())
                set_form_error(Array("#edit-start-time", "#edit-end-time"), "Start Time is after End Time");
            else
                set_form_error(Array("#edit-start-date", "#edit-end-date"), "Start Date is after End Date");
            errorFlag = false;
        }
        return errorFlag;
    }

    function get_property_time_shift(){
        var propertyShift = timezones[$("#edit-origin-property-id").val()];
        var d = new Date();
        var userShift = d.getTimezoneOffset()*60;
        return (propertyShift - userShift) * 1000; //shift in ms, like Date.getTime(); result
    }
	
	
//DEV
/*	function validate_shared(object_dom__form)
	{
		if ($(recipient).val()=='')
		{
			alert("Please select reservation time");
			$('.css_div__timeframe_popup_visibility_control').addClass('css__form_field_error');
			return false;
		}
		return true;
	}
*/	
	function ShowLoadingIcon()
	{
		if (!document.getElementById('id_div__loading_icon_layer'))
		{
			var object__div=document.createElement('div');
			object__div.className="css_div__loading_icon_layer";
			object__div.id="id_div__loading_icon_layer";
			object__div.innerHTML='<div class="css_div__group"><div class="css_div__icon"></div></div>';
			
			document.body.appendChild(object__div);
		}
		else
		{
			$('#id_div__loading_icon_layer').show();
		}
		AdjustPopupPosition('id_div__loading_icon_layer');
//		$('.css_div__schedule_calendar .css_div__group').fadeTo(0,0.5);
	}
	
	function HideLoadingIcon()
	{
		$('#id_div__loading_icon_layer').hide();
//		$('.css_div__schedule_calendar .css_div__group').fadeTo(0,1);
	}
	
	function InitScheduleCalendar(string__start_full_date)
	{
		if (string__start_full_date && string__start_full_date!=string__current_week_start_date)
		{
			ShowLoadingIcon();
			slide_timeframes(string__start_full_date);
		}
		else if (!string__current_week_start_date)
		{
			ShowLoadingIcon();
			slide_timeframes("");
		}
//		else
//			ShowScheduleCalendar();
		ShowScheduleCalendar();
//	move calendar and loading icon with page scrolling
		$(window).scroll(function(){AdjustPopupPosition("id_div__schedule_calendar-template");AdjustPopupPosition("id_div__loading_icon_layer");});
//	move calendar and loading icon with page resizing
		$(window).resize(function(){AdjustPopupPosition("id_div__schedule_calendar-template");AdjustPopupPosition("id_div__loading_icon_layer");});
	}
	
	function ShowScheduleCalendar()
	{
//	scroll calendar to 8:00 intervals
		$('.css_div__timeframe-template').scrollTop(230);

//		document.getElementById('id_div__schedule_calendar-template').childNodes[0].style.top=(100+GetVerticalScrollingPostition())+'px';
//		$('#id_div__loading_icon_layer .css_div__group').attr('style',"top:"+(100+GetVerticalScrollingPostition())+'px;');
		
		
		
		if (document.getElementById('id_div__schedule_calendar-template').style.display=='none')
			document.getElementById('id_div__schedule_calendar-template').style.display='block';

		AdjustPopupPosition('id_div__schedule_calendar-template');
		
		
        timeFrames = init_selected_timeframes();
		
		$('#id_div__schedule_calendar-template .css_div__timeframe').scrollTop(304);
		
//		HideLoadingIcon();

		restore_timeframes();
	}
	function HideScheduleCalendar()
	{
		document.getElementById('id_div__schedule_calendar-template').style.display='none';
	}

	function CancelScheduleCalendarChanges()
	{
		$(".ui-selected").removeClass("ui-selected");
		$(".altui-selected").removeClass("altui-selected");
		timeFrames = {};
		HideScheduleCalendar();
	}
	
	
	function GroupSelectedTimeframeList(selectedTimeframes)
	{
		var date;
        var groups = {};
        var x;
        for(date in selectedTimeframes)
		{
            var dateFrames = selectedTimeframes[date];
            var first = dateFrames[0];
            for(x = 1; x<dateFrames.length; x++)
			{
                if( (parseInt(dateFrames[x]) - parseInt(dateFrames[x-1]) != 1) || (parseInt(dateFrames[x])%48 == 0) )
				{
					 //test for interval over a night
                    //IMPORTANT! It is the last timeframe, but end of reservation is half an hour later
                    //for block [0-1] reservation [12am-1am]
					if (!groups[date_format(shift_to_date(date,dateFrames[x-1]))])
						groups[date_format(shift_to_date(date,dateFrames[x-1]))]=new Array();
                    groups[date_format(shift_to_date(date,dateFrames[x-1]))].push({start: parseInt(first), end: parseInt(dateFrames[x-1])});
                    first = dateFrames[x];
				}
			}
			if (!groups[date_format(shift_to_date(date,dateFrames[x-1]))])
				groups[date_format(shift_to_date(date,dateFrames[x-1]))]=new Array();
            groups[date_format(shift_to_date(date,dateFrames[x-1]))].push({start: parseInt(first), end: parseInt(dateFrames[x-1])}); //last interval

		}

        var sortedGroups = {};
        var dateArr = new Array();
        for(date in groups){
            dateArr.push(date);
        }
        dateArr.sort(function(a, b){
            a = new Date(a);
            b = new Date(b);
            return a.getTime() - b.getTime();
        });
        for(x in dateArr){
            date = dateArr[x];
            sortedGroups[date] = groups[date];
        }

		return sortedGroups;
	}
	function ApplyScheduleCalendarChanges()
	{
		if(!get_timeframes())
		{
			return false;
		}
		if (isEmpty(timeFrames))
		{
			HideScheduleCalendar();
			return false;
		}
        
		var selectedTimeframes=GroupSelectedTimeframeList(timeFrames);

		var int__index=0;
		var array__result=new Array();
        for(var date in selectedTimeframes)
		{
            var array__selected_timeframe_list = selectedTimeframes[date];
			var array__temp=[];
			for (var__key in array__selected_timeframe_list)
			{
                var object__start_date=shift_to_date(date, array__selected_timeframe_list[var__key].start);
				
				var int__start_hours=object__start_date.getHours();
				var int__start_minutes=object__start_date.getMinutes()==0?'00':object__start_date.getMinutes();
				
                //this should be (and was somewhere in wsgeneral?) in function!
				var varchar__start_time_period='am';
				if (int__start_hours>11)
					varchar__start_time_period='pm';
				if (int__start_hours>12)
					int__start_hours=int__start_hours-12;
				if (int__start_hours==0)
					int__start_hours=12;	
					
                //date.end + 1 because we need to get the end of interval
                var object__end_date=shift_to_date(date, array__selected_timeframe_list[var__key].end + 1);
				var int__end_hours=object__end_date.getHours();
				var int__end_minutes=object__end_date.getMinutes()==0?'00':object__end_date.getMinutes();
				
				var varchar__end_time_period='am';
				if (int__end_hours>11)
					varchar__end_time_period='pm';
				if (int__end_hours>12)
					int__end_hours=int__end_hours-12;
				if (int__end_hours==0)
					int__end_hours=12;
				
				array__temp.push('<span>'+int__start_hours+':'+int__start_minutes+varchar__start_time_period+' - '+int__end_hours+':'+int__end_minutes+varchar__end_time_period+' </span>');				
            }
			
			var object__date=new Date(date);
			if (object__date.getDay()!=0)
				object__date.setDate(object__date.getDate()-object__date.getDay());
			array__result[int__index]=new Array();
			array__result[int__index].push(date);
			array__result[int__index].push(array__temp.join(', '));
			array__result[int__index].push('<a href="javascript:void(0)" onclick="InitScheduleCalendar(\''+date_format(object__date)+'\')">Edit</a>');
	
			int__index++;
		}
		
		HideScheduleCalendar();
		
		$('#id_div__timeframe_popup_visibility_control').removeClass('css__form_field_error');
		
		set_timeframes();
		
		document.getElementById('id_div__schedule_summary_preview').style.display="block";
		document.getElementById('id_div__schedule_summary_preview_container').innerHTML=WSSR_RenderReviewTableGrid("Time Reservation",["Date","Reserved Time","Action"],array__result);
		
		timeFrames = {};
	}
	
	function WSSR_RenderReviewTableGrid(string__title,array__column_title,array__row,array__column_width)
	{
		if (typeof(array__row)=='object' && array__row.length==0)
			return "";
		
		var string_html__thead="";
		if (typeof(array__column_title)=='object' && array__column_title.length>0)
		{
			var array__thead=new Array();
		    var int__temp_index=0;
		    for (var var__key in array__column_title)
		    {
		    	array__thead.push('<th class="flexiheader css_th__column-'+int__temp_index+(int__temp_index==array__column_title.length-1?' css_th__last_column':'')+'"><div>'+array__column_title[var__key]+'</div></th>');
		    	int__temp_index++;
		    }
		    array__thead.push('<th class="flexiheader css_th__blank_column"><div></div></th>');
		    string_html__thead='<tr>'+array__thead.join('')+'</tr>';
		}
		
		var array__tbody=new Array();
		for (var var__key in array__row)
	    {
			array__tbody[var__key]=new Array();
	    	for (var var__key_ in array__row[var__key])
	    		array__tbody[var__key].push('<td '+(typeof(array__column_width)=='object' && array__column_width[var__key_]?'style="width:'+array__column_width[var__key_]+'"':'')+'><div>'+array__row[var__key][var__key_]+'</div></td>');
	    	if (array__row[var__key].length>1)
	    		array__tbody[var__key].push('<td><div></div></td>');
			
	    	array__tbody[var__key]='<tr>'+array__tbody[var__key].join('')+'</tr>';
	    }

		string_html__result=
	   		'<div class="flexigrid">'
				+'<div class="hDiv">'
					+'<div class="hDivBox">'
						+'<table cellpadding="0" cellspacing="0">'
							+'<thead>'
								+'<tr>'
									+'<th><div>'+string__title+':</div></th>'
								+'</tr>'
							+'</thead>'
						+'</table>'
					+'</div>'
				+'</div>'
				+'<div class="bDiv">'
					+'<table class="css_table__review_control" cellpadding="0" cellspacing="0">'
						+string_html__thead
						+array__tbody.join('')
					+'</table>'
				+'</div>'
			+'</div>';
		return string_html__result;
	}
	
	function GetVerticalScrollingPostition()
	{
		if (document.documentElement.scrollTop)
			return document.documentElement.scrollTop;
		else if (document.body.scrollTop)
			return document.body.scrollTop;
		else return 0;
	}
	
	function ClearAllTimeframes()
	{
        $("#id_div__schedule_summary_preview_container").html("");
		document.getElementById('id_div__schedule_summary_preview').style.display="none";
		$(".ui-selected").removeClass("ui-selected");
		$(".altui-selected").removeClass("altui-selected");
		$("#edit-timeframes").val('');
		timeFrames = {};
		string__current_week_start_date=null;
	}
	
	
	function InitTimeframeControl(int__target_day_timestamp)
	{
		int__default_day_timestamp=int__target_day_timestamp;
		ClearAllTimeframes();
		slide_timeframes(int__target_day_timestamp);
		$('#id_div__timeframe_popup_visibility_control').show();
	}
	
	var int__default_day_timestamp;
	
	
	var object__timeframe_details={};
	var handle__timeframe_description_popup_timeout;
	function InitTimeFrameDescriptionPopup(int__reservation_timestamp)
	{
		if (!document.getElementById('id_div__timeframe_description_popup'))
		{
			var object_dom__popup_layer=document.createElement('div');
			object_dom__popup_layer.id='id_div__timeframe_description_popup';
			object_dom__popup_layer.innerHTML='<div class="css_div__timeframe_description_popup"></div>';
			document.body.appendChild(object_dom__popup_layer);
		}
		
		if (handle__timeframe_description_popup_timeout)
			clearTimeout(handle__timeframe_description_popup_timeout);
		handle__timeframe_description_popup_timeout=setTimeout("ShowTimeFrameDescriptionPopup("+int__reservation_timestamp+")",1000);
	}
	
	function ShowTimeFrameDescriptionPopup(int__reservation_timestamp)
	{
		if (typeof(object__timeframe_details[int__reservation_timestamp])!="undefined")
			document.getElementById('id_div__timeframe_description_popup').childNodes[0].innerHTML=object__timeframe_details[int__reservation_timestamp];
	}
	
	
    //timeframes
    //type: "none", "top", "bottom" (default) - type of round
    function date_to_shift(startDate, currentDate, type){
        if(typeof(startDate) == 'string')
            startDate = new Date(startDate);
        if(typeof(currentDate) == 'string')
            currentDate = new Date(currentDate);

        //1800000 == half an hour. For change if we need custom time shifts
        if (type = "bottom" || typeof type == "undefined")
            return Math.floor( (currentDate.getTime() - startDate.getTime()) / 1800000 );
        if (type == "top")
            return Math.ceil( (currentDate.getTime() - startDate.getTime()) / 1800000 );
        return (currentDate.getTime() - startDate.getTime()) / 1800000;
    }

    function shift_to_date(startDate, shift){
        if(typeof(startDate) == 'string')
            startDate = new Date(startDate);
        return new Date( shift * 1800000 + startDate.getTime() );
    }
	
    function leading_zero(num){
        if(num < 10)
            return "0"+num;
        else
            return num;
    }

    //primitive date to string format
    function date_format(date){
        return leading_zero(date.getMonth()+1) + "/" + leading_zero(date.getDate()) + "/" + date.getFullYear();
    }
	
    //check the object is empty
	function isEmpty(obj){
        for(var x in obj){ return false; }
        return true;
    }

    //return true if any timeframes selected.
    function timeframes_selected(){
        if( isEmpty(timeFrames) && $(recipient).val() == "" )
            return false
        return true;
    }
	
	function GetObjectPropertyAmount(object__temp)
	{
		var int__index=0;
		for (var var__key in object__temp)
			int__index++;
		return int__index;
	}
	
	function EncodeSelectedTimeframes(object__timeframe)
	{
		var array__result=[];
		for (var var__key in object__timeframe)
			if(object__timeframe[var__key].length != 0)
				array__result.push(var__key+':'+object__timeframe[var__key].join(','));
			
		return array__result.join(';');			
	}
	function DecodeSelectedTimeframes(string__timeframe)
	{
		var object__result={};
		
		var array__temp=string__timeframe.split(';');
		
		for (var var__key in array__temp)
		{
			var array__temp_=array__temp[var__key].split(':');
			object__result[array__temp_[0]]=[];
			
			var array__temp__=array__temp_[1].split(',');
			
			for (var int__index=0;int__index<array__temp__.length;int__index++)
				object__result[array__temp_[0]].push(array__temp__[int__index]);
		}		
		return object__result;
	}

	    function init_selected_timeframes(){
        var data = $(recipient).val();
        if (data){
            return DecodeSelectedTimeframes(data);
        }
        return {};
    }
	
	
	function ClearReservationConflicts()
	{
		$('#edit-timeframe-reservation-conflict-list').val('');
		$('#id_div__schedule_reservation_conflict').hide();
		$('#id_div__schedule_reservation_conflict_container').html('');
		$jq162("#timeframe-select .reservation_conflict").removeClass("reservation_conflict");
	}



    function shared_init(){
        $jq162("document").ready(function(){

            $jq162("#timeframe-select").selectable({
                filter: "td:not(.locked)"
            });
            restore_timeframes();
            $jq162("#timeframe-select").selectable("refresh");
            $jq162("form").submit(function()
            {
	            if ($(recipient).val()=="")
				{
					alert("Please select reservation time");
					$(".css_div__timeframe_popup_visibility_control").addClass("css__form_field_error");
					return false;
				}
                return get_timeframes();
            });

            /*
             * Altering UI-selectable behavior on Ctrl and normal selection
             */
             var cellsSelected = 0;
             var singleSelected = "";
             var ctrlShifted = 0;

             $jq162("#timeframe-select").selectable({
                 unselected: function(event, ui) {
                     //Ctrl not used so we should prevent current selection from disappearing
                     if(!event.ctrlKey){
                        $("#"+ui.unselected.id).addClass("altui-selected");
                     } else if(ctrlShifted != 1) {
                        $("#"+ui.unselected.id).addClass("altui-unselected");
                     }
                 },
                 selected: function(event, ui) {
                     if(event.ctrlKey || $("#"+ui.selected.id).hasClass("locked")){
                        $("#"+ui.selected.id).addClass("altui-unselected");
                     } else {
                        $("#"+ui.selected.id).addClass("altui-selected");
                     }
                 },
                 stop: function(event, ui) {
                     $(".altui-selected").addClass("ui-selected");
                     $(".altui-unselected").removeClass("ui-selected").removeClass("altui-unselected").removeClass("altui-selected");
                     if(cellsSelected == 0){
                        $("#"+singleSelected).removeClass("ui-selected").removeClass("altui-selected");
                     }
                 },
                 start: function(event, ui){
                    singleSelected = "";
                    if(event.ctrlKey){
                        ctrlShifted = 0;
                    } else {
                        ctrlShifted = 1;
                    }
                 },
                 selecting: function(event, ui){
                    cellsSelected++;
                    if(!singleSelected){
                        singleSelected = ui.selecting.id;
                        cellsSelected = 0;
                        if(!$("#"+singleSelected).hasClass("altui-selected")){
                            cellsSelected++;
                        }
                 }
                 },
                 unselecting: function(event, ui){cellsSelected--;}
             });
        });
    }

    function restore_timeframes()
        {
            var data = timeFrames[string__current_week_start_date];
            for(elem in data)
                $("#timeframe-"+data[elem]).addClass("ui-selected").addClass("altui-selected");
        }

        //check frames consistency on apply
        function test_timeframes(block)
        {
            if(typeof block == "undefined")
                block = 2;

            if(block == 1)
            	return true; //always correct for half-hour blocks.

            var test = new Array();
            $("#timeframe-select .ui-selected").each(function()
            {
                test.push( $(this).attr("id").substr(10) );
            });
            test = test.sort(function(a,b){return a-b;});
            var last = -1;
            var count = 0;
            for(x in test)
            {
                if(parseInt(test[x]) == last+1)
                    count++;
                else
                {
                    if(count % block != 0)
                    	return false;
                    count = 1;
                }
                last = parseInt(test[x]);
            }
            if(count % block != 0)
            	return false; //test last chain

            var int__last_item;
            for (var var__key in test)
            {
            	if ((parseInt(test[var__key])+1)%48==0)
            	{
            		if (!int__last_item)
            			return false;
            		else if (parseInt(test[var__key])-parseInt(int__last_item)!=1)
            			return false;
            	}
            	int__last_item=test[var__key];
            }


            return true;
        }

		var timeFrames = {};
        var reservationBlock = 2;
        function get_timeframes(){

            if(!test_timeframes(reservationBlock)){
                alert("Please, reserve resources in one-hour time frames.");
                return false;
            }
			if (string__current_week_start_date)
			{
                timeFrames[string__current_week_start_date] = new Array();

	            $("#timeframe-select .ui-selected").each(function()
	            {
                    timeFrames[string__current_week_start_date].push($(this).attr("id").split("-")[1]);
	            });

	            timeFrames[string__current_week_start_date].sort(function(a, b){
		            return parseInt(a) - parseInt(b);
		        });

	        //@karp: to validate and rewrite
	            var string__temp="";
	            for (var var__key in timeFrames)
				{
					for (var var__key_ in timeFrames[var__key])
					{
						var object__date_temp=new Date(shift_to_date(var__key,timeFrames[var__key][var__key_]));
                        if (string__temp && string__temp!=object__date_temp.getYear()+"_"+object__date_temp.getMonth())
                        {
                                alert("Selected time frames must be in the range of same month");
                                return false;
                        }
					    string__temp=object__date_temp.getYear()+"_"+object__date_temp.getMonth();
				    }
                }
                if(timeFrames[string__current_week_start_date].length == 0)
					delete timeFrames[string__current_week_start_date];
			}
            return true;
        }

        function set_timeframes()
        {
        	if (timeFrames)
            	$(recipient).val(EncodeSelectedTimeframes(timeFrames));
        }

        var string__current_week_start_date;
        function slide_timeframes(targetDate)
        {
            if(!get_timeframes())
                return;

			ShowLoadingIcon();

            switch (targetDate){
                case "":
                    startDate = new Date();
                    break;
                case "forward":
                    startDate.setDate(startDate.getDate() + 7);
                    break;
                case "backward":
                    startDate.setDate(startDate.getDate() - 7);
                    break;
                //or we have time string
                default:
                    startDate = new Date(targetDate);
			}

            //move to func?

            var dateString = (leading_zero(startDate.getMonth()+1)) + "-" + leading_zero(startDate.getDate()) + "-" + startDate.getFullYear();

	        var resId=$("#edit-origin-suite").val();
			var string__request_url="/wsservicerequest/ajax/shared_calendar/"+resId+"/" + dateString;

			$.ajax({ url: string__request_url,
            success: function(json)
            {
				HideLoadingIcon();
                var data = jeval(json);

                if (typeof(data)!="object" || data==null || !data["startDay"])
                {
                	alert("Workspeed Service Not Available");
                	if (targetDate!="forward" && targetDate!="backward")
                	HideScheduleCalendar();
                	return false;
                }


                string__current_week_start_date = data["startDay"];

                var tmpDate = new Date(string__current_week_start_date);
                $(".css_div__schedule_calendar .sticky-header th").each(function()
                {
                	$(this).html(tmpDate.toString().slice(0,3) + " " + (leading_zero(tmpDate.getMonth()+1)) + "/" + leading_zero(tmpDate.getDate()));
                    tmpDate.setDate(tmpDate.getDate() + 1);
                });

                $jq162("#timeframe-select .close").removeClass("close");
                $jq162("#timeframe-select .reserved").removeClass("reserved");
                $jq162("#timeframe-select .available").removeClass("available");
                $jq162("#timeframe-select .past_date").removeClass("past_date");
                $jq162("#timeframe-select .next_month_date").removeClass("next_month_date");
                $jq162("#timeframe-select .reservation").removeClass("reservation");
                $jq162("#timeframe-select .locked").removeClass("locked");
                $jq162("#timeframe-select .reservation_conflict").removeClass("reservation_conflict");
                $jq162("#timeframe-select .ui-selected").removeClass("ui-selected").removeClass("altui-selected");

                if(data["timeframes"])
                {
                    for(var type in data["timeframes"])
                    {
						for (var int__index=0;int__index<data["timeframes"][type].length;int__index++)
	                        for(var shift = data["timeframes"][type][int__index]["startTime"]; shift <= data["timeframes"][type][int__index]["endTime"]; shift++)
	                        {
	                            $("#timeframe-" + shift).addClass(type);
	                            if (type=="close" || type=="reserved" || type=="available")
	                            	$("#timeframe-" + shift).addClass("locked");
	                        }
                	}
                }

                var currentOffset = data["currentTimeframe"];
                var maxShift=48*7;
                for (var shift=0; (shift < Math.ceil(currentOffset)) && (shift < maxShift); shift++)
                {
					$("#timeframe-"+shift).addClass("locked");
					if (!$("#timeframe-"+shift).hasClass("close") && !$("#timeframe-"+shift).hasClass("reserved") && !$("#timeframe-"+shift).hasClass("available"))
						$("#timeframe-"+shift).addClass("past_date");
                }

                if ($("#edit-timeframe-reservation-conflict-list").val())
                {
                	var array__temp=$("#edit-timeframe-reservation-conflict-list").val().split(";");
                	for(var int__index in array__temp)
                	{
                		var array__temp_=array__temp[int__index].split(":");
                		if (array__temp_[0]==string__current_week_start_date)
                		{
	                		var array__temp__=array__temp_[1].split(",");
	                		for(var int__index_ in array__temp__)
	                		{
								$("#timeframe-" + array__temp__[int__index_]).addClass("reservation_conflict");
							}
						}
					}
				}
                restore_timeframes();
            },
            error:function()
            {
				alert("Workspeed Service Not Available");

				HideLoadingIcon();
				if (targetDate!="forward" && targetDate!="backward")
				HideScheduleCalendar();

				return false;
            }
        });

        }

    function shared_init_origin_apply(){
        origin_after_apply = function(elem, changed, names){
            if(!changed) return;
            var shared = this.path.slice(-1).pop();
            $("#origin_property").html(names.top);
            var leafName = names.leaf+ " (" + this.customData.fullTree.catData[this.customData.fullTree.resData[shared].category].value + ")";

            $("#origin_address").html(leafName);

            $("#edit-origin-property").attr("value", names.top);
            $("#edit-origin-property-id").attr("value", tree.path[0]);
            $("#edit-origin-address").attr("value", leafName);
            $("#edit-origin-suite").attr("value", shared);

            reservationBlock = this.customData.fullTree.resData[shared].reservationBlock / 30;

            if(!this.init)
                ClearAllTimeframes();
        }
    }

    function controlsDisable(controls){
        for(var x in controls)
		{
            //Couldn't find troubles on IE with old code - so reverted to prevent Date changes on Review Visitors
//			alert(controls[x])
//			alert($(controls[x]).context.tagName);
			/*if ($(controls[x]).is('input'))
	            $(controls[x]).attr("readonly", "readonly");
			else*/
				$(controls[x]).attr("disabled", "disabled");
        }
    }

    function controlsEnable(controls){
        for(var x in controls)
		{
			/*if ($(controls[x]).is('input'))
	            $(controls[x]).removeAttr("readonly");
			else*/
	            $(controls[x]).removeAttr("disabled");
        }
    }
	
	function ViewSRDetails(string__page_path)
	{
		var array__temp=new Array();
		var string__temp="";
		
		if ($("#edit-property").val()!='all')
			array__temp.push('property_id='+$("#edit-property").val());
		if ($("#edit-type").val()!='all')
			array__temp.push('type_id='+$("#edit-type").val());
		if ($("#edit-status").val()!='all')
			array__temp.push('status_id='+$("#edit-status").val());
		if ($("#edit-search-type").val())
			array__temp.push('searchType='+$("#edit-search-type").val());
			
		if (array__temp.length>0)
			string__temp='?'+array__temp.join('&');
		
		location.href=string__page_path+string__temp;
	}
	
	function ViewVisitorDetails(string__page_path)
	{
		var array__temp=new Array();
		var string__temp="";
		
		if ($("#edit-property").val()!='all')
			array__temp.push('property_id='+$("#edit-property").val());
		if ($("#edit-date").val())
			array__temp.push('date='+$("#edit-date").val());
		if ($("#edit-search-type").val())
			array__temp.push('searchType='+$("#edit-search-type").val());

		if (array__temp.length>0)
			string__temp='?'+array__temp.join('&');
		
		location.href=string__page_path+string__temp;
	}
	
	var int__participant_image_loading_status=0;
	var handle__participant_image_loading_timeout;
	function ShowParticipantImage(varchar__mode,int__id,string__key)
	{
		ShowLoadingIcon();

		var string__request_url="/wsservicerequest/getparticipantimage/"+int__id+'/'+string__key;

			var object_dom__image=new Image();
			object_dom__image.onload=function()
			{
				if (object_dom__image.width && object_dom__image.width>0)
				{
					HideLoadingIcon();
					int__participant_image_loading_status=1;
				}
				else
				{
					ProcessParticipantImageLoadingError();
					return false;
				}
				if (handle__participant_image_loading_timeout)
					clearTimeout(handle__participant_image_loading_timeout);
				if (!document.getElementById('id_div__participant_image_preview-popup'))
				{
					var object__div=document.createElement('div');
					object__div.className="css_div__participant_image_preview-popup";
					object__div.id="id_div__participant_image_preview-popup";
					
					array__temp=$('#participant_photo_image_dimensions').val().split('_');
				int__width=parseInt(array__temp[0]);
				int__height=parseInt(array__temp[1]);
				
				var string_attribute__dimensions="";
				if (object_dom__image.width!=int__width || object_dom__image.height!=int__height)
					string_attribute__dimensions='width="'+int__width+'px" height="'+int__height+'px"';
					
					var string_html__popup=
					'<div class="css_div__group" style="width:'+(int__width)+'px;margin-left:-'+(Math.floor((int__width+2)/2))+'px;">'
							+'<table cellspacing="0" cellpadding="0">'
								+'<tr>'
								+'<td style="width:'+int__width+'px;height:'+int__height+'px"><img alt="" '+string_attribute__dimensions+' id="id_img__participant"/></td>'
								+'</tr>'
							+'</table>'
						+'<div class="css_div__closing_link"><a href="javascript:void(0)" onclick="HideParticipantImage()">Close</a></div>'
						+'</div>';
					
					object__div.innerHTML=string_html__popup;
					
					document.body.appendChild(object__div);
				}
				else
				{
					$('#id_div__participant_image_preview-popup').show();
				}
				
			AdjustPopupPosition("id_div__participant_image_preview-popup");
			
			$(window).scroll(function(){AdjustPopupPosition("id_div__participant_image_preview-popup");AdjustPopupPosition("id_div__loading_icon_layer");});
			$(window).resize(function(){AdjustPopupPosition("id_div__participant_image_preview-popup");AdjustPopupPosition("id_div__loading_icon_layer");});

				$('#id_img__participant').attr('src',object_dom__image.src);
			}
			object_dom__image.src=string__request_url;
			
			handle__participant_image_loading_timeout=setTimeout(ProcessParticipantImageLoadingError,5000);
	}
	
	function AdjustPopupPosition(string__element_id)
		{
//	stretch popup layer height to full body height (for crossbrawser and html layout differences)
		$('#'+string__element_id).css('height',$(document).height());
		
		$('#'+string__element_id).css('width',$(document).width());
//	move popup to window center
		if ($('#'+string__element_id).children('div').outerHeight(true)>=$(window).height())
			var string_css__top_offset=GetVerticalScrollingPostition();
		else
			var string_css__top_offset=($(window).height()/2-$('#'+string__element_id).children('div').outerHeight(true)/2+GetVerticalScrollingPostition());
		$('#'+string__element_id).children('div').css('top',Math.floor(string_css__top_offset));
	}
	
	function ProcessParticipantImageLoadingError()
	{
		if (int__participant_image_loading_status!=1)
		{
			HideLoadingIcon();
			alert("Can't load image. Please try again later!");
		}
	}
	
	function HideParticipantImage()
	{
		$('#id_div__participant_image_preview-popup').hide();
	}
	
	

					
					
					
					
					
					
					
	
	
	
	