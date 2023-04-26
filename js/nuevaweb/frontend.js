// MEC SEARCH FORM PLUGIN
(function($)
{
    $.fn.mecSearchForm = function(options)
    {
        // Default Options
        var settings = $.extend(
        {
            // These are the defaults.
            id: 0,
            search_form_element: '',
            atts: '',
            callback: function()
            {
            }
        }, options);
        
        $("#mec_sf_category_"+settings.id).on('change', function(e)
        {
            search();
        });
        
        $("#mec_sf_location_"+settings.id).on('change', function(e)
        {
            search();
        });
        
        $("#mec_sf_organizer_"+settings.id).on('change', function(e)
        {
            search();
        });
        
        $("#mec_sf_label_"+settings.id).on('change', function(e)
        {
            search();
        });
        
        $("#mec_sf_s_"+settings.id).on('change', function(e)
        {
            search();
        });

        $("#mec_sf_month_"+settings.id).on('change', function(e)
        {
            search();
        });

        $("#mec_sf_year_"+settings.id).on('change', function(e)
        {
            search();
        });
        
        function search()
        {
            var s = $("#mec_sf_s_"+settings.id).length ? $("#mec_sf_s_"+settings.id).val() : '';
            var category = $("#mec_sf_category_"+settings.id).length ? $("#mec_sf_category_"+settings.id).val() : '';
            var location = $("#mec_sf_location_"+settings.id).length ? $("#mec_sf_location_"+settings.id).val() : '';
            var organizer = $("#mec_sf_organizer_"+settings.id).length ? $("#mec_sf_organizer_"+settings.id).val() : '';
            var label = $("#mec_sf_label_"+settings.id).length ? $("#mec_sf_label_"+settings.id).val() : '';
            var month = $("#mec_sf_month_"+settings.id).length ? $("#mec_sf_month_"+settings.id).val() : '';
            var year = $("#mec_sf_year_"+settings.id).length ? $("#mec_sf_year_"+settings.id).val() : '';
            var skip_date = false;
            
            if ($("#mec_sf_month_"+settings.id).val() == 'ignor_date') {
                skip_date = true;
            }

            // Skip filter by date
            if(skip_date == true)
            {
                month = '';
                year = '';
            }
            
            var atts = settings.atts+'&sf[s]='+s+'&sf[month]='+month+'&sf[year]='+year+'&sf[category]='+category+'&sf[location]='+location+'&sf[organizer]='+organizer+'&sf[label]='+label;
            settings.callback(atts);
        }
    };
    
}(jQuery));

// MEC GOOGLE MAPS PLUGIN
(function($)
{
    $.fn.mecGoogleMaps = function(options)
    {
        // Default Options
        var settings = $.extend(
        {
            // These are the defaults.
            latitude: 0,
            longitude: 0,
            zoom: 14,
            icon: '../img/m-01.png',
            markers: {},
            sf: {},
            getDirection: 0,
            directionOptions:
            {
                form: '#mec_get_direction_form',
                reset: '.mec-map-get-direction-reset',
                addr: '#mec_get_direction_addr',
                destination: {},
            },
        }, options);
        
        // Search Widget
        if(settings.sf.container !== '')
        {
            $(settings.sf.container).mecSearchForm(
            {
                id: settings.id,
                atts: settings.atts,
                callback: function(atts)
                {
                    settings.atts = atts;
                    getMarkers();
                }
            });
        }
            
        // Create the options
        var bounds = new google.maps.LatLngBounds();
	var center = new google.maps.LatLng(settings.latitude, settings.longitude);
        
        var canvas = this;
        var DOM = canvas[0];
        
	var mapOptions = {
            scrollwheel: false,
            mapTypeId: google.maps.MapTypeId.ROADMAP,
            center: center,
            zoom: settings.zoom,
            styles: settings.styles,
	}
        
	// Init map
	var map = new google.maps.Map(DOM, mapOptions);
        
        // Init Infowindow
        var infowindow = new google.maps.InfoWindow(
        {
            pixelOffset: new google.maps.Size(0, -37)
        });
        
        var loadedMarkers = new Array();
        
        // Load Markers
        loadMarkers(settings.markers);
        
        // Initialize get direction feature
        if(settings.getDirection === 1) initSimpleGetDirection();
        else if(settings.getDirection === 2) initAdvancedGetDirection();
        
        function loadMarkers(markers)
        {
            var f = 0;
            for(var i in markers)
            {
                f++;

                var dataMarker = markers[i];

                var marker = new RichMarker(
                {
                    position: new google.maps.LatLng(dataMarker.latitude, dataMarker.longitude),
                    map: map,
                    event_ids: dataMarker.event_ids,
                    infowindow: dataMarker.infowindow,
                    lightbox: dataMarker.lightbox,
                    icon: (dataMarker.icon ? dataMarker.icon : settings.icon),
                    content: '<span class="mec-marker-wrap"><span class="mec-marker">'+dataMarker.count+'</span><span class="mec-marker-pulse-wrap"><span class="mec-marker-pulse"></span></span></span>',
                    shadow: 'none'
                });

                // Marker Info-Window
                google.maps.event.addListener(marker, 'mouseover', function(event)
                {
                    infowindow.close();
                    infowindow.setContent(this.infowindow);
                    infowindow.open(map, this);
                });

                // Marker Lightbox
                google.maps.event.addListener(marker, 'click', function(event)
                {
                    lity(this.lightbox);
                });

                // extend the bounds to include each marker's position
                bounds.extend(marker.position);
                
                // Added to Markers
                loadedMarkers.push(marker);
            }
            
            if(f > 1) map.fitBounds(bounds);

            // Set map center if only 1 marker found
            if(f === 1)
            {
                map.setCenter(new google.maps.LatLng(dataMarker.latitude, dataMarker.longitude));
            }
        }
        
        function getMarkers()
        {
            // Add loader
            $("#mec_googlemap_canvas"+settings.id).addClass("mec-loading");
            
            $.ajax(
            {
                url: settings.ajax_url,
                data: "action=mec_map_get_markers&"+settings.atts,
                dataType: "json",
                type: "post",
                success: function(response)
                {
                    // Remove Markers
                    removeMarkers();
                    
                    // Load Markers
                    loadMarkers(response.markers);
                    
                    // Remove loader
                    $("#mec_googlemap_canvas"+settings.id).removeClass("mec-loading");
                },
                error: function()
                {
                    // Remove loader
                    $("#mec_googlemap_canvas"+settings.id).removeClass("mec-loading");
                }
            });
        }
        
        function removeMarkers()
        {
            bounds = new google.maps.LatLngBounds();
            
            if(loadedMarkers)
            {
                for(i=0; i < loadedMarkers.length; i++) loadedMarkers[i].setMap(null);
                loadedMarkers.length = 0;
            }
        }
        
        var directionsDisplay;
        var directionsService;
        var startMarker;
        var endMarker;

        function initSimpleGetDirection()
        {
            $(settings.directionOptions.form).on('submit', function(event)
            {
                event.preventDefault();

                var from = $(settings.directionOptions.addr).val();
                var dest = new google.maps.LatLng(settings.directionOptions.destination.latitude, settings.directionOptions.destination.longitude);

                // Reset the direction
                if(typeof directionsDisplay !== 'undefined')
                {
                    directionsDisplay.setMap(null);
                    startMarker.setMap(null);
                    endMarker.setMap(null);
                }

                // Fade Google Maps canvas
                $(canvas).fadeTo(300, .4);

                directionsDisplay = new google.maps.DirectionsRenderer({suppressMarkers: true});
                directionsService = new google.maps.DirectionsService();

                var request = {
                    origin: from, 
                    destination: dest,
                    travelMode: google.maps.DirectionsTravelMode.DRIVING
                };

                directionsService.route(request, function(response, status)
                {
                    if(status === google.maps.DirectionsStatus.OK)
                    {
                        directionsDisplay.setDirections(response);
                        directionsDisplay.setMap(map);

                        var leg = response.routes[0].legs[0];
                        startMarker = new google.maps.Marker(
                        {
                            position: leg.start_location,
                            map: map,
                            icon: settings.directionOptions.startMarker,
                        });

                        endMarker = new google.maps.Marker(
                        {
                            position: leg.end_location,
                            map: map,
                            icon: settings.directionOptions.endMarker,
                        });
                    }

                    // Fade Google Maps canvas
                    $(canvas).fadeTo(300, 1);
                });

                // Show reset button
                $(settings.directionOptions.reset).removeClass('mec-util-hidden');
            });

            $(settings.directionOptions.reset).on('click', function(event)
            {
                $(settings.directionOptions.addr).val('');
                $(settings.directionOptions.form).submit();

                // Hide reset button
                $(settings.directionOptions.reset).addClass('mec-util-hidden');
            });
        }

        function initAdvancedGetDirection()
        {
            $(settings.directionOptions.form).on('submit', function(event)
            {
                event.preventDefault();

                var from = $(settings.directionOptions.addr).val();
                var url = 'https://maps.google.com/?saddr='+encodeURIComponent(from)+'&daddr='+settings.directionOptions.destination.latitude+','+settings.directionOptions.destination.longitude;

                window.open(url);
            });
        }
    };
    
}(jQuery));

// MEC FULL CALENDAR PLUGIN
(function($)
{
    $.fn.mecFullCalendar = function(options)
    {
        // Default Options
        var settings = $.extend(
        {
            // These are the defaults.
            id: 0,
            atts: '',
            ajax_url: '',
            sf: {},
            skin: '',
        }, options);
        
        // Set onclick Listeners
        setListeners();
        
        var sf;
        function setListeners()
        {
            // Search Widget
            if(settings.sf.container !== '')
            {
                sf = $(settings.sf.container).mecSearchForm(
                {
                    id: settings.id,
                    atts: settings.atts,
                    callback: function(atts)
                    {
                        settings.atts = atts;
                        search();
                    }
                });
            }
            
            // Add the onclick event
            $("#mec_skin_"+settings.id+" .mec-totalcal-box .mec-totalcal-view span").on('click', function(e)
            {
                e.preventDefault();
                var skin = $(this).data('skin');

                $(this).addClass('mec-totalcalview-selected').siblings().removeClass('mec-totalcalview-selected');
                
                loadSkin(skin);
            });
        }
        
        function loadSkin(skin)
        {
            // Set new Skin
            settings.skin = skin;
            
            // Add loader
            $("#mec_full_calendar_container_"+settings.id).addClass("mec-month-navigator-loading");
            
            $.ajax(
            {
                url: settings.ajax_url,
                data: "action=mec_full_calendar_switch_skin&skin="+skin+"&"+settings.atts+"&apply_sf_date=1",
                dataType: "json",
                type: "post",
                success: function(response)
                {
                    $("#mec_full_calendar_container_"+settings.id).html(response);
                    
                    // Remove loader
                    $("#mec_full_calendar_container_"+settings.id).removeClass("mec-month-navigator-loading");
                },
                error: function()
                {
                }
            });
        }
        
        function search()
        {
            // Add loader
            $("#mec_full_calendar_container_"+settings.id).addClass("mec-month-navigator-loading");
            
            $.ajax(
            {
                url: settings.ajax_url,
                data: "action=mec_full_calendar_switch_skin&skin="+settings.skin+"&"+settings.atts+"&apply_sf_date=1",
                dataType: "json",
                type: "post",
                success: function(response)
                {
                    $("#mec_full_calendar_container_"+settings.id).html(response);
                    
                    // Remove loader
                    $("#mec_full_calendar_container_"+settings.id).removeClass("mec-month-navigator-loading");
                },
                error: function()
                {
                }
            });
        }
    };
    
}(jQuery));

// MEC MONTHLY VIEW PLUGIN
(function($)
{
    $.fn.mecMonthlyView = function(options)
    {
        var active_month;
        var active_year;
        
        // Default Options
        var settings = $.extend(
        {
            // These are the defaults.
            today: null,
            id: 0,
            events_label: 'Events',
            event_label: 'Event',
            month_navigator: 0,
            atts: '',
            next_month: {},
            sf: {},
            ajax_url: '',
        }, options);

        // Initialize Month Navigator
        if(settings.month_navigator) initMonthNavigator();
        
        // Load Next Month in background
        setMonth(settings.next_month.year, settings.next_month.month, true);
        
        // Set onclick Listeners
        setListeners();
        
        // Search Widget
        if(settings.sf.container !== '')
        {
            sf = $(settings.sf.container).mecSearchForm(
            {
                id: settings.id,
                atts: settings.atts,
                callback: function(atts)
                {
                    settings.atts = atts;
                    search(active_year, active_month);
                }
            });
        }
        
        function initMonthNavigator()
        {
            // Remove the onclick event
            $("#mec_skin_"+settings.id+" .mec-load-month").off("click");

            // Add onclick event
            $("#mec_skin_"+settings.id+" .mec-load-month").on("click", function()
            {
                var year = $(this).data("mec-year");
                var month = $(this).data("mec-month");

                setMonth(year, month);
            });
        }
        
        function search(year, month)
        {
            // Add loading Class
            $("#mec_skin_"+settings.id+" .mec-month-container").addClass("mec-month-navigator-loading");
            $("#mec_skin_"+settings.id+" .mec-skin-monthly-view-events-container").addClass("mec-month-loading");
            
            $.ajax(
            {
                url: settings.ajax_url,
                data: "action=mec_monthly_view_load_month&mec_year="+year+"&mec_month="+month+"&"+settings.atts+"&apply_sf_date=1",
                dataType: "json",
                type: "post",
                success: function(response)
                {
                    active_month = response.current_month.month;
                    active_year = response.current_month.year;
            
                    // Append Month
                    $("#mec_skin_events_"+settings.id).html('<div class="mec-month-container" id="mec_monthly_view_month_'+settings.id+'_'+response.current_month.id+'" data-month-id="'+response.current_month.id+'">'+response.month+'</div>');

                    // Append Month Navigator
                    $("#mec_skin_"+settings.id+" .mec-skin-monthly-view-month-navigator-container").html('<div class="mec-month-navigator" id="mec_month_navigator_'+settings.id+'_'+response.current_month.id+'">'+response.navigator+'</div>');

                    // Append Events Side
                    $("#mec_skin_"+settings.id+" .mec-calendar-events-side").html('<div class="mec-month-side" id="mec_month_side_'+settings.id+'_'+response.current_month.id+'">'+response.events_side+'</div>');

                    // Re-initialize Month Navigator
                    initMonthNavigator();

                    // Set onclick Listeners
                    setListeners();

                    // Toggle Month
                    toggleMonth(response.current_month.id);

                    // Remove loading Class
                    $("#mec_skin_"+settings.id+" .mec-month-container").removeClass("mec-month-navigator-loading");
                    $("#mec_skin_"+settings.id+" .mec-skin-monthly-view-events-container").removeClass("mec-month-loading");
                },
                error: function()
                {
                }
            });
        }
        
        function setMonth(year, month, do_in_background)
        {
            if(typeof do_in_background === "undefined") do_in_background = false;
            var month_id = year+""+month;
            
            active_month = month;
            active_year = year;
            
            // Month exists so we just show it
            if($("#mec_monthly_view_month_"+settings.id+"_"+month_id).length)
            {
                // Toggle Month
                toggleMonth(month_id);
            }
            else
            {
                if(!do_in_background)
                {
                    // Add loading Class
                    $("#mec_skin_"+settings.id+" .mec-month-container").addClass("mec-month-navigator-loading");
                    $("#mec_skin_"+settings.id+" .mec-skin-monthly-view-events-container").addClass("mec-month-loading");
                }

                $.ajax(
                {
                    url: settings.ajax_url,
                    data: "action=mec_monthly_view_load_month&mec_year="+year+"&mec_month="+month+"&"+settings.atts+"&apply_sf_date=0",
                    dataType: "json",
                    type: "post",
                    success: function(response)
                    {
                        // Append Month
                        $("#mec_skin_events_"+settings.id).append('<div class="mec-month-container" id="mec_monthly_view_month_'+settings.id+'_'+response.current_month.id+'" data-month-id="'+response.current_month.id+'">'+response.month+'</div>');
                        
                        // Append Month Navigator
                        $("#mec_skin_"+settings.id+" .mec-skin-monthly-view-month-navigator-container").append('<div class="mec-month-navigator" id="mec_month_navigator_'+settings.id+'_'+response.current_month.id+'">'+response.navigator+'</div>');

                        // Append Events Side
                        $("#mec_skin_"+settings.id+" .mec-calendar-events-side").append('<div class="mec-month-side" id="mec_month_side_'+settings.id+'_'+response.current_month.id+'">'+response.events_side+'</div>');

                        // Re-initialize Month Navigator
                        initMonthNavigator();

                        // Set onclick Listeners
                        setListeners();

                        if(!do_in_background)
                        {
                            // Toggle Month
                            toggleMonth(response.current_month.id);

                            // Remove loading Class
                            $("#mec_skin_"+settings.id+" .mec-month-container").removeClass("mec-month-navigator-loading");
                            $("#mec_skin_"+settings.id+" .mec-skin-monthly-view-events-container").removeClass("mec-month-loading");
                            
                            // Set Month Filter values in search widget
                            $("#mec_sf_month_"+settings.id).val(month);
                            $("#mec_sf_year_"+settings.id).val(year);
                        }
                        else
                        {
                            $("#mec_monthly_view_month_"+settings.id+"_"+response.current_month.id).hide();
                            $("#mec_month_navigator_"+settings.id+"_"+response.current_month.id).hide();
                            $("#mec_month_side_"+settings.id+"_"+response.current_month.id).hide();
                        }
                    },
                    error: function()
                    {
                    }
                });
            }
        }

        function toggleMonth(month_id)
        {
            var active_month = $("#mec_skin_"+settings.id+" .mec-month-container-selected").data("month-id");
            var active_day = $("#mec_monthly_view_month_"+settings.id+"_"+active_month+" .mec-selected-day").data("day");

            if(active_day <= 9) active_day = "0"+active_day;

            // Toggle Month Navigator
            $("#mec_skin_"+settings.id+" .mec-month-navigator").hide();
            $("#mec_month_navigator_"+settings.id+"_"+month_id).show();

            // Toggle Month
            $("#mec_skin_"+settings.id+" .mec-month-container").hide();
            $("#mec_monthly_view_month_"+settings.id+"_"+month_id).show();

            // Add selected class
            $("#mec_skin_"+settings.id+" .mec-month-container").removeClass("mec-month-container-selected");
            $("#mec_monthly_view_month_"+settings.id+"_"+month_id).addClass("mec-month-container-selected");

            // Toggle Events Side
            $("#mec_skin_"+settings.id+" .mec-month-side").hide();
            $("#mec_month_side_"+settings.id+"_"+month_id).show();
        }
        
        var sf;
        function setListeners()
        {
            // Remove the onclick event
            $("#mec_skin_"+settings.id+" .mec-has-event").off("click");

            // Add the onclick event
            $("#mec_skin_"+settings.id+" .mec-has-event").on('click', function(e)
            {
                e.preventDefault();
                
                // define variables
                var $this = $(this), data_mec_cell = $this.data('mec-cell'), month_id = $this.data('month');

                $("#mec_monthly_view_month_"+settings.id+"_"+month_id+" .mec-calendar-day").removeClass('mec-selected-day');
                $this.addClass('mec-selected-day');

                $('#mec_month_side_'+settings.id+'_'+month_id+' .mec-calendar-events-sec:not([data-mec-cell=' + data_mec_cell + '])').slideUp();
                $('#mec_month_side_'+settings.id+'_'+month_id+' .mec-calendar-events-sec[data-mec-cell=' + data_mec_cell + ']').slideDown();

                $('#mec_monthly_view_month_'+settings.id+'_'+month_id+' .mec-calendar-events-sec:not([data-mec-cell=' + data_mec_cell + '])').slideUp();
                $('#mec_monthly_view_month_'+settings.id+'_'+month_id+' .mec-calendar-events-sec[data-mec-cell=' + data_mec_cell + ']').slideDown();
            });
        }
    };
    
}(jQuery));

// MEC WEEKLY VIEW PLUGIN
(function($)
{
    $.fn.mecWeeklyView = function(options)
    {
        var active_year;
        var active_month;
        var active_week;
        var active_week_number;
        
        // Default Options
        var settings = $.extend(
        {
            // These are the defaults.
            today: null,
            week: 1,
            id: 0,
            changeWeekElement: '.mec-load-week',
            month_navigator: 0,
            atts: '',
            ajax_url: '',
            sf: {}
        }, options);
        
        // Search Widget
        if(settings.sf.container !== '')
        {
            $(settings.sf.container).mecSearchForm(
            {
                id: settings.id,
                atts: settings.atts,
                callback: function(atts)
                {
                    settings.atts = atts;
                    search(active_year, active_month, active_week);
                }
            });
        }
            
        // Set The Week
        setThisWeek(settings.month_id+settings.week);
        
        // Set Listeners
        setListeners();
        
        // Initialize Month Navigator
        if(settings.month_navigator) initMonthNavigator(settings.month_id);
        
        function setListeners()
        {
            $(settings.changeWeekElement).on('click', function()
            {
                var week = $('#mec_skin_'+settings.id+' .mec-weekly-view-week-active').data('week-id');
                var max_weeks = $('#mec_skin_'+settings.id+' .mec-weekly-view-week-active').data('max-weeks');
                var new_week_number = active_week_number;
                
                if($(this).hasClass('mec-previous-month'))
                {
                    week = parseInt(week)-1;
                    new_week_number--;
                }
                else
                {
                    week = parseInt(week)+1;
                    new_week_number++;
                }
                
                if(new_week_number <= 1 || new_week_number >= max_weeks)
                {
                    // Disable Next/Previous Button
                    $(this).css({'opacity': .6, 'cursor': 'default'});
                    $(this).find('i').css({'opacity': .6, 'cursor': 'default'});
                }
                else
                {
                    // Enable Next/Previous Buttons
                    $('#mec_skin_'+settings.id+' .mec-load-week, #mec_skin_'+settings.id+' .mec-load-week i').css({'opacity': 1, 'cursor': 'pointer'});
                }
                
                // Week is not in valid range
                if(new_week_number === 0 || new_week_number > max_weeks)
                {
                }
                else
                {
                    setThisWeek(week);
                    
                    $('.mec-calendar-d-top').find('.mec-current-week').find('span').remove();
                    $('.mec-calendar-d-top').find('.mec-current-week').append('<span>'+new_week_number+'</span>');
                }
            });
        }
        
        function setThisWeek(week)
        {
            // Set week to active in week list
            $('#mec_skin_'+settings.id+' .mec-weekly-view-week').removeClass('mec-weekly-view-week-active');
            $('#mec_weekly_view_week_'+settings.id+'_'+week).addClass('mec-weekly-view-week-active');
            
            // Show related events
            $('#mec_skin_'+settings.id+' .mec-weekly-view-date-events').addClass('mec-util-hidden');
            $('.mec-weekly-view-week-'+settings.id+'-'+week).removeClass('mec-util-hidden');

            active_week = week;
            active_week_number = $('#mec_skin_'+settings.id+' .mec-weekly-view-week-active').data('week-number');
            
            if(active_week_number === 1)
            {
                // Disable Previous Button
                $('#mec_skin_'+settings.id+' .mec-previous-month').css({'opacity': .6, 'cursor': 'default'});
                $('#mec_skin_'+settings.id+' .mec-previous-month').find('i').css({'opacity': .6, 'cursor': 'default'});
            }
        }

        function initMonthNavigator(month_id)
        {
            $('#mec_month_navigator'+settings.id+'_'+month_id+' .mec-load-month').off('click');
            $('#mec_month_navigator'+settings.id+'_'+month_id+' .mec-load-month').on('click', function()
            {
                var year = $(this).data('mec-year');
                var month = $(this).data('mec-month');

                setMonth(year, month, active_week);
            });
        }
        
        function search(year, month, week)
        {
            var week_number = (String(week).slice(-1));

            // Add Loading Class
            $("#mec_skin_"+settings.id+" .mec-skin-weekly-view-events-container").addClass("mec-month-navigator-loading");

            $.ajax(
            {
                url: settings.ajax_url,
                data: "action=mec_weekly_view_load_month&mec_year="+year+"&mec_month="+month+"&mec_week="+week_number+"&"+settings.atts+"&apply_sf_date=1",
                dataType: "json",
                type: "post",
                success: function(response)
                {
                    // Remove Loading Class
                    $("#mec_skin_"+settings.id+" .mec-skin-weekly-view-events-container").removeClass("mec-month-navigator-loading");

                    // Append Month
                    $("#mec_skin_events_"+settings.id).html('<div class="mec-month-container" id="mec_weekly_view_month_'+settings.id+'_'+response.current_month.id+'">'+response.month+'</div>');

                    // Append Month Navigator
                    $("#mec_skin_"+settings.id+" .mec-skin-weekly-view-month-navigator-container").html('<div class="mec-month-navigator" id="mec_month_navigator'+settings.id+'_'+response.current_month.id+'">'+response.navigator+'</div>');

                    // Set Listeners
                    setListeners();

                    // Toggle Month
                    toggleMonth(response.current_month.id);

                    // Set active week
                    setThisWeek(response.week_id);
                },
                error: function()
                {
                }
            });
        }
        
        function setMonth(year, month, week)
        {
            var month_id = ''+year+month;
            var week_number = (String(week).slice(-1));
            
            active_month = month;
            active_year = year;

            // Month exists so we just show it
            if($("#mec_weekly_view_month_"+settings.id+"_"+month_id).length)
            {
                // Toggle Month
                toggleMonth(month_id);

                // Set active week
                setThisWeek(''+month_id+week_number);
            }
            else
            {
                // Add Loading Class
                $("#mec_skin_"+settings.id+" .mec-skin-weekly-view-events-container").addClass("mec-month-navigator-loading");

                $.ajax(
                {
                    url: settings.ajax_url,
                    data: "action=mec_weekly_view_load_month&mec_year="+year+"&mec_month="+month+"&mec_week="+week_number+"&"+settings.atts+"&apply_sf_date=0",
                    dataType: "json",
                    type: "post",
                    success: function(response)
                    {
                        // Remove Loading Class
                        $("#mec_skin_"+settings.id+" .mec-skin-weekly-view-events-container").removeClass("mec-month-navigator-loading");

                        // Append Month
                        $("#mec_skin_events_"+settings.id).append('<div class="mec-month-container" id="mec_weekly_view_month_'+settings.id+'_'+response.current_month.id+'">'+response.month+'</div>');

                        // Append Month Navigator
                        $("#mec_skin_"+settings.id+" .mec-skin-weekly-view-month-navigator-container").append('<div class="mec-month-navigator" id="mec_month_navigator'+settings.id+'_'+response.current_month.id+'">'+response.navigator+'</div>');

                        // Set Listeners
                        setListeners();

                        // Toggle Month
                        toggleMonth(response.current_month.id);

                        // Set active week
                        setThisWeek(response.week_id);
                        
                        // Set Month Filter values in search widget
                        $("#mec_sf_month_"+settings.id).val(month);
                        $("#mec_sf_year_"+settings.id).val(year);
                    },
                    error: function()
                    {
                    }
                });
            }
        }

        function toggleMonth(month_id)
        {
            // Show related events
            $('#mec_skin_'+settings.id+' .mec-month-container').addClass('mec-util-hidden');
            $('#mec_weekly_view_month_'+settings.id+'_'+month_id).removeClass('mec-util-hidden');

            $('#mec_skin_'+settings.id+' .mec-month-navigator').addClass('mec-util-hidden');
            $('#mec_month_navigator'+settings.id+'_'+month_id).removeClass('mec-util-hidden');

            // Initialize Month Navigator
            if(settings.month_navigator) initMonthNavigator(month_id);
        }
    };
    
}(jQuery));

// MEC DAILY VIEW PLUGIN
(function($)
{
    $.fn.mecDailyView = function(options)
    {
        var active_month;
        var active_year;
        var active_day;
        
        // Default Options
        var settings = $.extend(
        {
            // These are the defaults.
            today: null,
            id: 0,
            changeDayElement: '.mec-daily-view-day',
            events_label: 'Events',
            event_label: 'Event',
            month_navigator: 0,
            atts: '',
            ajax_url: '',
            sf: {},
        }, options);
        
        active_month = settings.month;
        active_year = settings.year;
        active_day = settings.day;
            
        // Set Today
        setToday(settings.today);

        // Set Listeners
        setListeners();

        // Initialize Month Navigator
        if(settings.month_navigator) initMonthNavigator(settings.month_id);
        
        // Initialize Days Slider
        initDaysSlider(settings.month_id);
        
        // Search Widget
        if(settings.sf.container !== '')
        {
            $(settings.sf.container).mecSearchForm(
            {
                id: settings.id,
                atts: settings.atts,
                callback: function(atts)
                {
                    settings.atts = atts;
                    search(active_year, active_month, active_day);
                }
            });
        }
        
        function setListeners()
        {
            $(settings.changeDayElement).on('click', function()
            {
                var today = $(this).data('day-id');
                setToday(today);
            });
        }

        var current_monthday;
        function setToday(today)
        {
            // For caring about 31st, 30th and 29th of some months
            if(!$('#mec_daily_view_day'+settings.id+'_'+today).length)
            {
                setToday(parseInt(today)-1);
                return false;
            }

            // Set day to active in day list
            $('.mec-daily-view-day').removeClass('mec-daily-view-day-active mec-color');
            $('#mec_daily_view_day'+settings.id+'_'+today).addClass('mec-daily-view-day-active mec-color');

            // Show related events
            $('.mec-daily-view-date-events').addClass('mec-util-hidden');
            $('#mec_daily_view_date_events'+settings.id+'_'+today).removeClass('mec-util-hidden');

            // Set today label
            var weekday = $('#mec_daily_view_day'+settings.id+'_'+today).data('day-weekday');
            var monthday = $('#mec_daily_view_day'+settings.id+'_'+today).data('day-monthday');
            var count = $('#mec_daily_view_day'+settings.id+'_'+today).data('events-count');
            var month_id = $('#mec_daily_view_day'+settings.id+'_'+today).data('month-id');

            $('#mec_today_container'+settings.id+'_'+month_id).html('<h2>'+monthday+'</h2><h3>'+weekday+'</h3><div class="mec-today-count">'+count+' '+(count > 1 ? settings.events_label : settings.event_label)+'</div>');

            if(monthday <= 9) current_monthday = '0'+monthday;
            else current_monthday = monthday;
        }

        function initMonthNavigator(month_id)
        {
            $('#mec_month_navigator'+settings.id+'_'+month_id+' .mec-load-month').off('click');
            $('#mec_month_navigator'+settings.id+'_'+month_id+' .mec-load-month').on('click', function()
            {
                var year = $(this).data('mec-year');
                var month = $(this).data('mec-month');

                setMonth(year, month, current_monthday);
            });
        }

        function initDaysSlider(month_id, day_id)
        {
            // Init Days slider
            var owl = $("#mec-owl-calendar-d-table-"+settings.id+"-"+month_id);
            owl.owlCarousel(
            {
                items : 22, //22 items above 1000px browser width
                itemsDesktop : [1000,19], //19 items between 1000px and 901px
                itemsDesktopSmall : [960,14], //14 betweem 960px and 768px
                itemsTablet: [767,7], //7 items between 767px and 480px
                itemsMobile : [479,4], //4 items between 479px and 0
                pagination : false
            });

            // Custom Navigation Events
            $("#mec_daily_view_month_"+settings.id+"_"+month_id+" .mec-table-d-next").click(function(e)
            {
                e.preventDefault();
                owl.trigger('owl.next');
            });

            $("#mec_daily_view_month_"+settings.id+"_"+month_id+" .mec-table-d-prev").click(function(e)
            {
                e.preventDefault();
                owl.trigger('owl.prev');
            });

            if(typeof day_id === 'undefined') day_id = $('.mec-daily-view-day-active').data('day-id');

            var today_str = day_id.toString().substring(6,8);
            var today_int = parseInt(today_str);

            owl.trigger('owl.goTo', [today_int]);
        }
        
        function search(year, month, day)
        {
            // Add Loading Class
            $("#mec_skin_"+settings.id+" .mec-skin-daily-view-events-container").addClass("mec-month-navigator-loading");

            $.ajax(
            {
                url: settings.ajax_url,
                data: "action=mec_daily_view_load_month&mec_year="+year+"&mec_month="+month+"&mec_day="+day+"&"+settings.atts+"&apply_sf_date=1",
                dataType: "json",
                type: "post",
                success: function(response)
                {
                    // Remove Loading Class
                    $("#mec_skin_"+settings.id+" .mec-skin-daily-view-events-container").removeClass("mec-month-navigator-loading");

                    // Append Month
                    $("#mec_skin_events_"+settings.id).html('<div class="mec-month-container" id="mec_daily_view_month_'+settings.id+'_'+response.current_month.id+'">'+response.month+'</div>');

                    // Append Month Navigator
                    $("#mec_skin_"+settings.id+" .mec-calendar-a-month.mec-clear").html('<div class="mec-month-navigator" id="mec_month_navigator'+settings.id+'_'+response.current_month.id+'">'+response.navigator+'</div>');

                    // Set Listeners
                    setListeners();
                    
                    active_year = response.current_month.year;
                    active_month = response.current_month.month;
                    active_day = '01';
                    
                    // Toggle Month
                    toggleMonth(response.current_month.id, ''+active_year+active_month+active_day);

                    // Set Today
                    setToday(''+active_year+active_month+active_day);
                },
                error: function()
                {
                }
            });
        }
        
        function setMonth(year, month, day)
        {
            var month_id = '' + year + month;
            
            active_month = month;
            active_year = year;
            active_day = day;
            
            // Month exists so we just show it
            if($("#mec_daily_view_month_"+settings.id+"_"+month_id).length)
            {
                // Toggle Month
                toggleMonth(month_id);

                // Set Today
                setToday(''+month_id+day);
            }
            else
            {
                // Add Loading Class
                $("#mec_skin_"+settings.id+" .mec-skin-daily-view-events-container").addClass("mec-month-navigator-loading");

                $.ajax(
                {
                    url: settings.ajax_url,
                    data: "action=mec_daily_view_load_month&mec_year="+year+"&mec_month="+month+"&mec_day="+day+"&"+settings.atts+"&apply_sf_date=0",
                    dataType: "json",
                    type: "post",
                    success: function(response)
                    {
                        // Remove Loading Class
                        $("#mec_skin_"+settings.id+" .mec-skin-daily-view-events-container").removeClass("mec-month-navigator-loading");

                        // Append Month
                        $("#mec_skin_events_"+settings.id).append('<div class="mec-month-container" id="mec_daily_view_month_'+settings.id+'_'+response.current_month.id+'">'+response.month+'</div>');

                        // Append Month Navigator
                        $("#mec_skin_"+settings.id+" .mec-calendar-a-month.mec-clear").append('<div class="mec-month-navigator" id="mec_month_navigator'+settings.id+'_'+response.current_month.id+'">'+response.navigator+'</div>');

                        // Set Listeners
                        setListeners();

                        // Toggle Month
                        toggleMonth(response.current_month.id, ''+year+month+'01');

                        // Set Today
                        setToday(''+year+month+'01');
                        
                        // Set Month Filter values in search widget
                        $("#mec_sf_month_"+settings.id).val(month);
                        $("#mec_sf_year_"+settings.id).val(year);
                    },
                    error: function()
                    {
                    }
                });
            }
        }

        function toggleMonth(month_id, day_id)
        {
            // Show related events
            $('#mec_skin_'+settings.id+' .mec-month-container').addClass('mec-util-hidden');
            $('#mec_daily_view_month_'+settings.id+'_'+month_id).removeClass('mec-util-hidden');

            $('#mec_skin_'+settings.id+' .mec-month-navigator').addClass('mec-util-hidden');
            $('#mec_month_navigator'+settings.id+'_'+month_id).removeClass('mec-util-hidden');

            // Initialize Month Navigator
            if(settings.month_navigator) initMonthNavigator(month_id);

            // Initialize Days Slider
            initDaysSlider(month_id, day_id);
        }
    };
    
}(jQuery));

// MEC LIST VIEW PLUGIN
(function($)
{
    $.fn.mecListView = function(options)
    {
        // Default Options
        var settings = $.extend(
        {
            // These are the defaults.
            id: 0,
            atts: '',
            ajax_url: '',
            sf: {},
            current_month_divider: '',
            end_date: '',
            offset: 0,
        }, options);
        
        // Set onclick Listeners
        setListeners();
        
        var sf;
        function setListeners()
        {
            // Search Widget
            if(settings.sf.container !== '')
            {
                sf = $(settings.sf.container).mecSearchForm(
                {
                    id: settings.id,
                    atts: settings.atts,
                    callback: function(atts)
                    {
                        settings.atts = atts;
                        search();
                    }
                });
            }
            
            $("#mec_skin_"+settings.id+" .mec-load-more-button").on("click", function()
            {
                loadMore();
            });
        }
        
        function loadMore()
        {
            // Add loading Class
            $("#mec_skin_"+settings.id+" .mec-load-more-button").addClass("mec-load-more-loading");

            $.ajax(
            {
                url: settings.ajax_url,
                data: "action=mec_list_load_more&mec_start_date="+settings.end_date+"&mec_offset="+settings.offset+"&"+settings.atts+"&current_month_divider="+settings.current_month_divider+"&apply_sf_date=0",
                dataType: "json",
                type: "post",
                success: function(response)
                {
                    if(response.count == "0")
                    {
                        // Remove loading Class
                        $("#mec_skin_"+settings.id+" .mec-load-more-button").removeClass("mec-load-more-loading");

                        // Hide load more button
                        $("#mec_skin_"+settings.id+" .mec-load-more-button").addClass("mec-util-hidden");
                    }
                    else
                    {
                        // Show load more button
                        $("#mec_skin_"+settings.id+" .mec-load-more-button").removeClass("mec-util-hidden");
                        
                        // Append Items
                        $("#mec_skin_events_"+settings.id).append(response.html);

                        // Remove loading Class
                        $("#mec_skin_"+settings.id+" .mec-load-more-button").removeClass("mec-load-more-loading");

                        // Update the variables
                        settings.end_date = response.end_date;
						settings.offset = response.offset;
                        settings.current_month_divider = response.current_month_divider;
                    }
                },
                error: function()
                {
                }
            });
        }
        
        function search()
        {
            // Hide no event message
            $("#mec_skin_no_events_"+settings.id).addClass("mec-util-hidden");
            
            // Add loading Class
            $("#mec_skin_"+settings.id).find('.mec-skin-list-events-container').addClass("mec-month-navigator-loading").end().find('.mec-load-more-wrap').hide();

            $.ajax(
            {
                url: settings.ajax_url,
                data: "action=mec_list_load_more&mec_start_date="+settings.start_date+"&"+settings.atts+"&current_month_divider=0&apply_sf_date=1",
                dataType: "json",
                type: "post",
                success: function(response)
                {
                    if(response.count == "0")
                    {
                        // Append Items
                        $("#mec_skin_events_"+settings.id).html('');
                        
                        // Remove loading Class
                        $("#mec_skin_"+settings.id).find('.mec-skin-list-events-container').removeClass("mec-month-navigator-loading").end().find('.mec-load-more-wrap').show();

                        // Hide it
                        $("#mec_skin_"+settings.id+" .mec-load-more-button").addClass("mec-util-hidden");
                        
                        // Show no event message
                        $("#mec_skin_no_events_"+settings.id).removeClass("mec-util-hidden");
                    }
                    else
                    {
                        // Append Items
                        $("#mec_skin_events_"+settings.id).html(response.html);

                        // Remove loading Class
                        $("#mec_skin_"+settings.id).find('.mec-skin-list-events-container').removeClass("mec-month-navigator-loading").end().find('.mec-load-more-wrap').show();

                        // Update the variables
                        settings.end_date = response.end_date;
						settings.offset = response.offset;
                        settings.current_month_divider = response.current_month_divider;
                    }
                },
                error: function()
                {
                }
            });
        }
    };
    
}(jQuery));

// MEC GRID VIEW PLUGIN
(function($)
{
    $.fn.mecGridView = function(options)
    {
        // Default Options
        var settings = $.extend(
        {
            // These are the defaults.
            id: 0,
            atts: '',
            ajax_url: '',
            sf: {},
            end_date: '',
            offset: 0,
            start_date: '',
        }, options);
        
        // Set onclick Listeners
        setListeners();
        
        var sf;
        function setListeners()
        {
            // Search Widget
            if(settings.sf.container !== '')
            {
                sf = $(settings.sf.container).mecSearchForm(
                {
                    id: settings.id,
                    atts: settings.atts,
                    callback: function(atts)
                    {
                        settings.atts = atts;
                        search();
                    }
                });
            }
            
            $("#mec_skin_"+settings.id+" .mec-load-more-button").on("click", function()
            {
                loadMore();
            });
        }
        
        function loadMore()
        {
            // Add loading Class
            $("#mec_skin_"+settings.id+" .mec-load-more-button").addClass("mec-load-more-loading");

            $.ajax(
            {
                url: settings.ajax_url,
                data: "action=mec_grid_load_more&mec_start_date="+settings.end_date+"&mec_offset="+settings.offset+"&"+settings.atts+"&apply_sf_date=0",
                dataType: "json",
                type: "post",
                success: function(response)
                {
                    if(response.count == "0")
                    {
                        // Remove loading Class
                        $("#mec_skin_"+settings.id+" .mec-load-more-button").removeClass("mec-load-more-loading");

                        // Hide load more button
                        $("#mec_skin_"+settings.id+" .mec-load-more-button").addClass("mec-util-hidden");
                    }
                    else
                    {
                        // Show load more button
                        $("#mec_skin_"+settings.id+" .mec-load-more-button").removeClass("mec-util-hidden");
                        
                        // Append Items
                        $("#mec_skin_events_"+settings.id).append(response.html);

                        // Remove loading Class
                        $("#mec_skin_"+settings.id+" .mec-load-more-button").removeClass("mec-load-more-loading");

                        // Update the variables
                        settings.end_date = response.end_date;
						settings.offset = response.offset;
                    }
                },
                error: function()
                {
                }
            });
        }
        
        function search()
        {
            // Hide no event message
            $("#mec_skin_no_events_"+settings.id).addClass("mec-util-hidden");
                        
            // Add loading Class
            $("#mec_skin_"+settings.id).find('.mec-skin-grid-events-container').addClass("mec-month-navigator-loading").end().find('.mec-load-more-wrap').hide();

            $.ajax(
            {
                url: settings.ajax_url,
                data: "action=mec_grid_load_more&mec_start_date="+settings.start_date+"&"+settings.atts+"&apply_sf_date=1",
                dataType: "json",
                type: "post",
                success: function(response)
                {
                    if(response.count == "0")
                    {
                        // Append Items
                        $("#mec_skin_events_"+settings.id).html('');
                        
                        // Remove loading Class
                        $("#mec_skin_"+settings.id).find('.mec-skin-grid-events-container').removeClass("mec-month-navigator-loading").end().find('.mec-load-more-wrap').show();

                        // Hide it
                        $("#mec_skin_"+settings.id+" .mec-load-more-button").addClass("mec-util-hidden");
                        
                        // Show no event message
                        $("#mec_skin_no_events_"+settings.id).removeClass("mec-util-hidden");
                    }
                    else
                    {
                        // Append Items
                        $("#mec_skin_events_"+settings.id).html(response.html);

                        // Remove loading Class
                        $("#mec_skin_"+settings.id).find('.mec-skin-grid-events-container').removeClass("mec-month-navigator-loading").end().find('.mec-load-more-wrap').show();

                        // Update the variables
                        settings.end_date = response.end_date;
						settings.offset = response.offset;
                    }
                },
                error: function()
                {
                }
            });
        }
    };
    
}(jQuery));

// MEC CAROUSEL VIEW PLUGIN
(function($)
{
    $.fn.mecCarouselView = function(options)
    {
        // Default Options
        var settings = $.extend(
        {
            // These are the defaults.
            id: 0,
            atts: '',
            ajax_url: '',
            sf: {},
            items: 3,
            style: 'type1',
            start_date: ''
        }, options);
        
        // Init Sliders
        initSlider();
        
        function initSlider()
        {
            if(settings.style === 'type1')
            {
                // Start carousel skin
                var owl = $("#mec_skin_"+settings.id+" .mec-event-carousel-type1 .owl-carousel");
                owl.owlCarousel(
                {
                    autoPlay: 3000, // Set AutoPlay to 3 seconds
                    items: settings.items,
                    itemsDesktop: [1199, 3],
                    itemsDesktopSmall: [979, 3],
                    autoplayHoverPause:true
                });
            }
            else
            {
                $("#mec_skin_"+settings.id+" .owl-carousel").owlCarousel(
                {
                    autoPlay: true,
                    items: settings.items,
                    pagination: false,
                    navigation: true,
                    autoplayHoverPause:true,
                    navigationText: ["<i class='mec-sl-arrow-left'></i>"," <i class='mec-sl-arrow-right'></i>"],
                });
            }
        }
    };
    
}(jQuery));

// MEC COUNTDOWN MODULE
(function($)
{
    $.fn.mecCountDown = function(options, callBack)
    {
        // Default Options
        var settings = $.extend(
        {
            // These are the defaults.
            date: null,
            format: null
        }, options);

        var callback = callBack;
        var selector = $(this);
        
        startCountdown();
        var interval = setInterval(startCountdown, 1000);
        
        function startCountdown()
        {
            var eventDate = Date.parse(settings.date) / 1000;
            var currentDate = Math.floor($.now() / 1000);

            if(eventDate <= currentDate)
            {
                callback.call(this);
                clearInterval(interval);
            }

            var seconds = eventDate - currentDate;

            var days = Math.floor(seconds / (60 * 60 * 24)); 
            seconds -= days * 60 * 60 * 24; 

            var hours = Math.floor(seconds / (60 * 60));
            seconds -= hours * 60 * 60; 

            var minutes = Math.floor(seconds / 60);
            seconds -= minutes * 60;
            
            if(days == 1) selector.find(".mec-timeRefDays").text(mecdata.day);
            else selector.find(".mec-timeRefDays").text(mecdata.days);
            
            if(hours == 1) selector.find(".mec-timeRefHours").text(mecdata.hour);
            else selector.find(".mec-timeRefHours").text(mecdata.hours);
            
            if(minutes == 1) selector.find(".mec-timeRefMinutes").text(mecdata.minute);
            else selector.find(".mec-timeRefMinutes").text(mecdata.minutes);
            
            if(seconds == 1) selector.find(".mec-timeRefSeconds").text(mecdata.second);
            else selector.find(".mec-timeRefSeconds").text(mecdata.seconds);

            if(settings.format === "on")
            {
                days = (String(days).length >= 2) ? days : "0" + days;
                hours = (String(hours).length >= 2) ? hours : "0" + hours;
                minutes = (String(minutes).length >= 2) ? minutes : "0" + minutes;
                seconds = (String(seconds).length >= 2) ? seconds : "0" + seconds;
            }

            if(!isNaN(eventDate))
            {
                selector.find(".mec-days").text(days);
                selector.find(".mec-hours").text(hours);
                selector.find(".mec-minutes").text(minutes);
                selector.find(".mec-seconds").text(seconds);
            }
            else
            {
                clearInterval(interval);
            }
        }
    };
    
}(jQuery));

(function($)
{
    $(document).ready(function()
    {
        // MEC WIDGET CAROUSEL
        $(".mec-widget .mec-event-grid-classic").owlCarousel(
        {
            singleItem: true,
            autoPlay: true,
            pagination: false,
            navigation: true,
            navigationText: ["<i class='mec-sl-angle-left'></i>","<i class='mec-sl-angle-right'></i>"],
            items: 1,
            autoHeight: true,
            responsiveClass: true,
        });
    });
})(jQuery);

function mec_gateway_selected(gateway_id)
{
    // Hide all gateway forms
    jQuery('.mec-book-form-gateway-checkout').addClass('mec-util-hidden');
    
    // Show selected gateway form
    jQuery('#mec_book_form_gateway_checkout'+gateway_id).removeClass('mec-util-hidden');
}

// TODO must be cleaned JS codes
(function($)
{
    $(document).ready(function()
    {
        // add mec-sm959 class if mec-wrap div size < 959
        (function()
        {
            // Optimisation: Store the references outside the event handler
            var $window	= $(window);

            function mec_wrap_resize()
            {
                var $mec_wrap = $('.mec-wrap'), mec_width = $mec_wrap.width();
                if(mec_width < 959)
                {
                    $mec_wrap.addClass('mec-sm959');
                }
                else
                {
                    $mec_wrap.removeClass('mec-sm959');
                }
            }

            // Execute on load
            mec_wrap_resize();

            // Bind event listener
            jQuery(window).bind('resize', function()
            {
                mec_wrap_resize();
            });
        })();

        // Fixed: social hover in iphone
        (function()
        {
            $('.mec-event-sharing-wrap').hover(function()
            {
                $(this).find('.mec-event-sharing').show(0);
            }, function()
            {
                $(this).find('.mec-event-sharing').hide(0);
            });
        })();

        // Register Booking Smooth Scroll
        (function()
        {
            $('a[href="#mec-events-meta-group-booking"]').click(function()
            {
                if(location.pathname.replace(/^\//,'') == this.pathname.replace(/^\//,'') && location.hostname == this.hostname)
                {
                    var target = $(this.hash);
                    target = target.length ? target : $('[name=' + this.hash.slice(1) +']');
                    
                    if(target.length)
                    {
                        var scrollTopVal = target.offset().top - 30;
                        
                        $('html, body').animate(
                        {
                                scrollTop: scrollTopVal
                        }, 600);
                        
                        return false;
                    }
                }
            });
        })();
    });
})(jQuery);
// parse a date in yyyy-mm-dd format
function parseDate(input) {
  var parts = input.split('-');
  // new Date(year, month [, day [, hours[, minutes[, seconds[, ms]]]]])
  return new Date(parts[0], parts[1]-1, parts[2]); // Note: months are 0-based
}