<?php
  foreach ($data as $row) {
    $points[] = array(
        'name' => $row['Site Name'],
        'x' => $row['X-coordinate'],
        'y' => $row['Y-coordinate'],
        'type' => $row['Technology Type'],
        'capacity' => $row['Installed Capacity (MWelec)']
    );
  }
?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8" />
    <title>Wind Farm Locations</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/ol@v7.4.0/ol.css">
    <script src="https://cdn.jsdelivr.net/npm/ol@v7.4.0/dist/ol.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/proj4js/2.6.1/proj4.js"></script>
  </head>
    <style>
        html,
        body,
        #map {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
        }
   </style>
  
  <body>
    <div id="map" class="map"></div>
  </body>
</html>

<script>
// Define the wind farm locations
const windFarmLocations = <?php echo json_encode($points); ?>;

// Proj4js definition for BNG (British National Grid) coordinate system
proj4.defs("EPSG:27700", "+proj=tmerc +lat_0=49 +lon_0=-2 +k=0.9996012717 +x_0=400000 +y_0=-100000 +ellps=airy +towgs84=446.448,-125.157,542.060,0.1502,0.2470,0.8421,-20.4894 +units=m +no_defs");


// Convert BNG coordinates to WGS84 (latitude and longitude) and adjust radius based on capacity
const windFarmFeatures = windFarmLocations.map(location => {
  const [ x, y, capacity ] = [parseInt(location.x), parseInt(location.y), parseInt(location.capacity)];
  const lonLat = proj4("EPSG:27700", "EPSG:4326", [x, y]);
  const [lon, lat] = lonLat;

  let c = 180;
  let color = { r: c, g: 0, b: 0 }

  if (location.type == "Wind Onshore") {
    color = { r: 0, g: c, b: 0 }
  } else if (location.type == "Wind Offshore") {
    color = { r: 0, g: c, b: 0 }
  } else if (location.type == "Solar Photovoltaics") {
    color = { r: c, g: c, b: 0 }
  } else if (location.type == "Small Hydro") {
    color = { r: 0, g: 0, b: c }
  } else if (location.type == "Large Hydro") {
    color = { r: 0, g: 0, b: c }
  }

  color = { r: 255, g: 0, b: 0 }

  let area = capacity * 2.0;
  const radius = Math.sqrt(area / Math.PI);
  const style = new ol.style.Style({
    image: new ol.style.Circle({
      radius: radius,
      fill: new ol.style.Fill({ color: `rgba(${color.r}, ${color.g}, ${color.b}, 0.5)` }),
      stroke: new ol.style.Stroke({ color: `rgb(${color.r}, ${color.g}, ${color.b})`, width: 1 })
    })
  });

  const feature = new ol.Feature(new ol.geom.Point(ol.proj.fromLonLat([lon, lat])));
  feature.setStyle(style);
  return feature;
});

// Create a vector source with the wind farm features
const vectorSource = new ol.source.Vector({
  features: windFarmFeatures
});

// Create a vector layer to display the wind farm locations
const vectorLayer = new ol.layer.Vector({
  source: vectorSource
});

// Create the map
const map = new ol.Map({
  target: "map",
  layers: [
    new ol.layer.Tile({
      source: new ol.source.OSM()
    }),
    vectorLayer
  ],
  view: new ol.View({
    center: ol.proj.fromLonLat([-4.2967, 52.4378]),
    zoom: 8.5
  })
});
</script>
