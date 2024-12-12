# Geo Maps

> **Geo Maps** is a Statamic addon that empowers users to seamlessly embed dynamic, customizable maps into their pages using **tags**.
>
> It leverages the power of [Leaflet JS](https://leafletjs.com/), an open-source JavaScript library widely known for its simplicity, performance, and versatility in creating interactive maps. 
>
> Additionally, Geo Maps integrates [Lucide](https://lucide.dev/), an open-source icon library offering over 1000 high-quality vector (SVG) icons. Lucide enhances the package by providing visually appealing, scalable icons and symbols that complement the map displays. These icons are ideal for markers, ensuring an intuitive and aesthetically pleasing user experience.
>
> We recognize and deeply value the contributions of the open-source community, as projects like Leaflet JS and Lucide are essential for driving innovation and accessibility. By utilizing these robust tools, Geo Maps not only delivers advanced mapping features but also underscores the importance of collaboration and shared resources in creating modern, user-centric solutions.

## Features

This addon does:

- **Customizable Markers**: Easily add markers with personalized icons and popup messages to highlight specific locations.
- **Flexible Map Configuration**: Adjust map size, center, zoom level, and toggle between light or dark modes to suit your design needs.
- **GeoJSON Integration**: Display complex geospatial data effortlessly by loading GeoJSON from variables or external URLs.
- **Interactive Popups**: Enable users to click on the map to retrieve latitude and longitude information seamlessly (available in basic maps).

## How to Install

You can install this addon via Composer:

``` bash
composer require iagofelicio/geo-maps
```

## How to Use

Once installed, the Geo Maps addon integrates seamlessly into your Statamic project by providing a set of intuitive **tags**.

### Loading required libs

The ```map:requirements``` tag is a powerful utility provided by the Geo Maps addon that adds all the necessary libraries to your page. 

This includes linking the required Leaflet JS and Lucide assets, along with any additional stylesheets or scripts needed to ensure your maps function smoothly. 

By placing this tag in your template's or page's ```<head>``` section, you ensure that your maps have all the foundational components they need to render correctly and interactively.

Example usage: 

```html
<head>
    <!-- Other libs -->
    {{ map:requirements }}
</head>
```

### Creating maps

#### 1. Blank Map

The simplest way to add a map to your page is by using the ```{{ map }}``` tag. This single tag renders a blank map with default settings, providing a quick and easy starting point. By using this default setup, you can immediately display a functional map, which can later be customized with additional options and parameters described in the sections below.

The default options include:

- Predefined id, width and height
- Automatically adjusting the color scheme to light or dark mode based on user preferences.
- Predefined latitude and longitude values to mark the center of the map.
- A default zoom level and max zoom allowed.
- Enabled popups that display the latitude and longitude of the location where you click on the map, making it especially useful for discovering geospatial information during map development.

These options can be changed as in the example below:

```php
{{ map 
    id="yourCustomId" # Default: random id
    width="100%" # Default: 100%
    height="100%" # Default: 100%
    colorScheme="light" # Default: automatic. Options: light or dark
    popup="true" # Default: true
    center="[-23,-48]" # Must be in the format [lat,lon]. Default: [0,0]
    zoom="5" # Default: 1
    maxZoom="15" # Default: 20
}}
```

**Important**: Map IDs on the same page cannot be the same.

#### 2. Single Marker

To add a marker to the map you must use the method below. All parameters of a blank map are customizable with the exception of the popup option which is set to true when there are markers.

```php
{{ map:marker
    # Other map options ...
    lat="-23" # Required
    lon="-48" # Required
    zoom=7 # If not informed, it will be used "5"
    center="[-20,-30]" # If not informed, the lat and lon values will be used
    text="This is the message to be visualized when someone click the <b>popup</b>."
    icon="map-pin-house" # Default: map-pin. It can be any icon from Lucide
    color="#ed5d69" # Fill color. Default: #3591b3 
    strokeColor="#c7f4fe" # Outline color. Default: #c7f4fe
    strokeWidth="2" # Default: 1
    iconSize=35 # Default: 42 
}}
```

#### 3. Multiple Markers

You can add as many markers as you want. However, to add more than one, use the following method. (Note that the method is plural).

```php
{{ map:markers
    # Other map options ...
    center="[-22,-51]" # If not informed, the default will be used
    marker_1="icon=map-pin-house,lat=-30,lon=30,iconSize=48,strokeWidth=1,text=Message for marker 1"
    marker_2="color=green,lat=-26,lon=51,iconSize=52,strokeWidth=1,text=Message for <b>marker 2</b>"
}}
```
The key to adding multiple markers is to use the ```marker_``` prefix and specify all marker parameters separated by commas. As in the example above. You can omit some options if you want default values ​​to be used.


#### 4. GeoJSON

[GeoJSON](https://geojson.org/) is a format for encoding a variety of geographic data structures.

A working example of GeoJSON can be found in the files directory of this repository. 

[sample.geojson](files/sample.geojson)

There are two ways to pass GeoJSON content to the ```map:geojson``` method:

##### 4.1. Content

Suppose the page where you will insert the map has a variable with GeoJSON content or, as in the illustrative case below, a global variable stores content in GeoJSON format.

```php
{{ map:geojson
    center="[-15,-51]"
    zoom="7"
    data="{{globals:geojson}}"
}}
```
The above code is enough to insert the map including the desired content.

##### 4.2. URL

Another scenario is through URLs. Whether to a file on your own site or to an external URL, the tag will read the GeoJSON content and plot the map.

_Example 1_:

```php
{{ map:geojson
    center="[-15,-51]"
    zoom="7"
    url="{{ link to="/assets/sample.geojson" absolute="true" }}"
}}
```

_Example 2_:

```php
{{ map:geojson
    center="[-15,-51]"
    zoom="7"
    url="https://raw.githubusercontent.com/openlayers/openlayers/refs/heads/main/examples/data/geojson/polygon-samples.geojson"
}}
```

## Contributions

Do you have any ideas on how to improve this add-on? Create an issue on Github and I'll try to analyze all cases and suggestions as soon as possible.
