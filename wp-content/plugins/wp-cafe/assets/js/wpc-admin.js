(function($) {

    "use strict";

    $(document).ready(function() {

        var time_class = ['.wpc_weekly_schedule_start_time','.wpc_weekly_schedule_end_time','.wpc_exception_start_time',
            '.wpc_exception_end_time', '.multi_diff_start_time', '.multi_diff_end_time'];

        // load  timepicker  
        $.map(time_class,function(value,index){
            $('.schedule_main_block,.multi_schedule_block, .exception_main_block').on('focus', value , function(){
                $(this).timepicker({
                    timeFormat  : 'h:i A',
                    dynamic     : true,
                    listWidth   : 1  //full width
                });

                if ( index == 0 || index == 1 ) {
                    time_picker( $, $(this), "weekly" )
                }
                else if ( index == 2 || index == 3 ) {
                    time_picker( $, $(this), "exceptional" )
                }
            });
        });

        // checkbox show hide

        var checkbox_arr = ['#wpc_allow_cancellation','#wpc_admin_notification_for_booking_req',
            '#wpc_user_notification_for_booking_req','#wpcafe_allow_cart',
            '#wpc_user_notification_for_confirm_req','#wpc_admin_notification_for_confirm_req',
            '#wpc_user_notification_for_cancel_req','#wpc_admin_cancel_notification'];

        checkbox_default_show_hide( $, checkbox_arr )



        /**********************
         Add remove block
         **********************/

            // Exceptional Shcedule dynamically increase decrease
        var exception_obj = {wrapper_block: '.exception_block' , parent_block:'.exception_section',second_wrapper:'exception_block',
                append_wrapper:'.exception_main_block',button_wrapper:'.add_exception_block',date_name:'wpc_exception_date', exception_message:'schedule_exception_message',
                start_time_name:'wpc_exception_start_time',end_time_name:'wpc_exception_end_time',remove_button:'remove_exception_block'};

        add_exception_block( exception_obj , 'reservation_exception_schedule' );

        // Weekly shcedule dynamically increase decrease
        var add_weekly_schedule_block_params = {
            parent_block        : 'schedule_block',
            second_wrapper      : 'wpc-weekly-schedule-list',
            second_wrapper_extra: 'week_schedule_wrap',
            append_wrapper      : '.schedule_main_block',
            button_wrapper      : '.wpc-weekly-schedule-btn',
            button_class        : '.add_schedule_block',
            field_name          : 'wpc_weekly_schedule',
            start_time_name     : 'wpc_weekly_schedule_start_time',
            end_time_name       : 'wpc_weekly_schedule_end_time',
            remove_button       : 'remove_schedule_block',
            clear_button        : 'wpc_weekly_clear',
            validation_message  : 'weekly_message',
            block_type          : 'weekly',
            start_class         : 'wpc_weekly_schedule_start_time',
            end_class           : 'wpc_weekly_schedule_end_time',
        };

        add_week_block( add_weekly_schedule_block_params );

        // remove shcedule block
        var remove_weekly_blcok = { parent_block:'.schedule_main_block', remove_button:'.remove_schedule_block'
            , removing_block:'.schedule_block' };

        remove_block( remove_weekly_blcok );

        // remove exception shcedule block
        var remove_excepion_blcok = { parent_block:'.exception_main_block', remove_button:'.remove_exception_block'
            , removing_block:'.exception_block' };

        remove_block( remove_excepion_blcok );

        // clear action
        var clear_class = ['.wpc_weekly_clear','.wpc_all_day_clear','.exception_time_clear', '.wpc_multi_weekly_clear'];

        $.each(clear_class,function(ind,val){
            $('.wpc-all-day-schedule,.schedule_main_block,.multi_schedule_block,.exception_main_block').on('click', val ,function(){
                var $this = $(this);
                if ( ind === 0 ) {
                    $('.wpc_weekly_schedule_start_time_'+$this.attr("id") ).val('');
                    $('.wpc_weekly_schedule_end_time_'+$this.attr("id") ).val('');
                    $('.weekly_message_'+$this.attr("id") ).html('');
                }
                else if( ind === 1 ) {
                    $('input[name="wpc_all_day_start_time"]').val('');
                    $('input[name="wpc_all_day_end_time"]').val('');
                    $('.all_day_message').html('');
                }
                else if( ind === 2 ) {
                    $('.wpc_exception_start_time_'+$this.attr("id") ).val('');
                    $('.wpc_exception_end_time_'+$this.attr("id") ).val('');
                    $('.exception_message_'+$this.attr("id")).html('');

                }
                else if( ind === 3 ) {
                    $('.multi_diff_start_time_'+$this.attr("id") ).val('');
                    $('.multi_diff_end_time_'+$this.attr("id") ).val('');
                    $('.diff_schedule_name_'+$this.attr("id") ).val('');
                    $('.diff_seat_capacity_'+$this.attr("id") ).val('');
                    $('.weekly_multi_message_'+$this.attr("id")).html('');

                }
            })
        })

        // weekly schedule select
        var selected_values = new Array();
        $('.week_schedule_wrap :checkbox').each(function(){
            if($(this).is(":checked")){
                selected_values.push($(this).attr('class'))
            }
        });

        $('.schedule_main_block').on( 'change' , '.week_schedule_wrap :checkbox' , function(){
            var wpc_all_day_start = $(".wpc_all_day_start").val();
            var wpc_all_day_end   = $(".wpc_all_day_end").val();
            if( wpc_all_day_start == "" && wpc_all_day_end == "" ){
                var value = $(this).attr('class');
                if( $(this).is(":checked") )
                {
                    var check = $.inArray(value , selected_values );
                    // if not exist , push in array
                    if( check == -1 ){
                        selected_values.push(value);
                    }else{
                        $(this).prop("checked", false);
                        alert( value.toUpperCase() + " " +"day exist. Please check another day");
                    }
                }
                else{
                    selected_values.splice(selected_values.indexOf(value),1);
                }
                if (selected_values.length == 0) {
                    $('input.wpc_all_day_start,input.wpc_all_day_end').removeAttr("disabled");
                }
            }
            else
            {
                var schedule_block = $(".week_schedule_wrap :checkbox");
                $(schedule_block). prop("checked", false);
                alert( "You have already set all day schedule . Please unset all day schedule .");
            }
        });

        // all day shcedule
        $(".wpc-all-day-schedule").on('focus',".wpc_all_day_start , .wpc_all_day_end", function(){
            if( selected_values.length == 0 ){
                $('input.wpc_all_day_start,input.wpc_all_day_end').removeAttr("disabled");
                $(this).timepicker({
                    timeFormat  : 'h:i A',
                    dynamic     : true,
                    listWidth   : 1
                });
                time_picker( $, $(this) , 'all_day' );

            }else{
                $('input.wpc_all_day_start,input.wpc_all_day_end').val();
                $('input.wpc_all_day_start,input.wpc_all_day_end').prop( 'disabled', 'disabled' );
                alert( "You have already checked weekly schedule . Please uncheck weekly schedule .");
            }
        });


        /****************************
         Guest reservation form
         *****************************/


            //====================== Reservatin form actions start ================================= //
        var obj = {}; var wpc_booking_form_data ={};
        if (typeof wpc_form_data !== "undefined") {
            if ( $.isArray( wpc_form_data ) && wpc_form_data.length === 0 ) {
                wpc_booking_form_data = null;
            }else{
                wpc_booking_form_data = wpc_form_data[0];
            }
            obj.wpc_form_client_data = wpc_booking_form_data;
            var $wpc_booking_section = $('.wpcafe-meta');
            var wpc_booking_date = $wpc_booking_section.find("#wpc_booking_date");
            if ( wpc_booking_date.length > 0) {
                obj.wpc_booking_date = wpc_booking_date;
                obj.booking_form_type= "admin";
                obj.inline_value     = false;
                obj.reserve_status   = null;

                reservation_form_actions( $ , obj )
            }
        }
        //====================== Reservatin form actions end ================================= //



        //admin settings tab
        $('.wpc-settings').on('click',".wpc-tab li a",function(e){
            e.preventDefault();
            var data_id = $(this).attr('data-id');
            $(".wpc-tab li a").removeClass("nav-tab-active");
            $(this).addClass("nav-tab-active");
            $(".tab-content .tab-pane").removeClass("active");
            $(".tab-pane[data-id='tab_" + data_id + "']").addClass("active");

            // set current tab
            $(".settings_tab").val("").val(data_id);

            // Hide submit button for Hooks tab
            var settings_submit = $("#cafe_settings_submit");
            if ( data_id =="hooks" ) {
                settings_submit.addClass("hide_field");
            }
            else{
                settings_submit.removeClass("hide_field");
            }

        });


        // Default party size validation

        var select_class = ['#wpc_default_gest_no','#wpc_min_guest_no','#wpc_max_guest_no'];

        var default_error = $('.default_error'); var min_error = $('.min_error'); var max_error = $('.max_error');

        $.each( select_class , function(index,value){
            $( value ).on('change',function(element){

                var default_guest_val = $('#wpc_default_gest_no :selected').val();
                var wpc_min_guest_no = $('#wpc_min_guest_no :selected').val();
                var wpc_max_guest_no = $('#wpc_max_guest_no :selected').val();

                // default
                if (  parseInt(default_guest_val) == 0 || 
                ( parseInt(default_guest_val) >= parseInt(wpc_min_guest_no)  &&  parseInt(wpc_max_guest_no) > parseInt(default_guest_val) ) ) {
                    default_error.fadeOut()
                    default_error.addClass('hide_field')
                    $('#wpc_default_gest_no :selected').prop('selected',true)
                } else {
                    default_error.fadeIn()
                    default_error.removeClass('hide_field')
                    $('#wpc_default_gest_no :selected').prop('selected',false)
                }

                if(wpc_min_guest_no !=="" && wpc_max_guest_no !==""){
                    // minimum
                    if ( parseInt(wpc_min_guest_no) < parseInt(wpc_max_guest_no) ) {
                        min_error.fadeOut()
                        min_error.addClass('hide_field')
                        $('#wpc_min_guest_no :selected').prop('selected',true)
                    } else {
                        min_error.fadeIn()
                        min_error.removeClass('hide_field')
                        $('#wpc_min_guest_no :selected').prop('selected',false)
                    }

                    // maximum
                    if ( parseInt(wpc_max_guest_no) > parseInt(wpc_min_guest_no) ) {
                        max_error.fadeOut()
                        max_error.addClass('hide_field')
                        $('#wpc_max_guest_no :selected').prop('selected',true)
                    } else {
                        max_error.fadeIn()
                        max_error.removeClass('hide_field')
                        $('#wpc_max_guest_no :selected').prop('selected',false)
                    }
                }
            });
        });
    });

    // load color picker
    var color_array = ["#wpc_primary_color","#wpc_secondary_color","#mini_product_bg"];
    $.each(color_array,function(index,value){
        $(value).wpColorPicker();
    });

    // **********************
    //  get from value in shortcode settings
    //  ****************************

     $('.shortcode-generator-wrap').on('click', '.shortcode-generate-btn', function (event) {
        event.preventDefault();
        var arr = [];

        $(this).parents('.shortcode-generator-wrap').find(".wpc-field-wrap").each(function(){
            var $this = $(this);
            var data = $this.find('.wpc-setting-input').val();
            var option_name = $this.find('.wpc-setting-input').attr('data-cat');
            var post_count = $this.find('.post_count').attr('data-count');

            if(option_name !=undefined && option_name !=''){
                data = option_name+' = '+ (data.length ? data : '""');
            }
            if(post_count !=undefined && post_count !=''){
                data = post_count+' = '+ (data.length ? data : '""');
            }
            arr.push(data);
        });


       var allData = arr.filter(Boolean);
       var shortcode = "["+ allData.join(' ') +"]";

      $(this).parents('.shortcode-generator-wrap').find('.wpc_include_shortcode').val(shortcode);
      $(this).parents('.shortcode-generator-wrap').find('.copy_shortcodes').slideDown();
      
    });

    $('.shortcode-generator-wrap').on('click', '.s-generate-btn', function (event) {
        var $this = $(this);
       $($this).parents('.shortcode-generator-wrap').find('.shortcode-generator-main-wrap').fadeIn();

       $($this).parents('.shortcode-generator-wrap').mouseup(function(e){
            var container = $(this).find(".shortcode-generator-inner");
            var container_parent = container.parent(".shortcode-generator-main-wrap");
            if(!container.is(e.target) && container.has(e.target).length === 0){
                container_parent.fadeOut();
            }
        });

    });
    $('.shortcode-generator-wrap').on('click', '.shortcode-popup-close', function (event) {
       $(this).closest('.shortcode-generator-main-wrap').fadeOut();
    });


    $(".wpc-field-wrap").each(function(){
        $(this).find(".get_schedule_template").on('change', function(){
            $(this).find("option:selected").each(function(){
                var $this = $(this);
                var optionValue = $this.attr("value");
                if(optionValue === 'schedules'){
                    $this.parents(".shortcode-generator-inner").find('.wpc-shortcode-select').attr("multiple", 'multiple');
                }else if(optionValue =='etn_pro_schedules_tab'){
                    $this.parents(".shortcode-generator-inner").find('.wpc-shortcode-select').attr("multiple", 'multiple');
                } else{
                    $this.parents(".shortcode-generator-inner").find('.wpc-shortcode-select').removeAttr("multiple");
                }
            });
        }).change();

    });
   show_conditinal_field($,'select',"form_style ='2'", '.image-url-field');

    /*--------------------------------
    // Shortcode builder action
    -----------------------------------*/
    var list_style = $(".list-style"); var tab_style = $(".tab-style");
    $(".free-options").on('change',function() {
        if ($(this).val() == "wpc_food_menu_list") {
            list_style.removeClass("wpc-d-none")
            tab_style.addClass("wpc-d-none")
        } else {
            tab_style.removeClass("wpc-d-none")
            list_style.addClass("wpc-d-none")
        }
    })

  // **********************
    //  end
  //  ****************************


})(jQuery)

// show/hide conditional field in shortcode generate
function show_conditinal_field($, selectClass, optionName, showHideClass){
    $(selectClass).on('change', function(){
        $(this).find("option:selected").each(function(){
            var optionValue = $(this).attr("value");
            if(optionValue !== optionName){
                $('.shortcode-generator-inner').find(showHideClass).show();
            } else{
                $('.shortcode-generator-inner').find(showHideClass).hide();
            }
        });
    });
}


// if get value 0 turn into time
function time_picker( $, data , type ="" ) {
    data.on('changeTime',function(){
        if ( "0" === data.val() ) {
            data.val('12:00 AM')
        }

        switch (type) {
            case "all_day":
                schedule_time_validation( $ , type , data );
                break;
            case "multi_all_day":
                schedule_time_validation( $ , type , data );
                break;
            case "multi_diff_days":
                schedule_time_validation( $ , type , data );
                break;
            case "weekly":
                schedule_time_validation( $ , type , data );
                break;
            case "exceptional":
                schedule_time_validation( $ , type , data );
                break;
            case "pickup":
                schedule_time_validation( $ , type , data );
                break;
            case "delivery":
                schedule_time_validation( $ , type , data );
                break;
            default:
                break;
        }

        data.timepicker('hide');
    })
}

/**
 * Reservation scheudle time validation
 */
function schedule_time_validation( $ , type = '' , input ){
    var start = ""; var end = ""; var response = ""; var message_class="";
    switch ( type ) {
        case 'all_day':
            start   = $(".wpc_all_day_start").val();
            end     = $(".wpc_all_day_end").val();
            message_class = $(".all_day_message");
            // all day schedule get message
            settings_time_validation( start, end , message_class , input );

            break;
        case 'multi_all_day':
            var id  = input.attr('id');
            start   = $(".multi_start_time_"+id).val();
            end     = $(".multi_end_time_"+id).val();
            message_class = $(".allday_multi_message_"+id);
            // all day schedule get message
            settings_time_validation( start, end , message_class , input );

            break;
        case 'multi_diff_days':
            var id  = input.attr('id');
            start   = $(".multi_diff_start_time_"+id).val();
            end     = $(".multi_diff_end_time_"+id).val();
            message_class = $(".weekly_multi_message_"+id);
            // all day schedule get message
            settings_time_validation( start, end , message_class , input );

            break;

        case 'weekly':
            var id  = input.attr('id');
            start   = $(".wpc_weekly_schedule_start_time_"+id ).val();
            end     = $(".wpc_weekly_schedule_end_time_"+id ).val();
            message_class = $( ".weekly_message_"+id );
            // weekly schedule get message
            settings_time_validation( start, end , message_class , input );

            break;
        case 'exceptional':
            var id  = input.attr('id');
            start   = $(".wpc_exception_start_time_"+id ).val();
            end     = $(".wpc_exception_end_time_"+id ).val();
            message_class = $( ".schedule_exception_message_"+id );
            // exceptional schedule get message
            settings_time_validation( start, end , message_class , input );
            break;

        case 'pickup':
            var id  = input.attr('id');
            start   = $(".wpc_pickup_start_time_"+id ).val();
            end     = $(".wpc_pickup_end_time_"+id ).val();
            message_class = $( ".pickup_valid_message_"+id );
            // pickup schedule get message
            settings_time_validation( start, end , message_class , input );
            break;

        case 'delivery':
            var id  = input.attr('id');
            start   = $(".wpc_delivery_start_time_"+id ).val();
            end     = $(".wpc_delivery_end_time_"+id ).val();
            message_class = $( ".delivery_valid_message_"+id );
            // delivery schedule get message
            settings_time_validation( start, end , message_class , input );
            break;
        default:
            break;
    }

    return response;
}

/**
 * Get settings time validation message
 */
function settings_time_validation( start, end , message_class , input ) {
    if ( start !== "" && end !== "" ) {
        var data = compare_between_time( start , end );
        if ( data == "early" || data == "equal" ) {
            var message = typeof wpc_form_data.time_valid_message !=="undefined" && wpc_form_data.time_valid_message !="" ? wpc_form_data.time_valid_message : "End time must be after start time";
            // Print message
            message_class.html( "" ).html( message ).fadeIn();
            input.val("")
        } else {
            message_class.html( "" ).fadeOut();
        }
    }
}

function compare_between_time( start , end ) {
    var startTime   = convert24Format( start );
    var endTime     = convert24Format( end );
    var startTime   = moment.duration(startTime).asSeconds();
    var endTime     = moment.duration(endTime).asSeconds();
    var data = "unknown";
    if (moment(endTime).isBefore(moment(startTime))) {
        data = "early";
    } else if (moment(startTime).isSame(moment(endTime))) {
        data = "equal";
    } else if (moment(endTime).isAfter(moment(startTime))) {
        data = "success";
    }

    return data
}

// copy text
function copyTextData( fieldId ){
    var fieldData = document.getElementById(fieldId);
    if( fieldData ){
        fieldData.select();
        document.execCommand("copy");
    }
}

// adding  schedule block
function add_week_block( obj ) {
    var week_days = ['Sat','Sun','Mon','Tue','Wed','Thu','Fri'];
    var i = jQuery( '.'+obj.second_wrapper_extra ).length;
    var createField = 8 - i ; var html = "";
    // add shcedule block
    jQuery( obj.button_wrapper ).on( 'click' , obj.button_class , function() {
        if( i <= createField ){
            var startTimeText = jQuery(this).data("start_time");
            var endTimeText = jQuery(this).data("end_time");

            var html ="";
            jQuery( obj.append_wrapper ).append(
                '<div class="'+obj.parent_block+' '+obj.second_wrapper_extra+'">'+
                '<div class="'+obj.second_wrapper+'">'+
                jQuery.map( week_days , function( day , key ){
                    var day_lower = day.toLowerCase();
                    html +='<input type="checkbox" name="'+obj.field_name+'['+i+']['+day+']" class="'+day_lower+'" id="'+obj.block_type+'_'+day_lower+'_'+i+'"/><label for="'+obj.block_type+'_'+day_lower+'_'+i+'">'+day+'</label>'
                })
                +html+'</div>'+
                '<div class="wpc-schedule-field">'+
                '<input type="text" name="'+obj.start_time_name+'[]" class="'+obj.start_time_name+' '+obj.start_class+'_'+i+' wpc_mt_two wpc-mr-one wpc-settings-input attr-form-control" id='+i+' placeholder="'+ startTimeText +'" />'+
                '<input type="text" name="'+obj.end_time_name+'[]" class="'+obj.end_time_name+' '+obj.end_class+'_'+i+'  wpc-settings-input attr-form-control" id='+i+' placeholder="'+ endTimeText +'"/>'+
                '<span class="'+obj.clear_button+'" id='+i+'> clear </span>'+
                '</div>'+
                '<div class="'+obj.validation_message+'_'+i+' wpc-default-guest-message"></div>'+
                '<span class="dashicons dashicons-no-alt '+obj.remove_button+' wpc-btn"></span>'+

                '</div>');
            i++;
        }
    });
    return html;
}

// remove weekly reservation multislot block
var remove_weekly_multi_slot = {
    parent_block: '.multi_schedule',
    remove_button: '.remove_weekly_schedule_block',
    removing_block: '.single-weekly-multi-schedule'
};

remove_block(remove_weekly_multi_slot);

// adding exception block
function add_exception_block( obj , block_name=false ) {
    var increase = jQuery(obj.wrapper_block).length;
    jQuery(obj.parent_block).on( 'click' , obj.button_wrapper , function() {
        increase++;
        switch (block_name) {
            case 'reservation_exception_schedule':
                var startTimeText = jQuery(this).data("start_time");
                var endTimeText = jQuery(this).data("end_time");
                jQuery( obj.append_wrapper ).append(
                    '<div class="'+obj.second_wrapper+' d-flex mb-2">'+
                    '<input type="text" name="'+obj.date_name+'[]" class="'+obj.date_name+' wpc_mt_two wpc-mr-one wpc-settings-input attr-form-control" placeholder="Date" id="'+obj.date_name+'_'+increase+'" />'+
                    '<input type="text" name="'+obj.start_time_name+'[]" class="'+ obj.start_time_name +'  '+obj.start_time_name+'_'+increase+' wpc-settings-input attr-form-control" id="'+increase+'" placeholder="'+ startTimeText +'" />'+
                    '<input type="text" name="'+obj.end_time_name+'[]" class="'+ obj.end_time_name +'  '+obj.end_time_name+'_'+ increase +' wpc-settings-input attr-form-control" id="'+increase+'" placeholder="'+ endTimeText +'"/>'+
                    '<span class="exception_time_clear" id='+increase+'> clear </span>'+
                    '<span class="wpc-btn dashicons dashicons-no-alt '+obj.remove_button+' wpc_icon_middle_position"></span>'+
                    '</div>'+
                    '<div class= '+obj.exception_message+'"+_+""'+increase+'></div>'
                );
                break;

            default:
                break;
        }
    });

    flat_date_pick('.exception_main_block', '.wpc_exception_date');

}

// remove block
function remove_block( obj ) {
    jQuery(obj.parent_block).on( 'click' , obj.remove_button , function(e) {
        e.preventDefault();
        jQuery(this).parent( obj.removing_block ).remove();
    });
}

function checkbox_default_show_hide( $, checkbox_arr ) {
    $.map( checkbox_arr, function( value , index ){
        // checkbox checked / unchecked value set
        $(value).on('change',function(){
            var get_sibling = $(this).siblings('input[type="checkbox"][value="off"]');
            if (get_sibling) {
                if ( $(this).is(':checked') ) {
                    $(this).attr('checked',true)
                    get_sibling.attr('checked',false)
                }else{
                    $(this).removeAttr('checked')
                    get_sibling.attr('checked',true)
                }
            }
        })
    } )
}


function flat_date_pick(parent_name,class_name ){

    jQuery(parent_name).on('focus',class_name, function(){
        jQuery(this).flatpickr();
    });
}
