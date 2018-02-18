<!-- Этот блок кода нужно вставить в ту часть страницы, где вы хотите разместить карту (начало) -->
<div id="ymaps-map-id_136203750845753291536" style="width: 100%; height: 100%;"></div>
<!--<div style="width: 100%; text-align: right;"><a href="http://api.yandex.ru/maps/tools/constructor/index.xml" target="_blank" style="color: #1A3DC1; font: 13px Arial, Helvetica, sans-serif;">Создано с помощью инструментов Яндекс.Карт</a></div>
-->
<script type="text/javascript">
function fid_136203750845753291536(ymaps) {
    var map = new ymaps.Map("ymaps-map-id_136203750845753291536", {
        center: [37.62453745507812, 55.7766719780927],
        zoom: 10,
        type: "yandex#map"
    });
    map.controls
        .add("zoomControl")
        .add("mapTools")
        .add(new ymaps.control.TypeSelector(["yandex#map", "yandex#satellite", "yandex#hybrid", "yandex#publicMap"]));
};
</script>
<script type="text/javascript" src="http://api-maps.yandex.ru/2.0-stable/?lang=ru-RU&coordorder=longlat&load=package.full&wizard=constructor&onload=fid_136203750845753291536"></script>
<!-- Этот блок кода нужно вставить в ту часть страницы, где вы хотите разместить карту (конец) -->