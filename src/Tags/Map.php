<?php

namespace Iagofelicio\GeoMaps\Tags;

use Statamic\Tags\Tags;
use Statamic\Support\Str;
use Statamic\Assets\Asset;
use Iagofelicio\GeoMaps\Utils\Helper;
use Illuminate\Validation\ValidationException;

class Map extends Tags
{

    private $centerLat = 0;
    private $centerLon = 0;
    private $zoom = '1';
    private $maxZoom = 20;
    private $width = '500px';
    private $height = '500px';
    private $icon = 'map-pin';
    private $color = '#3591b3';
    private $text = '<b>Popup message here</b><br><br>You can use some HTML tags to customize the text.';
    private $iconSize = 42;
    private $strokeWidth = 1;
    private $strokeColor = '#c7f4fe';
    private $colorScheme = "auto";
    private $popup = true;

    /**
     * The {{ map:requirements }} tag.
     *
     * @return string|array
     */
    public function requirements()
    {
        return '
            <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
            <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
            <script src="https://unpkg.com/lucide@latest"></script>
            <style>
                :root { --map-tiles-filter: brightness(0.6) invert(1) contrast(3) hue-rotate(200deg) saturate(0.3) brightness(0.7);}
                @media (prefers-color-scheme: dark) { .map-tiles { filter:var(--map-tiles-filter, none); } }
                @media (prefers-color-scheme: light) { .map-tiles { filter:var(--map-tiles-filter, none); } }
                pre {outline: 1px solid #ccc; padding: 5px; margin: 5px; }
                .string { color: #009688; }
                .number { color: #673ab7; }
                .boolean { color: #00bcd4; }
                .null { color: #3f51b5; }
                .key { color: #626567; }
            </style>
        ';
    }

    /**
     * The {{ map }} tag.
     *
     * @return string|array
     */
    public function index()
    {
        $width = $this->params->get('width', $this->width);
        $height = $this->params->get('height', $this->height);
        $id = $this->params->get('id', 'id_'.mt_rand(1000,9999));
        $zoom = $this->params->get('zoom', $this->zoom);
        $center = $this->params->get('center', '['.$this->centerLat.', '.$this->centerLon.']');
        $maxZoom = $this->params->get('maxZoom', $this->maxZoom);

        $identifier = "map_".$id;
        $tiles = "tiles_".$id;

        $colorScheme = $this->params->get('colorScheme', $this->colorScheme);
        $colorSchemeString = Helper::parseColorScheme($colorScheme);

        $popup = $this->params->bool('popup', $this->popup);
        $popupString = Helper::parsePopup($popup,$id,$identifier);

        $code = '
            <div id="'.$id.'" style="width: '.$width.'; height: '.$height.';"></div>
            <script>
                '.$colorSchemeString.'
                const '.$identifier.' = L.map(\''.$id.'\').setView('.$center.', '.$zoom.');
                const '.$tiles.' = L.tileLayer(\'https://tile.openstreetmap.org/{z}/{x}/{y}.png\', {
                    maxZoom: '.$maxZoom.',
                    attribution: \'&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>\',
                    className: colorScheme
                }).addTo('.$identifier.');
                '.$popupString.'
            </script>
        ';
        return $code;
    }

    /**
     * The {{ map:marker }} tag.
     *
     * @return string|array
     */
    public function marker()
    {
        if($this->params->get('lat') == null || $this->params->get('lon') == null){
            throw ValidationException::withMessages(
                ['missing_info' => 'Missing lat (latitude) and/or lon (longitude) of marker']
            );
        }

        $width = $this->params->get('width', $this->width);
        $height = $this->params->get('height', $this->height);
        $id = $this->params->get('id', 'id_'.mt_rand(1000,9999));
        $maxZoom = $this->params->get('maxZoom', $this->maxZoom);

        $identifier = "map_$id";
        $tiles = "tiles_$id";

        $lat = $this->params->get('lat');
        $lon = $this->params->get('lon');
        $zoom = $this->params->get('zoom', 5);
        $center = $this->params->get('center', '['.$lat.', '.$lon.']');

        $text = $this->params->get('text', $this->text);
        $color = $this->params->get('color', $this->color);
        $iconSize = $this->params->get('iconSize', $this->iconSize);
        $icon = $this->params->get('icon', $this->icon);
        $strokeWidth = $this->params->get('strokeWidth', $this->strokeWidth);
        $strokeColor = $this->params->get('strokeColor', $this->strokeColor);

        $markersString = Helper::parseMarkers($id,$lat,$lon,$text,$color,$iconSize,$icon,$strokeWidth,$strokeColor,$identifier,$this->popup);

        $colorScheme = $this->params->get('colorScheme', $this->colorScheme);
        $colorSchemeString = Helper::parseColorScheme($colorScheme);


        $code = '
            <div id="'.$id.'" style="width: '.$width.'; height: '.$height.';"></div>
            <script>
                '.$colorSchemeString.'
                const '.$identifier.' = L.map(\''.$id.'\').setView('.$center.', '.$zoom.');
                const '.$tiles.' = L.tileLayer(\'https://tile.openstreetmap.org/{z}/{x}/{y}.png\', {
                    maxZoom: '.$maxZoom.',
                    attribution: \'&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>\',
                    className: colorScheme
                }).addTo('.$identifier.');
                '.$markersString[0].'
                '.$markersString[1].'
                '.$markersString[2].'

           </script>
        ';
        return $code;

    }

    /**
     * The {{ map:markers }} tag.
     *
     * @return string|array
     */
    public function markers()
    {
        $width = $this->params->get('width', $this->width);
        $height = $this->params->get('height', $this->height);
        $id = $this->params->get('id', 'id_'.mt_rand(1000,9999));
        $identifier = "map_$id";
        $tiles = "tiles_$id";
        $zoom = $this->params->get('zoom', $this->zoom);
        $center = $this->params->get('center', '['.$this->centerLat.', '.$this->centerLon.']');
        $maxZoom = $this->params->get('maxZoom', $this->maxZoom);

        $colorScheme = $this->params->get('colorScheme', $this->colorScheme);
        $colorSchemeString = Helper::parseColorScheme($colorScheme);

        $allMarkers = [];
        foreach($this->params as $key => $value){
            $text = $this->text;
            $color = $this->color;
            $iconSize = $this->iconSize;
            $icon = $this->icon;
            $strokeWidth = $this->strokeWidth;
            $strokeColor = $this->strokeColor;

            if(Str::contains($key,'marker_')){
                if(!Str::contains($value,'lat') || !Str::contains($value,'lon')){
                    throw ValidationException::withMessages(
                        ['missing_info' => "Missing lat (latitude) and/or lon (longitude) of $key"]
                    );
                }
                $markerTmpString = explode(',',$value);
                foreach($markerTmpString as $option){
                    $optionKey = (explode('=',$option))[0];
                    $optionValue = (explode('=',$option))[1];
                    match ($optionKey) {
                        'lat' => $lat = $optionValue,
                        'lon' => $lon = $optionValue,
                        'text' => $text = $optionValue,
                        'color' => $color = $optionValue,
                        'iconSize' => $iconSize = $optionValue,
                        'icon' => $icon = $optionValue,
                        'strokeWidth' => $strokeWidth = $optionValue,
                        'strokeColor' => $strokeColor = $optionValue,
                    };
                }
                $snippet = Helper::parseMarkers($id,$lat,$lon,$text,$color,$iconSize,$icon,$strokeWidth,$strokeColor,$identifier,$this->popup,$key);
                $allMarkers[] = implode('',$snippet);
            }
        }
        $markersString = implode('', $allMarkers);

        $code = '
            <div id="'.$id.'" style="width: '.$width.'; height: '.$height.';"></div>
            <script>
                '.$colorSchemeString.'
                const '.$identifier.' = L.map(\''.$id.'\').setView('.$center.', '.$zoom.');
                const '.$tiles.' = L.tileLayer(\'https://tile.openstreetmap.org/{z}/{x}/{y}.png\', {
                    maxZoom: '.$maxZoom.',
                    attribution: \'&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>\',
                    className: colorScheme
                }).addTo('.$identifier.');
                '.$markersString.'
            </script>
        ';
        return $code;
    }

    /**
     * The {{ map:geojson }} tag.
     *
     * @return string|array
     */
    public function geojson()
    {
        $width = $this->params->get('width', $this->width);
        $height = $this->params->get('height', $this->height);
        $id = $this->params->get('id', 'id_'.mt_rand(1000,9999));
        $zoom = $this->params->get('zoom', $this->zoom);
        $center = $this->params->get('center', '['.$this->centerLat.', '.$this->centerLon.']');
        $maxZoom = $this->params->get('maxZoom', $this->maxZoom);
        $identifier = "map_".$id;
        $tiles = "tiles_".$id;

        $url = $this->params->get('url');
        $data = $this->params->get('data');

        $colorScheme = $this->params->get('colorScheme', $this->colorScheme);
        $colorSchemeString = Helper::parseColorScheme($colorScheme);

        $popup_jsonPrettier = '
           function syntaxHighlight(json) {
                json = json.replace(/&/g, \'&amp;\').replace(/</g, \'&lt;\').replace(/>/g, \'&gt;\');
                return json.replace(/("(\\u[a-zA-Z0-9]{4}|\\[^u]|[^\\"])*"(\s*:)?|\b(true|false|null)\b|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?)/g, function (match) {
                    var cls = \'number\';
                    if (/^"/.test(match)) {
                        if (/:$/.test(match)) {
                            cls = \'key\';
                        } else {
                            cls = \'string\';
                        }
                    } else if (/true|false/.test(match)) {
                        cls = \'boolean\';
                    } else if (/null/.test(match)) {
                        cls = \'null\';
                    }
                    return \'<span class="\' + cls + \'">\' + match + \'</span>\';
                });
            }
        ';

        $popUp_table = '
            function generateTableHTML(dataObject) {
                const dataContent_'.$id.' = dataObject;
                let tableHTML = `
                    <table style="border-collapse: collapse; width: 100%;">
                        <tr>
                            <th style="border: 1px solid #ededed; padding: 3px 8px 3px 8px;">Properties</th>
                            <th style="border: 1px solid #ededed; padding: 3px 8px 3px 8px;">Value</th>
                        </tr>
                `;
                for (const [key, value] of Object.entries(dataContent_'.$id.')) {
                    tableHTML += `
                        <tr>
                            <td style="border: 1px solid #ededed; padding: 3px 8px 3px 8px;">${key}</td>
                            <td style="border: 1px solid #ededed; padding: 3px 8px 3px 8px;">${
                                typeof value === \'object\' ? syntaxHighlight(JSON.stringify(value, undefined, 2)) : value
                            }</td>
                        </tr>
                    `;
                }

                // Close the table
                tableHTML += `</table>`;

                return tableHTML;
            }
        ';

        $popUp_feature = '
            function onEachFeature_'.$id.'(feature, layer) {
                if (feature.properties) {
                    layer.bindPopup(generateTableHTML(feature.properties));
                }
            }
        ';

        if(isset($url)){
            $code = '
                <div id="'.$id.'" style="width: '.$width.'; height: '.$height.';"></div>
                <script>
                    '.$popup_jsonPrettier.'
                    '.$popUp_table.'
                    '.$popUp_feature.'
                    fetch(\''.$url.'\')
                        .then(response => response.json())
                        .then(geojson_'.$id.' => {
                            '.$colorSchemeString.'
                            const '.$identifier.' = L.map(\''.$id.'\').setView('.$center.', '.$zoom.');
                            const '.$tiles.' = L.tileLayer(\'https://tile.openstreetmap.org/{z}/{x}/{y}.png\', {
                                maxZoom: '.$maxZoom.',
                                attribution: \'&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>\',
                                className: colorScheme
                            }).addTo('.$identifier.');
                            L.geoJSON(geojson_'.$id.',{
                                onEachFeature: onEachFeature_'.$id.'
                            }).addTo('.$identifier.');
                        })
                        .catch(error => {
                            console.error(\'Error fetching GeoJSON:\', error);
                            const '.$identifier.' = L.map(\''.$id.'\').setView('.$center.', '.$zoom.');
                            const '.$tiles.' = L.tileLayer(\'https://tile.openstreetmap.org/{z}/{x}/{y}.png\', {
                                maxZoom: '.$maxZoom.',
                                attribution: \'&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>\',
                                className: colorScheme
                            }).addTo('.$identifier.');
                        });
                </script>
            ';
        } elseif(isset($data)){
            $code = '
                <div id="'.$id.'" style="width: '.$width.'; height: '.$height.';"></div>
                <script>
                    '.$popup_jsonPrettier.'
                    '.$popUp_table.'
                    '.$popUp_feature.'
                    var geojson_'.$id.' = '.$data.';
                    '.$colorSchemeString.'
                    const '.$identifier.' = L.map(\''.$id.'\').setView('.$center.', '.$zoom.');
                    const '.$tiles.' = L.tileLayer(\'https://tile.openstreetmap.org/{z}/{x}/{y}.png\', {
                        maxZoom: '.$maxZoom.',
                        attribution: \'&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>\',
                        className: colorScheme
                    }).addTo('.$identifier.');
                    L.geoJSON(geojson_'.$id.',{
                        onEachFeature: onEachFeature_'.$id.'
                    }).addTo('.$identifier.');
                </script>
            ';
        } else {
            $code = '
                <div id="'.$id.'" style="width: '.$width.'; height: '.$height.';"></div>
                <script>
                    '.$colorSchemeString.'
                    const '.$identifier.' = L.map(\''.$id.'\').setView('.$center.', '.$zoom.');
                    const '.$tiles.' = L.tileLayer(\'https://tile.openstreetmap.org/{z}/{x}/{y}.png\', {
                        maxZoom: '.$maxZoom.',
                        attribution: \'&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>\',
                        className: colorScheme
                    }).addTo('.$identifier.');
                </script>
            ';
        }

        return $code;
    }

}
