<?php

namespace Iagofelicio\GeoMaps\Utils;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class Helper
{
    /**
     * Validate parameters and throw error exceptions if
     *
     * @return string|array
     */
    public static function validateTagParams($parameters)
    {
        $data = $parameters->toArray();

        if (isset($data['center']) && is_string($data['center'])) {
            $decoded = json_decode($data['center'], true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $data['center'] = $decoded;
            }
        }
        $colorRegex = '/^(' .
            '#[\da-f]{3}|' . // Hex 3
            '#[\da-f]{6}|' . // Hex 6
            'rgba\(((\d{1,2}|1\d\d|2([0-4]\d|5[0-5]))\s*,\s*){2}((\d{1,2}|1\d\d|2([0-4]\d|5[0-5]))\s*)(,\s*(0\.\d+|1))\)|' . // RGBA
            'hsla\(\s*((\d{1,2}|[1-2]\d{2}|3([0-5]\d|60)))\s*,\s*((\d{1,2}|100)\s*%)\s*,\s*((\d{1,2}|100)\s*%)(,\s*(0\.\d+|1))\)|' . // HSLA
            'rgb\(((\d{1,2}|1\d\d|2([0-4]\d|5[0-5]))\s*,\s*){2}((\d{1,2}|1\d\d|2([0-4]\d|5[0-5]))\s*)\)|' . // RGB
            'hsl\(\s*((\d{1,2}|[1-2]\d{2}|3([0-5]\d|60)))\s*,\s*((\d{1,2}|100)\s*%)\s*,\s*((\d{1,2}|100)\s*%)\)|' . // HSL
            'oklch\(\s*([\d.]+%?)\s+([\d.]+)\s+([\d.]+)(?:\s*\/\s*([\d.]+%?))?\s*\)|' . // OKLCH (With Alpha support)
            'var\(\s*(--[\w-]+)\s*\)|' . // CSS Variables
            '(black|silver|gray|white|maroon|red|purple|fuchsia|green|lime|olive|yellow|navy|blue|teal|aqua|aliceblue|antiquewhite|aqua|aquamarine|azure|beige|bisque|black|blanchedalmond|blue|blueviolet|brown|burlywood|cadetblue|chartreuse|chocolate|coral|cornflowerblue|cornsilk|crimson|cyan|darkblue|darkcyan|darkgoldenrod|darkgray|darkgreen|darkgrey|darkkhaki|darkmagenta|darkolivegreen|darkorange|darkorchid|darkred|darksalmon|darkseagreen|darkslateblue|darkslategray|darkslategrey|darkturquoise|darkviolet|deeppink|deepskyblue|dimgray|dimgrey|dodgerblue|firebrick|floralwhite|forestgreen|fuchsia|gainsboro|ghostwhite|gold|goldenrod|gray|green|greenyellow|grey|honeydew|hotpink|indianred|indigo|ivory|khaki|lavender|lavenderblush|lawngreen|lemonchiffon|lightblue|lightcoral|lightcyan|lightgoldenrodyellow|lightgray|lightgreen|lightgrey|lightpink|lightsalmon|lightseagreen|lightskyblue|lightslategray|lightslategrey|lightsteelblue|lightyellow|lime|limegreen|linen|magenta|maroon|mediumaquamarine|mediumblue|mediumorchid|mediumpurple|mediumseagreen|mediumslateblue|mediumspringgreen|mediumturquoise|mediumvioletred|midnightblue|mintcream|mistyrose|moccasin|navajowhite|navy|oldlace|olive|olivedrab|orange|orangered|orchid|palegoldenrod|palegreen|paleturquoise|palevioletred|papayawhip|peachpuff|peru|pink|plum|powderblue|purple|rebeccapurple|red|rosybrown|royalblue|saddlebrown|salmon|sandybrown|seagreen|seashell|sienna|silver|skyblue|slateblue|slategray|slategrey|snow|springgreen|steelblue|tan|teal|thistle|tomato|transparent|turquoise|violet|wheat|white|whitesmoke|yellow|yellowgreen)' . // Named Colors
        ')$/i';

        $validator = Validator::make($data, [
            'id' => ['nullable', 'max:30'],
            'icon' => ['nullable', 'string', 'max:20'],
            'color' => ['nullable', 'regex:'.$colorRegex],
            'strokeColor' => ['nullable', 'regex:'.$colorRegex],
            'strokeWidth' => ['nullable', 'integer', 'min:0', 'max:10'],
            'iconSize' => ['nullable', 'integer'],
            'width' => ['nullable', 'string', 'regex:/^\d+(px|%|rem|em|vh|vw)?$/'],
            'height' => ['nullable', 'string', 'regex:/^\d+(px|%|rem|em|vh|vw)?$/'],
            'colorScheme' => ['nullable', 'in:light,dark,auto,automatic,default'],
            'center' => ['nullable', 'array', 'size:2'],
            'center.0' => ['required_with:center','numeric'],
            'center.1' => ['required_with:center','numeric'],
            'lat' => ['nullable', 'numeric', 'min:-90', 'max:90'],
            'lon' => ['nullable', 'numeric', 'min:-180', 'max:180'],
            'zoom' => ['nullable', 'integer', 'min:0', 'max:20'],
            'maxZoom' => ['nullable', 'integer', 'min:0', 'max:20'],
            'popup' => ['nullable', 'boolean'],
            'url' => ['nullable', 'url:http,https'],
            'data' => ['nullable', 'json'],
            'text' => ['nullable', 'string'],
            'markers' => ['nullable', 'array'],
            'markers.*.lat' => ['required_with:markers', 'numeric', 'min:-90', 'max:90'],
            'markers.*.lon' => ['required_with:markers', 'numeric', 'min:-180', 'max:180'],
            'markers.*.iconSize' => ['nullable','numeric'],
            'markers.*.icon' => ['nullable','string', 'max:20'],
            'markers.*.color' => ['nullable','regex:'.$colorRegex],
            'markers.*.strokeWidth' => ['nullable','integer', 'min:0', 'max:10'],
            'markers.*.strokeColor' => ['nullable','regex:'.$colorRegex],
            'markers.*.text' => ['nullable','string'],
        ]);

        if ($validator->fails()) {
            throw ValidationException::withMessages($validator->errors()->toArray());
        }
        
        return $validator->validated();
    }

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
                function getPopup_'.$markerId.'(){
                    var template_'.$markerId.' = document.createElement(\'template\');
                    template_'.$markerId.'.innerHTML = \''.$text.'\'.trim(); 
                    return template_'.$markerId.'.content;
                }

                L.marker(['.$lat.', '.$lon.'], {
                    icon: icon_'.$id.'_'.$markerId.'
                }).addTo('.$identifier.').bindPopup(getPopup_'.$markerId.');
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

