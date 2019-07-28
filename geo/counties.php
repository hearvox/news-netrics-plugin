<!DOCTYPE html>
<html>
  <head>
    <title>Counties</title>
    <meta name="viewport" content="initial-scale=1.0">
    <!-- https://news.pubmedia.us/wp-content/plugins/news-netrics/popnews/counties.html -->
    <meta charset="utf-8">
    <style>
        /* Always set the map height explicitly to define the size of the div
        * element that contains the map. */
        #map {
            height: 100%;
        }
        /* Optional: Makes the sample page fill the window. */
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        }

        html,
        body,
        #map {
            height: 98%; margin: 0;
            padding: 0;
            overflow: hidden;
        }

        .nicebox {
            position: absolute;
            font-family: "Roboto", "Arial", sans-serif;
            font-size: 1rem;
            z-index: 5;
            box-shadow: 0 4px 6px -4px #333;
            padding: 5px 10px;
            background: rgb(255,255,255);
            background: linear-gradient(to bottom,rgba(255,255,255,1) 0%,rgba(245,245,245,1) 100%);
            border: rgb(229, 229, 229) 1px solid;
        }


        #controls {
            top: 10px;
            left: 110px;
            width: 360px;
            height: 45px;
        }

        #data-box {
            top: 10px;
            left: 500px;
            height: 5rem;
            line-height: 1.6;
            display: none;
        }

        #census-variable {
            width: 360px;
            height: 20px;
        }

        #legend {
            display: flex;
            display: -webkit-box;
            padding-top: 7px
        }

        .color-key {
            background: linear-gradient(to right,
                hsl(5, 69%, 54%) 0%,
                hsl(29, 71%, 51%) 17%,
                hsl(54, 74%, 47%) 33%,
                hsl(78, 76%, 44%) 50%,
                hsl(102, 78%, 41%) 67%,
                hsl(127, 81%, 37%) 83%,
                hsl(151, 83%, 34%) 100%);
            flex: 1;
            -webkit-box-flex: 1;
            margin: 0 5px;
            text-align: left;
            font-size: 1.0em;
            line-height: 1.0em;
        }

        #data-value,
        #data-val-2 {
            font-size: 1.1em;
        }

        #data-label {
            font-size: 1.2em;
            font-weight: normal;
            padding-right: 10px;
        }

        /*#data-label:after { content: ':' }*/

        #data-caret {
            margin-left: -5px;
            display: none;
            font-size: 14px;
            width: 14px;
        }

        </style>

    </head>
    <body>

<div id="controls" class="nicebox">
    <div>
        <select id="census-variable">
            <option value="1">Total population</option>
            <option value="2">Population density</option>
            <!-- <option value="2">Land area</option> -->
        </select>
    </div>
    <div id="legend">
        <div id="census-min">min</div>
        <div class="color-key"><span id="data-caret">&#x25c6;</span></div>
        <div id="census-max">max</div>
    </div>
</div><!-- #controls -->
<div id="data-box" class="nicebox">
    <label id="data-label" for="data-value"></label>
    <div>Pop.: <span id="data-value"></span></div>
    <div>/sq.mi.: <span id="data-val-2"></span></div>
</div>

<div id="map"></div>



<pre id="log" style="clear: both; margin-top: 0; height: 1.5rem;">data</pre>

<?php
/*
[["GEO_ID","POP","DENSITY","GEONAME","state","county"],
["0500000US01001","55601","93.534505205","Autauga County, Alabama","01","001"],
]
*/

$states = netrics_get_state_terms();

foreach ( $states as $state ) {
    $counties = netrics_get_county_terms( $state->term_id );

    foreach ( $counties as $county ) {
        echo "{$county->term_id},{$county->count}";
    }
}

?>
<script async defer
src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCf1_AynFKX8-A4Xh1geGFZwq1kgUYAtZc&callback=initMap"></script>

<script>
var mapStyle = [{
    'stylers': [{'visibility': 'off'}]
}, {
    'featureType': 'landscape',
    'elementType': 'geometry',
    'stylers': [{'visibility': 'on'}, {'color': '#fcfcfc'}]
}, {
    'featureType': 'water',
    'elementType': 'geometry',
    'stylers': [{'visibility': 'on'}, {'color': '#bfd4ff'}]
}];
var map;
// var censusMin = Number.MAX_VALUE, censusMax = -Number.MAX_VALUE;
var censusMin = 88;
var censusMin = 10105518;

function initMap() {
    // load the map
    map = new google.maps.Map(document.getElementById('map'), {
        center: {lat: 40, lng: -100},
        zoom: 5,
        styles: mapStyle
    });

    // Set up the style rules and events for google.maps.Data
    map.data.setStyle(styleFeature);
    map.data.addListener('mouseover', mouseInToRegion);
    map.data.addListener('mouseout', mouseOutOfRegion);

    // wire up the button
    var selectBox = document.getElementById('census-variable');
    google.maps.event.addDomListener(selectBox, 'change', function() {
        clearCensusData();
        loadCensusData(selectBox.options[selectBox.selectedIndex].value);
    });

    // County polygons only need to be loaded once, do them now
    loadMapShapes();
}

/** Loads the county boundary polygons from a GeoJSON source. */
function loadMapShapes() {
    // load US county outline polygons from a GeoJson file
    map.data.loadGeoJson(
        'https://news.pubmedia.us/wp-content/plugins/news-netrics/geo/us-counties-gz_2010_us_050_00_20m.json',
        { idPropertyName: 'GEO_ID' }
    );

    // wait for the request to complete by listening for the first feature to be added
    google.maps.event.addListenerOnce(map.data, 'addfeature', function() {
        google.maps.event.trigger(document.getElementById('census-variable'),
        'change');
    });

}

/**
 * Loads the census data from a simulated API call to the US Census API.
 *
 * @param {string} variable
 */
function loadCensusData(selection) {
    // load the requested variable from the census API (using local copies)
    var xhr = new XMLHttpRequest();
    xhr.open(
        'GET',
        'https://news.pubmedia.us/wp-content/plugins/news-netrics/geo/census-population-counties.json'
    );

    xhr.onload = function() {
        var censusData = JSON.parse(xhr.responseText);
        censusData.shift(); // the first row contains column names.
        document.getElementById('log').textContent = censusData;

        var log = '';

        censusData.forEach(function(row) {
            var censusVariable = parseFloat(row[selection]);
            var countyId = row[0];

            // console.log(countyId + row[3]);
            // document.getElementById('log').innerHTML = countyId + "\t" + row[3];

            // keep track of min and max values
            if (censusVariable < censusMin) {
                censusMin = censusVariable;
            }

            if (censusVariable > censusMax) {
                censusMax = censusVariable;
            }

            // update the existing row with the new data
            map.data.getFeatureById(countyId).setProperty('census_variable', censusVariable)
            map.data.getFeatureById(countyId).setProperty('census_name', row[3]);
            map.data.getFeatureById(countyId).setProperty('pop_total', row[1]);
            map.data.getFeatureById(countyId).setProperty('pop_density', row[2]);

        });

        // update and display the legend
        document.getElementById('census-min').textContent = censusMin.toLocaleString();
        document.getElementById('census-max').textContent = censusMax.toLocaleString();
    };

    xhr.send();
}

/** Removes census data from each shape on the map and resets the UI. */
function clearCensusData() {
    censusMin = Number.MAX_VALUE;
    censusMax = -Number.MAX_VALUE;

    map.data.forEach(function(row) {
        row.setProperty('census_variable', undefined);
    });

    document.getElementById('data-box').style.display = 'none';
    document.getElementById('data-caret').style.display = 'none';
}

/**
 * Applies a gradient style based on the 'census_variable' column.
 * This is the callback passed to data.setStyle() and is called for each row in
 * the data set.  Check out the docs for Data.StylingFunction.
 *
 * @param {google.maps.Data.Feature} feature
 */
function styleFeature(feature) {
/*
    var low = [5, 69, 54];  // color of smallest datum
    var high = [151, 83, 34];   // color of largest datum

    // delta represents where the value sits between the min and max
    // var delta = (feature.getProperty('census_variable') - censusMin) / (censusMax - censusMin);
    var delta = (feature.getProperty('census_variable') - censusMin) / (censusMax - censusMin);

    var color = [];
    for (var i = 0; i < 3; i++) {
        // calculate an integer color based on the delta
        color[i] = (high[i] - low[i]) * delta + low[i];
    }
*/

    var selectBox = document.getElementById('census-variable');
    var selection = selectBox.options[selectBox.selectedIndex].value;

    var level_high = 40000;
    var level_mid  = 4000;

    // Quartiles (approx.)å
    var level_1 = 9000;
    var level_2 = 19000;
    var level_3 = 37000;
    var level_4 = 93000;

    if ( selection == 2 ) {
        level_high = 30;
        level_mid  = 3;
    }

    // Color based on pop.
    var pop   = feature.getProperty('census_variable');
    var color = [5, 69, 54];

    if ( pop > level_high ) {
        color = [151, 83, 34];
    } else if ( pop > level_mid ) {
        color = [54, 75, 60];
    }

    // determine whether to show this shape or not
    var showRow = true;
    if (feature.getProperty('census_variable') == null || isNaN(feature.getProperty('census_variable'))) {
        // showRow = false;
    }

    var outlineWeight = 0.5, zIndex = 1;
    if (feature.getProperty('county') === 'hover') {
        outlineWeight = zIndex = 2;
    }

    // document.getElementById('log').innerHTML = censusMax + "\t" + pop + "\t" + 'hsl(' + color[0] + ',' + color[1] + '%,' + color[2] + '%)' + "\t" +censusMin;

    return {
        strokeWeight: outlineWeight,
        strokeColor: '#fff',
        zIndex: zIndex,
        fillColor: 'hsl(' + color[0] + ',' + color[1] + '%,' + color[2] + '%)',
        fillOpacity: 0.75,
        visible: showRow
    };
}

/**
 * Responds to the mouse-in event on a map shape (county).
 *
 * @param {?google.maps.MouseEvent} e
 */
function mouseInToRegion(e) {
    // set the hover county so the setStyle function can change the border
    e.feature.setProperty('county', 'hover');

    var percent = (e.feature.getProperty('census_variable') - censusMin) / (censusMax - censusMin) * 100;

    // update the label
    pop_total   = parseFloat( e.feature.getProperty('pop_total') );
    pop_density = parseFloat( e.feature.getProperty('pop_density') ).toFixed(1);
    // document.getElementById('data-label').textContent = e.feature.getProperty('NAME');
    document.getElementById('data-label').textContent = e.feature.getProperty('census_name');
    // document.getElementById('data-value').textContent = e.feature.getProperty('census_variable').toLocaleString();
    document.getElementById('data-value').textContent = pop_total.toLocaleString();
    document.getElementById('data-val-2').textContent = pop_density.toLocaleString();
    document.getElementById('data-box').style.display = 'block';
    document.getElementById('data-caret').style.display = 'block';
    document.getElementById('data-caret').style.paddingLeft = percent + '%';
}

/**
 * Responds to the mouse-out event on a map shape (county).
 *
 * @param {?google.maps.MouseEvent} e
 */
function mouseOutOfRegion(e) {
    // reset the hover county, returning the border to normal
    e.feature.setProperty('county', 'normal');
}






/*

https://storage.googleapis.com/mapsdevsite/json/states.js
http://eric.clst.org/tech/usgeojson/
https://raw.githubusercontent.com/kjhealy/us-county/master/data/geojson/gz_2010_us_050_00_20m.json
https://news.pubmedia.us/wp-content/plugins/news-netrics/popnews/counties.html
https://api.census.gov/data/2018/pep/population?get=GEO_ID,POP,DENSITY,GEONAME&for=county:*

["8303","0.4862974641","Kusilvak Census Area, Alaska","02","158"],
["14309","6.834634765","Oglala Lakota County, South Dakota","46","102"],

[["GEO_ID","POP","DENSITY","GEONAME","state","county"],
["0500000US01001","55601","93.534505205","Autauga County, Alabama","01","001"],
]

$states = netrics_get_state_terms();

*/
</script>

    </body>
</html>
