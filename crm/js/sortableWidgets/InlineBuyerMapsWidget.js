/*********************************************************************************
 * Copyright (C) 2011-2014 X2Engine Inc. All Rights Reserved.
 *
 * X2Engine Inc.
 * P.O. Box 66752
 * Scotts Valley, California 95067 USA
 *
 * Company website: http://www.x2engine.com
 * Community and support website: http://www.x2community.com
 *
 * X2Engine Inc. grants you a perpetual, non-exclusive, non-transferable license
 * to install and use this Software for your internal business purposes.
 * You shall not modify, distribute, license or sublicense the Software.
 * Title, ownership, and all intellectual property rights in the Software belong
 * exclusively to X2Engine.
 *
 * THIS SOFTWARE IS PROVIDED "AS IS" AND WITHOUT WARRANTIES OF ANY KIND, EITHER
 * EXPRESS OR IMPLIED, INCLUDING WITHOUT LIMITATION THE IMPLIED WARRANTIES OF
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE, TITLE, AND NON-INFRINGEMENT.
 ********************************************************************************/

var primary_ponit;
var markersArray = [];

function clearOverlays() {
    for (var i = 0; i < markersArray.length; i++ ) {
        markersArray[i].setMap(null);
    }
    markersArray.length = 0;
}
//check if values filled in for filter are correct
function checkFilterConditions()
{
    address = $('#map_c_listing_address_c').val();
    radius = $('#map_radius').val();
    askingprice_start = $('#map_c_listing_askingprice_c_start').val();
    askingprice_end = $('#map_c_listing_askingprice_c_end').val();
    ownerscashflow_start = $('#map_c_ownerscashflow_start').val();
    ownerscashflow_end = $('#map_c_ownerscashflow_end').val();
    downpayment_start = $('#map_c_listing_downpayment_c_start').val();
    downpayment_end = $('#map_c_listing_downpayment_c_end').val();
    business_type = $('#map_business_type').val();

    if(typeof address == 'undefined')
    {
        address = '';
    }
    if(typeof radius == 'undefined')
    {
        radius = '';
    }
    if(typeof askingprice_start == 'undefined')
    {
        askingprice_start = '';
    }
    if(typeof askingprice_end == 'undefined')
    {
        askingprice_end = '';
    }
    if(typeof ownerscashflow_start == 'undefined')
    {
        ownerscashflow_start = '';
    }
    if(typeof ownerscashflow_end == 'undefined')
    {
        ownerscashflow_end = '';
    }
    if(typeof downpayment_start == 'undefined')
    {
        downpayment_start = '';
    }
    if(typeof downpayment_end == 'undefined')
    {
        downpayment_end = '';
    }
    if(typeof business_type == 'undefined')
    {
        business_type = '';
    }

    //console.log('checkFilterConditions');
    //console.log('askingprice_start: ' + askingprice_start + ' - ' + askingprice_end);
    errorMsg = '';
    if (address == '')
    {
        errorMsg += "\nAddress is mandatory";
    }
    if (radius == '')
    {
        errorMsg += "\nRadius is mandatory";
    }
    if ( (askingprice_start != '' && askingprice_end == '') || (askingprice_start == '' && askingprice_end != '') )
    {
        errorMsg += "\nAsking Price need to be an interval. Please select both start / end fields";
    }
    else
    {
        if ( (askingprice_start != '' && askingprice_end != '') && (askingprice_start > askingprice_end) )
        {
            errorMsg += "\nInvalid Asking Price Interval";
        }
    }

    if ( (ownerscashflow_start != '' && ownerscashflow_end == '') || (ownerscashflow_start == '' && ownerscashflow_end != '') )
    {
        errorMsg += "\nOwners Cash Flow need to be an interval. Please select both start / end fields";
    }
    else
    {
        if ( (ownerscashflow_start != '' && ownerscashflow_end != '') && (ownerscashflow_start > ownerscashflow_end) )
        {
            errorMsg += "\nInvalid Owners Cash Flow Interval";
        }
    }

    if ( (downpayment_start != '' && downpayment_end == '') || (downpayment_start == '' && downpayment_end != '') )
    {
        errorMsg += "\nDown Payment need to be an interval. Please select both start / end fields";
    }
    else
    {
        if ( (downpayment_start != '' && downpayment_end != '') && (downpayment_start > downpayment_end) )
        {
            errorMsg += "\nInvalid Down Payment Interval";
        }
    }

    return errorMsg;

}
//match marker with filter values added by user (Address, Radius, Asking Price between, Owners Cash Flow between, Down Payment between, Business Categories)
function matchFilter(marker_position)
{
    //console.log(markersArray);
    //console.log(listingsArray);

    address = $('#map_c_listing_address_c').val();
    radius = $('#map_radius').val();
    askingprice_start = $('#map_c_listing_askingprice_c_start').val();
    askingprice_end = $('#map_c_listing_askingprice_c_end').val();
    ownerscashflow_start = $('#map_c_ownerscashflow_start').val();
    ownerscashflow_end = $('#map_c_ownerscashflow_end').val();
    downpayment_start = $('#map_c_listing_downpayment_c_start').val();
    downpayment_end = $('#map_c_listing_downpayment_c_end').val();
    business_type = $('#map_business_type').val();

    console.log(business_type);

    if(typeof address == 'undefined')
    {
        address = '';
    }
    if(typeof radius == 'undefined')
    {
        radius = '';
    }
    if(typeof askingprice_start == 'undefined')
    {
        askingprice_start = '';
    }
    if(typeof askingprice_end == 'undefined')
    {
        askingprice_end = '';
    }
    if(typeof ownerscashflow_start == 'undefined')
    {
        ownerscashflow_start = '';
    }
    if(typeof ownerscashflow_end == 'undefined')
    {
        ownerscashflow_end = '';
    }
    if(typeof downpayment_start == 'undefined')
    {
        downpayment_start = '';
    }
    if(typeof downpayment_end == 'undefined')
    {
        downpayment_end = '';
    }
    if(typeof business_type == 'undefined')
    {
        business_type = '';
    }

    //console.log('listingsArray');
    //console.log(marker_position);
    //console.log(listingsArray);
    markerDetails = listingsArray[marker_position];

    console.log('marker_position');
    //console.log(markerDetails);
    console.log(markerDetails.askingprice + ' :: ' + askingprice_start + ' - ' + askingprice_end);
    console.log(markerDetails.ownerscashflow + ' :: ' + ownerscashflow_start + ' - ' + ownerscashflow_end);
    console.log(markerDetails.downpayment + ' :: ' + downpayment_start + ' - ' + downpayment_end);

    is_match = true;
    if (askingprice_start != '' && askingprice_end != '')
    {
        if (parseFloat(markerDetails.askingprice) >= parseFloat(askingprice_start) && parseFloat(markerDetails.askingprice) <= parseFloat(askingprice_end))
        {
            is_match = true;
            console.log('IN askingprice interval');
        }
        else
        {
            is_match = false;
        }
    }
    if (ownerscashflow_start != '' && ownerscashflow_end != '')
    {
        if (parseFloat(markerDetails.ownerscashflow) >= parseFloat(ownerscashflow_start) && parseFloat(markerDetails.ownerscashflow) <= parseFloat(ownerscashflow_end))
        {
            is_match = true;
            console.log('IN ownerscashflow interval');
        }
        else
        {
            is_match = false;
        }
    }
    if (downpayment_start != '' && downpayment_end != '')
    {
        if (parseFloat(markerDetails.downpayment) >= parseFloat(downpayment_start) && parseFloat(markerDetails.downpayment) <= parseFloat(downpayment_end))
        {
            is_match = true;
            console.log('IN downpayment interval');
        }
        else
        {
            is_match = false;
        }
    }
    if (business_type != '')
    {
        console.log(business_type);
        console.log('markerDetails.business_type ' + markerDetails.business_type);
        // if (jQuery.inArray( business_type, markerDetails.business_type ) != -1)
        markerDetails_business_type = markerDetails.business_type;
        if (markerDetails_business_type === null)
        {
            markerDetails_business_type = '';
        }
        if (markerDetails_business_type != '')
        {
            if (markerDetails_business_type.indexOf(business_type) >= 0)
            {
                is_match = true;
                console.log(markerDetails.address + ' IN business_type interval');
            }
            else
            {
                is_match = false;
            }
        }
        else
        {
            is_match = false;
        }


    }
    console.log('is_match ');
    console.log(is_match);
    console.log('------ ');
    return is_match;
}
function listMarkers(markers,kmRadius, infoWindowContent, infoWindow, marker, i, markersArray)
{
    // Loop through our array of markers & place each one on the map
    for( i = 0; i < markers.length; i++ ) {

        (function(i) { // protects i in an immediately called function
            console.log('need to matchFilter');
            if (matchFilter(i))
            {
                console.log( ' IS matchFilter');
                $.getJSON('http://maps.googleapis.com/maps/api/geocode/json?address='+markers[i]+'&sensor=false', null, function (data) {
                    if (typeof data.results[0] == 'undefined')
                    {
                        return false;
                    }
                    var position = data.results[0].geometry.location;
                    // var latlng = new google.maps.LatLng(p.lat, p.lng);
                    console.log(markers[i]);

                    // console.log(primary_ponit);
                    // console.log(kmRadius);
                    ///  console.log(position);
                    // bounds.extend(position);

                    if (pointInCircle(position, kmRadius, primary_ponit))
                    {
                        //console.log(' inside radius');
                        //  console.log(markers[i]);
                        // console.log(position);
                        marker = new google.maps.Marker({
                            position: position, //it will place marker based on the addresses, which they will be translated as geolocations.
                            map: map,
                            title: markers[i]
                        });
                        console.log(infoWindowContent);
                        console.log(i);
                        console.log(infoWindowContent[i]);
                        // Allow each marker to have an info window
                        google.maps.event.addListener(marker, 'click', (function(marker, i) {
                            return function() {
                                infoWindow.setContent(infoWindowContent[i]);
                                infoWindow.open(map, marker);
                            }
                        })(marker, i));

                        markersArray.push(marker);

                        console.log( ' INSide radius');
                    }
                    else
                    {
                        console.log( ' OUTSide radius');
                    }

                    // Automatically center the map fitting all markers on the screen
                    // map.fitBounds(bounds);
                });
            }
            else
            {
                console.log( ' NOT matchFilter');
            }
        })(i);
    }
}
function initMap()
{
    geocoder = new google.maps.Geocoder();
    var latlng = new google.maps.LatLng(40.7127837, -74.00594130000002); //New York, NY, USA
    var mapOptions =
    {
        zoom: 8,
        center: latlng
    }
    map = new google.maps.Map(document.getElementById('buyer_map'), mapOptions);
    // codeAddress(address);//call the function
    $.ajax({
        url:"http://maps.googleapis.com/maps/api/geocode/json?address="+address+"&sensor=false",
        type: "POST",
        success:function(res){
            primary_ponit = res.results[0].geometry.location;
            map.setCenter(primary_ponit);//center the map over the result
            //place a marker at the location
            var marker = new google.maps.Marker(
                {
                    map: map,
                    position: primary_ponit,
                    title: address
                });

            google.maps.event.addListener(marker, 'click', (function(marker, i) {
                return function() {
                    infoWindow.setContent('<div class="info_content"><h3>'+address+'</h3></div>');
                    infoWindow.open(map, marker);
                }
            })(marker, i));

            markersArray.push(marker);
        }
    });
    var kmRadius = 1000;

    //var bounds = new google.maps.LatLngBounds();

    // Display multiple markers on a map
    var infoWindow = new google.maps.InfoWindow(), marker, i;

    listMarkers(markers, kmRadius, infoWindowContent, infoWindow, marker, i, markersArray);

    var onChangeHandler = function() {
        //check filter conditions, if are ok make the filter
        filterErrors = checkFilterConditions();
        console.log('filterErrors');
        console.log(filterErrors);
        if (filterErrors == '')
        {
            kmRadius = $("#map_radius").val();
            address = $("#map_c_listing_address_c").val();
            $.ajax({
                url:"http://maps.googleapis.com/maps/api/geocode/json?address="+address+"&sensor=false",
                type: "POST",
                success:function(res){
                    primary_ponit = res.results[0].geometry.location;
                    map.setCenter(primary_ponit);//center the map over the result
                    //place a marker at the location
                    var marker = new google.maps.Marker(
                        {
                            map: map,
                            position: primary_ponit,
                            title: address
                        });

                    google.maps.event.addListener(marker, 'click', (function(marker, i) {
                        return function() {
                            infoWindow.setContent('<div class="info_content"><h3>'+address+'</h3></div>');
                            infoWindow.open(map, marker);
                        }
                    })(marker, i));

                    markersArray.push(marker);

                    console.log('kmRadius ' + kmRadius);
                    console.log('address ' + address);
                    console.log('primary_ponit ');
                    console.log(primary_ponit);
                    clearOverlays();
                    listMarkers(markers, kmRadius, infoWindowContent, infoWindow, marker, i, markersArray);
                }
            });

        }
        else
        {
            alert(filterErrors);
        }


    };
    document.getElementById('map_filter_btn').addEventListener('click', onChangeHandler);

}

function codeAddress(address)
{
    geocoder.geocode( {address:address}, function(results, status)
    {
        if (status == google.maps.GeocoderStatus.OK)
        {
            map.setCenter(results[0].geometry.location);//center the map over the result
            //place a marker at the location
            var marker = new google.maps.Marker(
                {
                    map: map,
                    position: results[0].geometry.location,
                    title: address
                });
            primary_ponit = results[0].geometry.location;
        } else {
            alert('Geocode was not successful for the following reason: ' + status);
        }
    });
}

function pointInCircle(point, radius, center)
{
    /* console.log('pointInCircle');
     console.log(point);
     console.log(center);
     console.log(radius);
     */

    var latLngCenter = new google.maps.LatLng(center.lat, center.lng);
    var latLngPoint = new google.maps.LatLng(point.lat, point.lng);

    computeDistance = google.maps.geometry.spherical.computeDistanceBetween(latLngPoint, latLngCenter); //In metres
    computeDistanceKm = (computeDistance / 1000).toFixed(1);
    //0.000621371192 = number of miles in a meter
    computeDistanceMiles = (computeDistance * 0.000621371192).toFixed(1);

    //name="map_radius_unit"
    /*console.log('computeDistance');
     console.log(radius);
     console.log(computeDistance);
     console.log(computeDistanceKm);
     console.log(computeDistanceMiles);*/

    map_radius_unit = $('#map_radius_unit').val();
    if (map_radius_unit == 'Miles')
    {
        //console.log('check by Miles');
        return (parseFloat(computeDistanceMiles) <= parseFloat(radius));
    }
    else
    {
        //console.log('check by Km');
        return (parseFloat(computeDistanceKm) <= parseFloat(radius));
    }


}

if (typeof x2 === 'undefined') x2 = {};

x2.InlineBuyerMapsWidget = (function () {

function InlineBuyerMapsWidget (argsDict) {
    var defaultArgs = {
        hideFullHeader: true,
        DEBUG: x2.DEBUG && false,
        recordId: null,
        recordType: null,
        displayMode: null,
        height: null,
        ajaxGetModelAutocompleteUrl: '',
        defaultsByRelatedModelType: {}, // {<model type>: <dictionary of default attr values>}
        createUrls: {}, // {<model type>: <string>}
        dialogTitles: {}, // {<model type>: <string>}
        tooltips: {}, // {<model type>: <string>}
        hasUpdatePermissions: null,
        createRelationshipUrl: null,

        // used to determine which models the quick create button is displayed for
        modelsWhichSupportQuickCreate: []
    };
    this._relationshipsGridContainer$ = $('#buyerMaps-form');
    /* x2prostart */
    this._relationshipsGraph = null;
    this._inlineGraphContainer$ = $('#inline-buyerMaps-graph-container');
    this._inlineGraphViewButton$ = $('#inline-buyerMaps-graph-view-button');
    /* x2proend */
    this._gridViewButton$ = $('#porftolio-grid-view-button');
    this._form$ = $('#new-buyerMaps-form');
    this._relationshipManager = null;

    auxlib.applyArgs (this, defaultArgs, argsDict);

    GridViewWidget.call (this, argsDict);
}

InlineBuyerMapsWidget.prototype = auxlib.create (GridViewWidget.prototype);


/**
 * submits relationship create form via AJAX, performs validation
 */
InlineBuyerMapsWidget.prototype._submitCreateRelationshipForm = function () {
    var that = this;
    $('.clistings-error').removeClass ('error');
    $('.clistings-error').hide ();
    var error = false;

    //get all listings that was checkec
    var checkedListingsValues = $('#BuyerMaps_all_listings .checkbox-column-checkbox:checked').map(function() {
        return this.value;
    }).get();

    if (checkedListingsValues.length < 1) {
        that.DEBUG && console.log ('no listings selected');
        error = true;
    }
    if (error) {
        $('.clistings-error').addClass ('error');
        $('.clistings-error').show();
        return false;
    }
    that._form$.slideUp (200);

    $('#new-buyerMaps-form').append('<input type="hidden" name="checkedListingsValues" value="'+checkedListingsValues+'" />');

    $.ajax ({
        url: this.createRelationshipUrl,
        type: 'POST',
        data: $('#new-buyerMaps-form').serializeArray (),
        success: function (data) {
            $.fn.yiiGridView.update('buyerMaps-grid');
        }
    });
};



/**
 * Sets up create form submission button behavior
 */
InlineBuyerMapsWidget.prototype._setUpCreateFormSubmission = function () {
    var that = this;

    $('#add-buyerMaps-button').on('click', function () {
        //console.log('add-buyerMaps-button');
        that._submitCreateRelationshipForm ();
        return false;
    });
};

InlineBuyerMapsWidget.prototype._changeMode = function (mode) {
    var form$ = $('#buyerMaps-form');
    if (mode === 'simple') {
        form$.addClass ('simple-mode');
        form$.removeClass ('full-mode');
    } else {
        form$.removeClass ('simple-mode');
        form$.addClass ('full-mode');
    }
};

InlineBuyerMapsWidget.prototype._setUpModeSelection = function () {
    var that = this;
    this.element.find ('a.simple-mode, a.full-mode').click (function () {
        if ($(this).hasClass ('disabled-link')) return false;
        var newMode = $(this).hasClass ('simple-mode') ? 'simple' : 'full';
        that.setProperty ('mode', newMode);
        $(this).siblings ().removeClass ('disabled-link');
        $(this).addClass ('disabled-link');
        that._changeMode (newMode);
        return false;
    });
};


InlineBuyerMapsWidget.prototype._setUpNewRelationshipsForm = function () {
    var that = this;

    $('#new-buyerMaps-button').click (function () {

        $('#BuyerMaps_all_listings .checkbox-column-checkbox').prop('checked', false);

        if (that._form$.is (':visible')) {
            that._form$.slideUp (200);
        } else {
            that.contentContainer.attr ('style', '');
            that._form$.slideDown (200);
        }
    });

    //select all / check when a listing is clickec
    $('#clistings_buyer_gvCheckbox_all').change (function () {
        var status = $(this).is(":checked") ? true : false;
        $('#new-buyerMaps-form .buyer-checkbox-column-checkbox').prop('checked', status);
    });
    $('.buyer-checkbox-column-checkbox').change (function () {
        if ($(this).is(":checked")){
            var isAllChecked = 0;
            $(".buyer-checkbox-column-checkbox").each(function(){
                if(!this.checked)
                    isAllChecked = 1;
            })
            if(isAllChecked == 0){ $("#clistingsgridC_selectAllCheckbox").prop("checked", true); }
        }
        else {
            $("#clistingsgridC_selectAllCheckbox").prop("checked", false);
        }
    });

    //search/filter for table
    $('.search_input').keyup(function(e){
        if(e.keyCode == 13)
        {
            $.ajax ({
                url: yii.scriptUrl + '/relationships/viewInlineGraph',
                data: {
                    recordId: this.recordId,
                    recordType: this.recordType,
                    height: that.height
                },
                success: function (data) {
                    that._inlineGraphContainer$.html (data);
                    that._relationshipsGraph = x2.relationshipsGraph;
                    that._displayInlineGraph ();
                }
            });
        }
    });


    this._setUpCreateFormSubmission ();
};



InlineBuyerMapsWidget.prototype._init = function () {
    GridViewWidget.prototype._init.call (this);
    if (this.displayMode === 'grid') this.element.find ('.ui-resizable-handle').hide ();
    this._setUpPageSizeSelection ();
    this._setUpModeSelection ();
    /* x2prostart */

    if (this.hasUpdatePermissions) this._setUpNewRelationshipsForm ();
};


return InlineBuyerMapsWidget;

}) ();



