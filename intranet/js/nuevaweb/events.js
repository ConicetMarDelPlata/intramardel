jQuery(document).ready(function($)
{
    // Image picker on terms menu
    $('.mec_upload_image_button').click(function(event)
    {
        event.preventDefault();
        
        var frame;
        if(frame)
        {
            frame.open();
            return;
        }

        frame = wp.media();
        frame.on('select', function()
        {
            // Grab the selected attachment.
            var attachment = frame.state().get('selection').first();

            $('#mec_thumbnail_img').html('<img src="'+attachment.attributes.url+'" />');
            $('#mec_thumbnail').val(attachment.attributes.url);
            
            $('.mec_remove_image_button').toggleClass('mec-util-hidden');
            
            frame.close();
        });
        
        frame.open();
    });
    
    // Image remover on terms menu
    $('.mec_remove_image_button').click(function(event)
    {
        event.preventDefault();
        
        $('#mec_thumbnail_img').html('');
        $('#mec_thumbnail').val('');
        
        $('.mec_remove_image_button').toggleClass('mec-util-hidden');
    });
    
    // Image picker on add event menu for location
    $('.mec_location_upload_image_button').click(function(event)
    {
        event.preventDefault();
        
        var frame;
        if(frame)
        {
            frame.open();
            return;
        }

        frame = wp.media();
        frame.on('select', function()
        {
            // Grab the selected attachment.
            var attachment = frame.state().get('selection').first();

            $('#mec_location_thumbnail_img').html('<img src="'+attachment.attributes.url+'" />');
            $('#mec_location_thumbnail').val(attachment.attributes.url);
            
            $('.mec_location_remove_image_button').toggleClass('mec-util-hidden');
            
            frame.close();
        });
        
        frame.open();
    });
    
    // Image remover on add event menu for location
    $('.mec_location_remove_image_button').click(function(event)
    {
        event.preventDefault();
        
        $('#mec_location_thumbnail_img').html('');
        $('#mec_location_thumbnail').val('');
        
        $('.mec_location_remove_image_button').toggleClass('mec-util-hidden');
    });
    
    // Image picker on add event menu for organizer
    $('.mec_organizer_upload_image_button').click(function(event)
    {
        event.preventDefault();
        
        var frame;
        if(frame)
        {
            frame.open();
            return;
        }

        frame = wp.media();
        frame.on('select', function()
        {
            // Grab the selected attachment.
            var attachment = frame.state().get('selection').first();

            $('#mec_organizer_thumbnail_img').html('<img src="'+attachment.attributes.url+'" />');
            $('#mec_organizer_thumbnail').val(attachment.attributes.url);
            
            $('.mec_organizer_remove_image_button').toggleClass('mec-util-hidden');
            
            frame.close();
        });
        
        frame.open();
    });
    
    // Image remover on add event menu for organizer
    $('.mec_organizer_remove_image_button').click(function(event)
    {
        event.preventDefault();
        
        $('#mec_organizer_thumbnail_img').html('');
        $('#mec_organizer_thumbnail').val('');
        
        $('.mec_organizer_remove_image_button').toggleClass('mec-util-hidden');
    });
    
    // Image remover on frontend event submission menu
    $('#mec_fes_remove_image_button').click(function(event)
    {
        event.preventDefault();
        
        $('#mec_fes_thumbnail_img').html('');
        $('#mec_fes_thumbnail').val('');
        $('#mec_featured_image_file').val('');
        
        $('#mec_fes_remove_image_button').addClass('mec-util-hidden');
    });
    
    $('#mec_start_date').datepicker(
    {
        changeYear: true,
        changeMonth: true,
        dateFormat: 'yy-mm-dd',
        gotoCurrent: true,
        yearRange: 'c-3:c+5',
    });
    
    $('#mec_end_date').datepicker(
    {
        changeYear: true,
        changeMonth: true,
        dateFormat: 'yy-mm-dd',
        gotoCurrent: true,
        yearRange: 'c-3:c+5',
    });
    
    $('#mec_date_repeat_end_at_date').datepicker(
    {
        changeYear: true,
        changeMonth: true,
        dateFormat: 'yy-mm-dd',
        gotoCurrent: true,
        yearRange: 'c-3:c+5',
    });
    
    $('.mec_date_picker').datepicker(
    {
        changeYear: true,
        changeMonth: true,
        dateFormat: 'yy-mm-dd',
        gotoCurrent: true,
        yearRange: 'c-3:c+5',
    });
    
    $('#mec_location_id').on('change', function()
    {
        mec_location_toggle();
    });
    
    $('#mec_organizer_id').on('change', function()
    {
        mec_organizer_toggle();
    });
    
    mec_location_toggle();
    mec_organizer_toggle()
    
    $('#mec_repeat').on('change', function()
    {
        mec_repeat_toggle();
    });
    
    mec_repeat_toggle();
    
    $('#mec_repeat_type').on('change', function()
    {
        mec_repeat_type_toggle();
    });
    
    mec_repeat_type_toggle();
    
    $('#mec_bookings_limit_unlimited').on('change', function()
    {
        mec_bookings_unlimited_toggle();
    });
    
    $('#mec_add_in_days').on('click', function()
    {
        var date = $('#mec_exceptions_in_days_date').val();
        if(date === '') return false;
        
        var key = $('#mec_new_in_days_key').val();
        var html = $('#mec_new_in_days_raw').html().replace(/:i:/g, key).replace(/:val:/g, date);
        
        $('#mec_in_days').append(html);
        $('#mec_new_in_days_key').val(parseInt(key)+1);
    });
    
    $('#mec_add_not_in_days').on('click', function()
    {
        var date = $('#mec_exceptions_not_in_days_date').val();
        if(date === '') return false;
        
        var key = $('#mec_new_not_in_days_key').val();
        var html = $('#mec_new_not_in_days_raw').html().replace(/:i:/g, key).replace(/:val:/g, date);
        
        $('#mec_not_in_days').append(html);
        $('#mec_new_not_in_days_key').val(parseInt(key)+1);
    });
    
    $('#mec_add_ticket_button').on('click', function()
    {
        var key = $('#mec_new_ticket_key').val();
        var html = $('#mec_new_ticket_raw').html().replace(/:i:/g, key);
        
        $('#mec_tickets').append(html);
        $('#mec_new_ticket_key').val(parseInt(key)+1);
    });
    
    $('#mec_add_hourly_schedule_button').on('click', function()
    {
        var key = $('#mec_new_hourly_schedule_key').val();
        var html = $('#mec_new_hourly_schedule_raw').html().replace(/:i:/g, key);
        
        $('#mec_hourly_schedules').append(html);
        $('#mec_new_hourly_schedule_key').val(parseInt(key)+1);
    });
    
    $('#mec_add_fee_button').on('click', function()
    {
        var key = $('#mec_new_fee_key').val();
        var html = $('#mec_new_fee_raw').html().replace(/:i:/g, key);
        
        $('#mec_fees_list').append(html);
        $('#mec_new_fee_key').val(parseInt(key)+1);
    });
});

function mec_location_toggle()
{
    if(jQuery('#mec_location_id').val() != '0') jQuery('#mec_location_new_container').hide();
    else jQuery('#mec_location_new_container').show();
}

function mec_organizer_toggle()
{
    if(jQuery('#mec_organizer_id').val() != '0') jQuery('#mec_organizer_new_container').hide();
    else jQuery('#mec_organizer_new_container').show();
}

function mec_repeat_toggle()
{
    if(jQuery('#mec_repeat').is(':checked')) jQuery('.mec-form-repeating-event-row').show();
    else jQuery('.mec-form-repeating-event-row').hide();
}

function mec_repeat_type_toggle()
{
    var repeat_type = jQuery('#mec_repeat_type').val();
    
    if(repeat_type == 'certain_weekdays')
    {
        jQuery('#mec_repeat_interval_container').hide();
        jQuery('#mec_repeat_certain_weekdays_container').show();
        jQuery('#mec_exceptions_in_days_container').hide();
    }
    else if(repeat_type == 'custom_days')
    {
        jQuery('#mec_repeat_interval_container').hide();
        jQuery('#mec_repeat_certain_weekdays_container').hide();
        jQuery('#mec_exceptions_in_days_container').show();
    }
    else if(repeat_type != 'daily' && repeat_type != 'weekly')
    {
        jQuery('#mec_repeat_interval_container').hide();
        jQuery('#mec_repeat_certain_weekdays_container').hide();
        jQuery('#mec_exceptions_in_days_container').hide();
    }
    else
    {
        jQuery('#mec_repeat_interval_container').show();
        jQuery('#mec_repeat_certain_weekdays_container').hide();
        jQuery('#mec_exceptions_in_days_container').hide();
    }
}

function mec_in_days_remove(i)
{
    jQuery('#mec_in_days_row'+i).remove();
}

function mec_not_in_days_remove(i)
{
    jQuery('#mec_not_in_days_row'+i).remove();
}

function mec_bookings_unlimited_toggle()
{
    jQuery('#mec_bookings_limit').toggleClass('mec-util-hidden');
}

function mec_hourly_schedule_remove(i)
{
    jQuery("#mec_hourly_schedule_row"+i).remove();
}

function mec_ticket_remove(i)
{
    jQuery("#mec_ticket_row"+i).remove();
}

function mec_set_event_color(color)
{
    try
    {
        jQuery("#mec_event_color").wpColorPicker('color', '#'+color);
    }
    catch(e)
    {
        jQuery("#mec_event_color").val(color);
    }
}

function mec_remove_fee(key)
{
    jQuery("#mec_fee_row"+key).remove();
}