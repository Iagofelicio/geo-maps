<?php

namespace Iagofelicio\GeoMaps\Utils;

class Helper
{


    /**
     * Build the proper code to enable popup on click
     *
     * @return string|array
     */
    public static function parsePopup($popup,$id,$identifier)
    {
        if($popup){
            return '
                var popup_'.$id.' = L.popup();
                function onMapClick(e) {
                    popup_'.$id.'
                        .setLatLng(e.latlng)
                        .setContent("You clicked the map at the following location:<br><br><b>Lat</b>: " + e.latlng.lat + "<br><b>Lon</b>: " + e.latlng.lng)
                        .openOn('.$identifier.');
                }
                '.$identifier.'.on(\'click\', onMapClick);
            ';
        } else {
            return '';
        }
    }

    /**
     * Build the proper code to force a color scheme or set it automatically
     *
     * @return string|array
     */
    public static function parseColorScheme($colorScheme)
    {
        if($colorScheme == "dark"){
            return '
                var colorScheme = \'map-tiles\';
            ';
        } elseif($colorScheme == "light"){
            return '
                var colorScheme = \'\';
            ';
        } else {
            return '
                if (window.matchMedia && window.matchMedia(\'(prefers-color-scheme: dark)\').matches) {
                    var colorScheme = \'map-tiles\';
                } else {
                    var colorScheme = \'\';
                }
            ';
        }
    }

    /**
     * Convert a list of markers params into a snippet leaflet string
     *
     * @return string|array
     */
    public static function parseMarkers($id,$lat,$lon,$text,$color,$iconSize,$icon,$strokeWidth,$strokeColor,$identifier,$popup,$markerId = '')
    {

        $icon = '
            var icon_'.$id.'_'.$markerId.' = L.divIcon({
                className: \'geo-maps-icon\',
                html:`<i data-lucide="'.$icon.'"></i>`,
                iconSize: ['.$iconSize.', '.$iconSize.'],
                iconAnchor: ['.(0.25*$iconSize).', '.(0.85*$iconSize).'],
                popupAnchor: ['.(0.25*$iconSize).', '.(-0.85*$iconSize).'],
                shadowSize: ['.$iconSize.', '.$iconSize.']
            });
        ';

        if($popup){
            $marker = '
                L.marker(['.$lat.', '.$lon.'], {
                    icon: icon_'.$id.'_'.$markerId.'
                }).addTo('.$identifier.').bindPopup(\''.$text.'\');
            ';
        } else {
            $marker = '
                L.marker(['.$lat.', '.$lon.'], {
                    icon: icon_'.$id.'_'.$markerId.'
                }).addTo('.$identifier.');
            ';
        }

        $lucide = '
            lucide.createIcons({
                attrs: {
                    \'stroke-width\': \''.$strokeWidth.'\',
                    stroke: \''.$strokeColor.'\',
                    width: \''.$iconSize.'\',
                    height: \''.$iconSize.'\',
                    fill: \''.$color.'\'
                }
            });
        ';
        return [$icon,$marker,$lucide];
    }

}

