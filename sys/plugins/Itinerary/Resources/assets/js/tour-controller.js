(function($, s){
    $(document).ready(function() {
        window.TourController = {};
        TourController.data = JSON.parse(s("[name='tour_spots']").val());
        try {
            TourController.routes = JSON.parse(s("[name='tour_routes']").val());
        } catch(e) {
            TourController.routes = [];
        }
        if (!(Array.isArray(TourController.routes))) {
            TourController.routes = [];
        }
        TourController.current_day = 1;
        TourController.selected_spot = undefined;
        TourController.selected_geolocation = undefined;
        TourController.lang = {
            "Preview" : "プレビュー",
            "Latitude" : "緯度",
            "Longitude" : "経度",
            "Title" : "タイトル",
            "Description" : "説明",
            "Location" : "ロケーション",
            "Please select a location" : "場所を選択してください",
            "Please input a title" : "タイトルを入力してください",
            "No location result" : "ロケーション結果なし",
            "Day" : "日",
            "Hour" : "時間",
            "Minute" : "分",
            "Day Tour" : "日目",
            "Add a day" : "日付追加",
            "Routes calculation has not been completed. Would you save it anyway?" : "ルートの計算は完了していません。とにかく保存しますか？",
            "You haven't set locations. Would you save it anyway?" : "場所を設定していません。とにかく保存しますか？"
        };

        TourController.setLang = function(key, val) {
            if (typeof key == 'object') {
                Object.assign(TourController.lang, key);
            } else {
                if (key && val) {
                    TourControll.lang[key] = val;
                }
            }
            return TourController.lang;
        };
        TourController.translate = function(key) {
            if (TourController.lang[key]) {
                return TourController.lang[key];
            }else {
                if (key.length) {
                    return key.charAt(0).toUpperCase + key.slice(1);
                } else {
                    return key;
                }
            }
        }; var t = TourController.translate;
        TourController.bindNavItemEvents = function() {
            s('.nav-item .fa-close').off('click').on('click', function() {
                TourController.removeDay();
                TourController.emit();
            });
            s('.nav-link:not(#btnAddDay)').off('click').on('click', function() {
                var day = $(this).parent().data('day');
                TourController.showDay(day);
                TourController.emit();
            });
        };
        TourController.bindBlockSpotEvents = function() {
            s(".block-spot-new .block-spot-wrapper, .block-spot-predefined .block-spot-wrapper").off('click').on('click', function(e) {
                e.stopPropagation();
                s('.overlay').css('display', 'none');
                if ($(this).find('.overlay').length) {
                    $(this).find('.overlay').css('display', 'flex');
                }
                var $block = $(this).parents('.block-spot');
                TourController.selectBlockSpot($block);
            });
            $('body').off('click').on('click', function(e) {
                var $target = $(e.target);
                if ($target.parents('.block-spot-form').length
                    || $target.parents('#map_content').length
                    || $target.parents('#cdn-browser').length
                    || $target.parents('.browser-actions').length
                    || $target.parents('#modalPreview').length
                    || !(TourController.selected_spot)) {

                } else {
                    TourController.deselectBlockSpot();
                }
            });
            s('.btn-spot-preview').off('click').on('click', function() {
                $block = $(this).parents('.block-spot');
                $modal = s('#modalPreview');
                $modal.find('.modal-title').text('').text($block.find('.spot-title').text() || t('Preview'));
                $modal.find('.modal-body').html('')
                    .append(
                        $block.find('.spot-image').clone().find('img').length ?
                            $block.find('.spot-image').clone().find('img').attr('height', '192').css('max-width', '100%').parent() :
                            $block.find('.spot-image').clone().find('div').css('height', '192').parent())
                    .append($block.find('.spot-address').clone().addClass('text-center'))
                    .append(
                        $block.data('lat') && $block.data('lng') ?
                            "<p class='text-center mt-1'>(<strong>" + t('Latitude') + ": </strong>" + $block.data('lat').toFixed(3) + " <strong>" + t('Longitude') + ": </strong>" + $block.data('lng').toFixed(3) + ")</p>" :
                            ""
                    ).append($block.find('.spot-content').clone());
                $modal.modal('show');
            });
            s('.btn-add-spot').off('click').on('click', function() {
                $block = $(this).parents('.block-spot');
                TourController.addSpot(TourController.getSpotData($block));
                TourController.emit();
                setTimeout(function() {
                    TourController.deselectBlockSpot();
                }, 200);
            });
        };
        TourController.addNewDay = function() {
            var days = TourController.data.length;
            var new_day = days + 1;
            s(".nav-item .fa-close").remove();
            s("#btnAddDay").parent().before("<li class='nav-item' data-day='" + new_day + "'><a class='nav-link' data-toggle='tab'>" + new_day + "日目<i class='fa fa-close ml-3'></i></a></li>");
            s('#divTour .tour-day').removeClass('active');
            TourController.deselectBlockSpot();
            TourController.data.push([]);
            TourController.bindNavItemEvents();
            TourController.drawSidebar();
            TourController.showDay(new_day);
        };
        TourController.removeDay = function() {
            var days = TourController.data.length;
            if (days > 1) {
                TourController.data.pop();
                s('.nav-item:nth-last-of-type(2)').remove();
                if (s('.nav-item').length > 2) {
                    s('.nav-item:nth-last-of-type(2)').find('.nav-link').append("<i class='fa fa-close ml-3'></i>");
                }
                TourController.bindNavItemEvents();
                if (TourController.current_day > TourController.data.length) {
                    TourController.showDay(TourController.data.length);
                }
            }
            TourController.drawSidebar();
        };
        TourController.defaultMapOption = {
            disableScripts: true,
            fitBounds: true,
            ready: function (engineMap) {
                var input = document.getElementById('pac_input');
                var searchBox = new google.maps.places.SearchBox(input);
                engineMap.map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);
                engineMap.map.addListener('bounds_changed', function() {
                    searchBox.setBounds(engineMap.map.getBounds());
                });
                searchBox.addListener('places_changed', function(){
                    var places = searchBox.getPlaces();
                    if (places.length == 0) {
                        return ;
                    }
                    var place = places[0];
                    var dataLatLng = [
                      place.geometry.location.lat(),
                      place.geometry.location.lng(),
                      engineMap.map.getZoom()
                    ];
                    TourController.selected_geolocation = {
                        map_lat: dataLatLng[0],
                        map_lng: dataLatLng[1],
                    };
                    engineMap.clearMarkers();
                    TourController.showDay(TourController.current_day);
                    engineMap.addMarker(dataLatLng, {
                        icon: {
                            url: place.icon,
                            size: new google.maps.Size(71, 71),
                            scaledSize: new google.maps.Size(32, 32)
                        },
                        title: place.name,
                        label: place.name
                    });
                    $("input[name=spot_map_lat]").val('').val(dataLatLng[0]);
                    $("input[name=spot_map_lng]").val('').val(dataLatLng[1]);
                    $("input[name=spot_title]").val('').val( place.name);
                    $("input[name=spot_address]").val('').val(place.formatted_address);
                    if (place.photos.length) {
                        var url = place.photos[0].getUrl();
                        var $form = $form = s('.block-spot-form');
                        $form.find("input[v-model=value]").val('');
                        if ($form.find('.attach-demo').find('img').length) {
                            $form.find('.attach-demo').find('img').attr('src', url);
                        } else {
                            $form.find('.attach-demo').html(`<img src="${url}">`)
                              .after(`<div class="upload-actions justify-content-between" v-show="value">
                                                <span></span>
                                                <a class="delete btn btn-sm btn-danger"><i class="fa fa-trash"></i></a>
                                    </div>`);
                        }
                        $form.find('.dungdt-upload-box').addClass('active');
                    }
                });
                engineMap.map.addListener('click', function (event) {
                    $("#pac_input").val('');
                    var dataLatLng = [
                        event.latLng.lat(),
                        event.latLng.lng(),
                        engineMap.map.getZoom()
                    ];
                    TourController.selected_geolocation = {
                        map_lat: dataLatLng[0],
                        map_lng: dataLatLng[1]
                    };
                    engineMap.clearMarkers();
                    new google.maps.Geocoder().geocode({
                        'latLng': event.latLng
                    }, function(results, status) {
                        if (status == google.maps.GeocoderStatus.OK) {
                            var result = results[1] ? results[1] : (result[0] ? result[0] : false);
                            if (result) {
                                var place_id = result.place_id;
                                var request = {
                                    placeId: place_id,
                                    fields: ['name', 'formatted_address', 'place_id', 'geometry', 'photos']
                                };
                                var service = new google.maps.places.PlacesService(engineMap.map);
                                service.getDetails(request, function(place, status) {
                                    if (status == google.maps.places.PlacesServiceStatus.OK) {
                                        TourController.deselectBlockSpot();
                                        var dataLatLng = [
                                          TourController.selected_geolocation.map_lat,
                                          TourController.selected_geolocation.map_lng,
                                          engineMap.map.getZoom()
                                        ];
                                        // TourController.selected_geolocation = {
                                        //     map_lat: dataLatLng[0],
                                        //     map_lng: dataLatLng[1],
                                        // };
                                        engineMap.clearMarkers();
                                        TourController.showDay(TourController.current_day);
                                        engineMap.addMarker(dataLatLng, {
                                            title: place.name,
                                            label: place.name
                                        });
                                        $("input[name=spot_map_lat]").val('').val(dataLatLng[0]);
                                        $("input[name=spot_map_lng]").val('').val(dataLatLng[1]);
                                        $("input[name=spot_title]").val('').val( place.name);
                                        $("input[name=spot_address]").val('').val(place.formatted_address);
                                        if (place.photos && place.photos.length) {
                                            var url = place.photos[0].getUrl();
                                            var $form = $form = s('.block-spot-form');
                                            $form.find("input[v-model=value]").val('');
                                            if ($form.find('.attach-demo').find('img').length) {
                                                $form.find('.attach-demo').find('img').attr('src', url);
                                            } else {
                                                $form.find('.attach-demo').html(`<img src="${url}">`)
                                                  .after(`<div class="upload-actions justify-content-between" v-show="value">
                                                <span></span>
                                                <a class="delete btn btn-sm btn-danger"><i class="fa fa-trash"></i></a>
                                    </div>`);
                                            }
                                            $form.find('.dungdt-upload-box').addClass('active');
                                        }
                                    }
                                });
                            }
                        }
                    });
                    TourController.showDay(TourController.current_day);
                    engineMap.addMarker(dataLatLng, {

                    });
                    $("input[name=spot_map_lat]").attr("value", dataLatLng[0]);
                    $("input[name=spot_map_lng]").attr("value", dataLatLng[1]);
                    TourController.emit();
                });

            }
        };
        TourController.addFunctions = function() {
            TourController.engineMap.directionService = new google.maps.DirectionsService();
            TourController.engineMap.directionRenderer = new google.maps.DirectionsRenderer();
            TourController.engineMap.directionRenderer.setMap(TourController.engineMap.map);
            TourController.engineMap.directionRenderer.setOptions({
                suppressMarkers: false,
                markerOptions: {
                    opacity: 0.3,
                    zIndex: 10
                }
            });
            TourController.engineMap.addMarker = function(latLng,options) {
                var markerOption = {
                    zIndex: 15,
                    position: {
                        lat:latLng[0],
                        lng:latLng[1]
                    },
                    map: this.map,
                };
                if(options.icon) {
                    markerOption.icon = options.icon;
                }
                if (options.title) {
                    markerOption.title = options.title;
                }
                if (options.label) {
                    markerOption.label = options.label;
                }
                if (options.zIndex !== undefined) {
                    markerOption.zIndex = options.zIndex;
                }
                var m = new google.maps.Marker(markerOption);
                m.addListener('click', function(e) {
                    var spots = TourController.data;
                    var spot = null;
                    var lat = e.latLng.lat();
                    var lng = e.latLng.lng();
                    for (var i=0; i<spots.length; i++) {
                        for (var j=0; j<spots[i].length; j++) {
                            if (lat == spots[i][j].map_lat && lng == spots[i][j].map_lng) {
                                spot = spots[i][j];
                            }
                        }
                    }
                    if (spot) {
                        var content = `<div class="gmap-info-window row">
                          <div class="col-5 p-1">
                          ${spot.image_url ? "<img src='" + spot.image_url + "' class='gmap-info-photo'>" : "<div style=\"width:100%; height:96px; background-color:#eee;\"></div>"}
                          </div>
                          <div class="col-7 p-1">
                          <h5 class="gmap-info-title">${spot.title || ''}</h5>
                          <p class="gmap-info-address">${spot.address || ''}</p>
                          </div>
                          <div class="col-12 mt-1"><p class="gmap-info-content">${spot.content || ''}</p></div>
                          </div>
                        </div>`;
                        var infowindow = new google.maps.InfoWindow({
                            width: 320,
                            maxWidth: 320,
                            content
                        });
                        infowindow.open(markerOption.map, m);
                    }
                });
                this.markers.push(m);

            };
            TourController.engineMap.showRoutes = function(arr) {
                var service = TourController.engineMap.directionService;
                var renderer = TourController.engineMap.directionRenderer;
                renderer.set('directions', null);
                var spots;
                if (TourController.selected_geolocation) {
                    spots = [...arr, TourController.selected_geolocation];
                } else {
                    spots = [...arr];
                }
                // if (spots.length < 2) return false;
                var waypoints = [];
                for (var i=1; i<spots.length-1; i++) {
                    var spot = spots[i];
                    waypoints.push({
                        location: new google.maps.LatLng(parseFloat(spot.map_lat), parseFloat(spot.map_lng)),
                        stopover: true
                    })
                }
                if (spots[0]) {
                    var request = {
                        origin: new google.maps.LatLng(spots[0].map_lat, spots[0].map_lng),
                        destination: new google.maps.LatLng(spots.getLastItem().map_lat, spots.getLastItem().map_lng),
                        waypoints,
                        optimizeWaypoints: false,
                        travelMode: google.maps.DirectionsTravelMode.DRIVING
                    };
                    service.route(request, function(response, status) {
                        if (status == google.maps.DirectionsStatus.OK) {
                            renderer.setDirections(response);
                            var routes = response.routes;
                            if (routes.length) {
                                routes = routes[0].legs;
                                TourController.routes[TourController.current_day - 1] = routes;
                            }
                            TourController.drawSidebar();

                        } else if (status == google.maps.DirectionsStatus.ZERO_RESULTS) {
                            toastr.warning(t('No location result'));
                        }
                    });
                }
            };
        };
        TourController.navigateMap = function(center, zoom) {
            var option = {center, zoom};
            Object.assign(option, TourController.defaultMapOption);
            TourController.engineMap = new BravoMapEngine('map_content', option);

            TourController.addFunctions();
        };

        var dayTour = TourController.data[TourController.current_day - 1];
        var center = averageGeoLocation(dayTour);
        var zoom = zoomLevelFromDistance(maxDistanceFromCenter(dayTour));
        var mapView = {};
        if (center && zoom) {
            TourController.navigateMap([center.map_lat, center.map_lng], zoom);
        } else {
            TourController.navigateMap([35.652832,139.839478], 5);
        }
        TourController.showDay = function(day, init = false) {
            var old_current_day = TourController.current_day;
            if (day && day > 0) {
                TourController.current_day = day;
            } else {
                day = TourController.current_day;
            }
            s('.nav-item').removeClass('active');
            s('.nav-item .nav-link').removeClass('active');
            s(".nav-item[data-day='" + TourController.current_day + "']")
                .find('.nav-link').addClass('active');
            s('#divTour .tour-day').removeClass('active');
            s('#divTour').find(`[data-day-order=${TourController.current_day - 1}]`).addClass('active');
            var dayTour = TourController.data[day - 1];

            if (dayTour) {
                TourController.engineMap.clearMarkers();
                if (old_current_day != TourController.current_day || init) {
                    var center = averageGeoLocation(dayTour);
                    var zoom = zoomLevelFromDistance(maxDistanceFromCenter(dayTour));
                    if (center && zoom) {
                        TourController.navigateMap([center.map_lat, center.map_lng], zoom);
                    }
                    TourController.selected_geolocation = undefined;
                }
                TourController.engineMap.showRoutes(dayTour);
                dayTour.forEach(function(spot, index) {
                    var label = `${index+1}`;
                    dayTour.forEach(function(other, key) {
                        if (index < key && spot.id == other.id) {
                            label = `${label}, ${key+1}`;
                        }
                        if (index > key && spot.id == other.id) {
                            label = '';
                        }
                    });
                    TourController.engineMap.addMarker([parseFloat(spot.map_lat), parseFloat(spot.map_lng)], {
                        label });
                });

            }
        };
        TourController.getSpotInputData = function() {
            $form = s('.block-spot-form');
            if ($form.data('id')) {
                var id = $form.data('id');
            } else {
                var temporary_id = 1;
                if ($form.data('temporary-id')) {
                    temporary_id = parseInt($form.data('temporary-id')) + 1;
                    if (isNaN(temporary_id)) {
                        temporary_id = 1;
                    }
                }
                var id =  "temp_" + temporary_id;
                $form.data('temporary-id', temporary_id);
            }
            var map_lat = $form.find('[name=spot_map_lat]').val();
            var map_lng = $form.find('[name=spot_map_lng]').val();
            var location_id = $form.data('location-id') || s('[name=location_id]').val();
            if (map_lat == '' || map_lng == '' ) {
                toastr.error(t('Please select a location'));
                return false;
            }
            if (!($form.find('[name=spot_title]').val())) {
                toastr.error(t('Please input a title'));
                return false;
            }
            var image_id = $form.find('[v-model=value]').val();
            var image_url = '';
            if ($form.find('.attach-demo').find('img').length) {
                if (image_id ) {
                    demo_url = $form.find('.attach-demo').find('img').attr('src');
                    if (demo_url) {
                        var ext = demo_url.slice(demo_url.lastIndexOf('.'));
                        if (demo_url.includes('-150')) {
                            image_url = demo_url.substring(0, demo_url.lastIndexOf('-150'));
                            image_url += ext;
                        } else if (demo_url.includes('.')) {
                            image_url = demo_url.substring(0, demo_url.lastIndexOf('.'));
                            image_url += ext;
                        } else {
                            image_url = demo_url;
                        }

                    }
                } else {
                    demo_url = $form.find('.attach-demo').find('img').attr('src');
                    if (demo_url) {
                        image_url = demo_url;
                    }
                }
            }
            return {
                id, map_lat, map_lng, location_id, image_url,
                title: $form.find('[name=spot_title]').val(),
                address: $form.find('[name=spot_address]').val(),
                content: $form.find('[name=spot_content]').val(),
                image_id: $form.find('[v-model=value]').val(),
            };
        };
        TourController.setSpotInputData = function(spot) {
            $form = s('.block-spot-form');
            var id = spot.id;
            $form.data('id', id);
            if (spot.image_id) {
                $form.find("input[v-model=value]").val('').val(spot.image_id);
                if (spot.image_url) {
                    var url = spot.image_url;
                    var ext = url.substring(url.lastIndexOf('.'));
                    var name = url.substring(0, url.lastIndexOf('.'));
                    url = `${name}-150${ext}`;
                    if ($form.find('.attach-demo').find('img').length) {
                        $form.find('.attach-demo').find('img').attr('src', url);
                    } else {
                        $form.find('.attach-demo').html(`<img src="${url}">`)
                            .after(`<div class="upload-actions justify-content-between" v-show="value">
                                                <span></span>
                                                <a class="delete btn btn-sm btn-danger"><i class="fa fa-trash"></i></a>
                                    </div>`);
                    }
                    $form.find('.dungdt-upload-box').addClass('active');
                }
            } else if (spot.image_url) {
                $form.find("input[v-model=value]").val('');
                url = spot.image_url;
                if ($form.find('.attach-demo').find('img').length) {
                    $form.find('.attach-demo').find('img').attr('src', url);
                } else {
                    $form.find('.attach-demo').html(`<img src="${url}">`)
                      .after(`<div class="upload-actions justify-content-between" v-show="value">
                                                <span></span>
                                                <a class="delete btn btn-sm btn-danger"><i class="fa fa-trash"></i></a>
                                    </div>`);
                }
                $form.find('.dungdt-upload-box').addClass('active');
            }
            $form.find("input[name=spot_title]").val('').val(spot.title);
            $form.find("input[name=spot_address]").val('').val(spot.address);
            $form.find("input[name=spot_map_lat]").val('').val(spot.map_lat);
            $form.find("input[name=spot_map_lng]").val('').val(spot.map_lng);
            $form.find("[name=spot_content]").val('').val(spot.content);
            $form.data('location-id', spot.location_id);
        };

        TourController.getBlockSpotHtml = function(data, clazz='new') {
            return `
                    <div class="block-spot block-spot-${clazz} col-md-2 col-sm-4" data-id="${data.id}" data-location="${data.location_id}" data-lat="${data.map_lat}" data-lng="${data.map_lng}" data-image-id="${data.image_id}">
                        <div class="block-spot-wrapper">
                            <div class="overlay">
                                <button type="button" class="btn btn-danger mr-1 btn-add-spot"><i class=" fa fa-plus"></i></button>
                                <button type="button" class="btn btn-success mr-1 btn-spot-preview"><i class=" fa fa-search"></i></button>
                                <button type="button" class="btn btn-dark btn-remove-spot-from-list"><i class="fa fa-remove"></i></button>
                            </div>
                            <div class="row">
                                <div class="col text-center spot-image">
                                ${data.image_url ? "<img src='" + data.image_url + "' height='96'>" : "<div style=\"width:100%; height:96px; background-color:#eee;\"></div>"}
                                </div>
                            </div>
                            <div class="row">
                                <div class="col text-center">
                                    <p class="mt-1 mb-0 spot-title"><strong>${data.title}</strong></p>
                                    <p class="mt-1 mb-0 spot-address"><i>${data.address}</i></p>
                                    <p class="mt-1 mb-0 text-left spot-content">${data.content}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    `;
        };
        TourController.getSpotData = function($block) {
            return {
                location_id: $block.data('location'),
                map_lat: $block.data('lat'),
                map_lng: $block.data('lng'),
                id: $block.data('id'),
                image_id: $block.data('image-id'),
                image_url: $block.find('.spot-image').find('img').length ? $block.find('.spot-image').find('img').attr('src') : undefined,
                title: $block.find('.spot-title').text(),
                address: $block.find('.spot-address').text(),
                content: $block.find('.spot-content').text()
            }
        };
        TourController.selectBlockSpot = function($block) {
            s('.block-spot .overlay').css('display', 'none');
            $block.find('.overlay').css('display', 'flex');
            TourController.setSpotInputData(TourController.getSpotData($block));
            TourController.selected_spot = $block.data('id');
        };
        TourController.deselectBlockSpot = function() {
            s('.block-spot .overlay').css('display', 'none');
            $form = s('.block-spot-form');
            $form.data('id', '')
                .data('location-id', '');
            $form.find("input[name=spot_title]").val('');
            $form.find("input[name=spot_address]").val('');
            $form.find("input[name=spot_map_lat]").val('');
            $form.find("input[name=spot_map_lng]").val('');
            $form.find("[name=spot_content]").val('');
            $form.data('location-id', $("[name=location_id]").val());
            $form.find("input[v-model=value]").val('');
            $form.find('.upload-actions').remove();
            $form.find(".dungdt-upload-box").removeClass('active');
            $form.find(".attach-demo").find('img').attr('src', '');
            TourController.selected_spot = undefined;
        };
        TourController.saveSpot = function(data) {
            if (data) {
                $block = $(`.block-spot[data-id='${data.id}']:not(.block-spot-form)`);

                if ($block.length) {
                    $block.before(TourController.getBlockSpotHtml(data));
                    $block.remove();
                    TourController.selectBlockSpot($(`.block-spot[data-id='${data.id}']`));
                } else {
                    var html = TourController.getBlockSpotHtml(data);

                    if ($('#panelSpots .block-spot-form').length) {
                        $('#panelSpots .block-spot-form').after(html);
                    } else {
                        var $html = $(html);
                        $html.find('.btn-remove-spot-from-list').remove();
                        s('#panelSpots .panel-body > .row').eq(0).prepend($html);
                    }
                }
                TourController.deselectBlockSpot();
                TourController.bindBlockSpotEvents();
                var spotData = TourController.data;
                for (var i=0; i<spotData.length; i++) {
                    for (var j=0; j<spotData[i].length; j++) {
                        if (data.id == spotData[i][j].id) {
                            spotData[i][j] = data;
                        }
                    }
                }
                TourController.drawSidebar();
                TourController.selected_geolocation = undefined;
            }
        };
        TourController.addSpot = function(spot) {
            var daySpots = TourController.data[this.current_day - 1];
            var lastSpot = daySpots ? daySpots.getLastItem() : false;
            if (lastSpot && lastSpot.map_lng == spot.map_lng && lastSpot.map_lat == spot.map_lat) {
                return false;
            } else {
                daySpots.push(spot);
            }
            TourController.drawSidebar();
            TourController.showDay();
            TourController.deselectBlockSpot();
        };
        TourController.removeSpot = function(dayOrder, visitOrder) {
            var dayTours = TourController.data[dayOrder];
            if (dayTours) {
                var spotBefore = dayTours[visitOrder - 1];
                var spotAfter = dayTours[visitOrder + 1];
                dayTours.remove(visitOrder);
                if (spotBefore && spotAfter && spotBefore.map_lat == spotAfter.map_lat && spotBefore.map_lng == spotAfter.map_lng) {
                    dayTours.remove(visitOrder);
                }
            }
            TourController.drawSidebar();
            TourController.showDay(dayOrder + 1);

        };
        TourController.formatRoute = function(route) {
            var formattedDistance = (route.distance.value / 1000).toFixed(1) + "km";
            var totalDuration = route.duration.value;
            var day = Math.floor(totalDuration / 86400);
            var remainder = totalDuration - day * 86400;
            var hour = Math.floor(remainder / 3600);
            remainder = remainder - hour * 3600;
            var minute = Math.round(remainder / 60);
            var formattedDuration = day > 0 ? `${day}${t('Day')} ${hour}${t('Hour')} ${minute}${t('Minute')}` :
                ( hour > 0 ? `${hour}${t('Hour')} ${minute}${t('Minute')}` : `${minute}${t('Minute')}` );
            return {
                distance: {
                    text: formattedDistance, value: route.distance.value
                },
                duration: {
                    text: formattedDuration, value: route.duration.value
                }
            }

        };
        TourController.calcTotalDistance = function(routes) {
            var totalDistance = 0;
            var totalDuration = 0;
            routes.forEach(function(route) {
                totalDistance += route.distance.value;
                totalDuration += route.duration.value;
            });
            return TourController.formatRoute({
                distance: {value: totalDistance},
                duration: {value: totalDuration}
            });
        };
        TourController.drawSidebar = function() {
            var data = TourController.data;
            var dayHtml = '';
            data.forEach(function(spots, dayOrder) {
                var day = dayOrder + 1;

                var dayRoutes = TourController.routes[dayOrder];
                if (dayRoutes) {
                    var totalRouteInfo = TourController.calcTotalDistance(dayRoutes);
                    dayHtml += `<div class="tour-day" data-day-order="${dayOrder}"><label class="tour-day-title">${day} 日目<label class="float-right mb-0 text-dark ">${totalRouteInfo.distance.text} (${totalRouteInfo.duration.text})</label></label><ol>`;
                    spots.forEach(function(spot, visitOrder) {
                        if (dayRoutes[visitOrder]) {
                            routeInfo = TourController.formatRoute(dayRoutes[visitOrder]);
                            dayHtml += `<li class="tour-spot" data-lat="${spots.map_lat}" data-lng="${spots.map_lng}" data-visit-order="${visitOrder}">
                                                    <div class="row">
                                                        <div class="col"><strong>${spot.title}</strong>
                                                            <button type='button' class='btn btn-sm p-0 float-right remove-spot-from-list'><i class="fa fa-times-circle text-danger"></i></button></div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col text-right text-black-50">${routeInfo.distance.text} (${routeInfo.duration.text})</div>
                                                    </div>
                                                </li>`;
                        } else {
                            dayHtml += `<li class="tour-spot" data-lat="${spots.map_lat}" data-lng="${spots.map_lng}" data-visit-order="${visitOrder}"><strong>${spot.title}</strong><button type='button' class='btn btn-sm p-0 float-right remove-spot-from-list'><i class="fa fa-times-circle text-danger"></i></button></li>`;
                        }

                    });
                } else {
                    dayHtml += `<div class="tour-day" data-day-order="${dayOrder}"><label class="tour-day-title">${day} 日目</label><ol>`;
                    spots.forEach(function(spot, visitOrder) {
                        dayHtml += `<li class="tour-spot" data-lat="${spots.map_lat}" data-lng="${spots.map_lng}" data-visit-order="${visitOrder}"><strong>${spot.title}</strong><button type='button' class='btn btn-sm p-0 float-right remove-spot-from-list'><i class="fa fa-times-circle text-danger"></i></button></li>`;
                    });
                }

                dayHtml += "</ol></div>";
            });
            s('#divTour').html('').html(dayHtml);
            s('#divTour').find(`[data-day-order=${TourController.current_day-1}]`).addClass('active');
            s('#divTour').find('ol li').off('click').on('click', function() {
                s('#divTour').find('ol li').removeClass('active');
                $(this).addClass('active');
                s('.remove-spot-from-list').off('click').on('click', function() {
                    var visitOrder = $(this).parents('li').data('visit-order');
                    var dayOrder = $(this).parents('div.tour-day').data('day-order');
                    TourController.removeSpot(dayOrder, visitOrder);
                    TourController.emit();
                });
            });
            s('#divTour .tour-day-title').off('click').on('click', function() {
                $this = $(this);
                s('#divTour').find('.tour-day').removeClass('active');
                $day = $this.parents('.tour-day').addClass('active');
                TourController.showDay($day.data('day-order') + 1);
                TourController.emit();
            });
        };
        TourController.moveSpotUp = function(dayOrder, visitOrder) {
            var dayTours = TourController.data[dayOrder];
            if (!dayTours) return false;
            var spot = dayTours[visitOrder];
            if (!spot) return false;
            if (visitOrder == 0) {
                if (dayOrder == 0) {
                    return false;
                };
                var neighbor = TourController.data[dayOrder - 1].getLastItem();
                if (neighbor && neighbor.map_lat == spot.map_lat && neighbor.map_lng == spot.map_lng) {
                } else {
                    dayTours.remove(0);
                    TourController.routes[dayOrder].remove(0);
                    TourController.data[dayOrder - 1].push(spot);
                    visitOrder = TourController.data[dayOrder - 1].getLastIndex();
                    dayOrder = dayOrder - 1;
                }

            } else {
                var spotBefore = dayTours[visitOrder - 1];
                var spotAfter = dayTours[visitOrder + 1];
                var spotBeforex2 = dayTours[visitOrder - 2];
                if ((spotBefore && spotAfter && spotBefore.map_lat == spotAfter.map_lat && spotBefore.map_lng == spotAfter.map_lng)
                    || (spotBeforex2 && spotBeforex2.map_lat == spot.map_lat && spotBeforex2.map_lng == spot.map_lng)){
                } else {
                    dayTours[visitOrder - 1] = spot;
                    dayTours[visitOrder] = spotBefore;
                    visitOrder = visitOrder - 1;
                }
            }
            TourController.showDay(dayOrder + 1);
            TourController.drawSidebar();
            s('#divTour').find('.tour-spot').removeClass('active');
            s('#divTour').find(`[data-day-order=${dayOrder}]`).find(`[data-visit-order=${visitOrder}]`).addClass('active');

        };
        TourController.moveSpotDown = function(dayOrder, visitOrder) {
            var dayTours = TourController.data[dayOrder];
            if (!dayTours) return false;
            var spot = dayTours[visitOrder];
            if (!spot) return false;
            if (visitOrder == dayTours.getLastIndex()) {
                if (dayOrder == TourController.data.getLastIndex()) {
                    return false;
                } else {
                    spotAfter = TourController.data[dayOrder + 1][0];
                    if (spotAfter && spotAfter.map_lat == spot.map_lat && spotAfter.map_lng == spot.map_lng) {

                    } else {
                        dayTours.remove(visitOrder);
                        TourController.routes[dayOrder].pop();
                        TourController.data[dayOrder + 1] = [spot, ...TourController.data[dayOrder + 1]];
                        visitOrder = 0;
                        dayOrder = dayOrder + 1;
                    }
                }
            } else {
                var spotAfter = dayTours[visitOrder + 1];
                var spotBefore = dayTours[visitOrder - 1];
                var spotAfterx2 = dayTours[visitOrder + 2];
                if ((spotAfter && spotBefore && spotAfter.map_lat == spotBefore.map_lat && spotAfter.map_lng == spotBefore.map_lng)
                    || (spotAfterx2 && spotAfterx2.map_lat == spot.map_lat && spotAfterx2.map_lng == spot.map_lng)){
                } else {
                    dayTours[visitOrder + 1] = spot;
                    dayTours[visitOrder] = spotAfter;
                    visitOrder = visitOrder + 1;
                }
            }
            TourController.showDay(dayOrder + 1);
            TourController.drawSidebar();
            s('#divTour').find('.tour-spot').removeClass('active');
            s('#divTour').find(`[data-day-order=${dayOrder}]`).find(`[data-visit-order=${visitOrder}]`).addClass('active');
        };
        TourController.moveDayUp = function(dayOrder) {
            var currentDay = TourController.data[dayOrder];
            var dayBefore = TourController.data[dayOrder - 1];
            if (currentDay && dayBefore) {
                TourController.data[dayOrder - 1] = currentDay;
                TourController.data[dayOrder] = dayBefore;
                var currentRoute = TourController.routes[dayOrder];
                var routeBefore = TourController.routes[dayOrder - 1];
                if(currentRoute && routeBefore) {
                    TourController.routes[dayOrder] = routeBefore;
                    TourController.routes[dayOrder - 1] = currentRoute;
                }
                TourController.drawSidebar();
                TourController.showDay(dayOrder);
            }
        };
        TourController.moveDayDown = function(dayOrder) {
            var currentDay = TourController.data[dayOrder];
            var dayAfter = TourController.data[dayOrder + 1];
            if (currentDay && dayAfter) {
                TourController.data[dayOrder + 1] = currentDay;
                TourController.data[dayOrder] = dayAfter;
                var currentRoute = TourController.routes[dayOrder];
                var routeAfter = TourController.routes[dayOrder + 1];
                if (currentRoute && routeAfter) {
                    TourController.routes[dayOrder] = routeAfter;
                    TourController.routes[dayOrder + 1] = currentRoute;
                }
                TourController.drawSidebar();
                TourController.showDay(dayOrder + 2);
            }
        };
        TourController.emptyDay = function(dayOrder) {
            var currentDay = TourController.data[dayOrder];
            if (currentDay) {
                TourController.data[dayOrder] = [];
                TourController.routes[dayOrder] = [];
            }
            TourController.drawSidebar();
            TourController.showDay(dayOrder + 1);
        };
        TourController.drawDayNavbar = function() {
            var $ul = s('#panelLocation').find('ul.nav-tabs');
            var day_count = TourController.data.length;
            var ulHtml = '';
            for (var i=0; i<day_count; i++) {
                var day = i + 1;
                ulHtml += `
                        <li class="nav-item" data-day="${day}">
                            <a class="nav-link ${day==TourController.current_day ? 'active' : ''}" data-toggle="tab">${day} ${t('Day Tour')}
                            ${(day>1 && day==day_count) ? '<i class="fa fa-close ml-3"></i>' : ''}
                            </a>
                        </li>`;
            }
            ulHtml += `<li class="nav-item"><a class="nav-link" data-toggle="tab" id="btnAddDay"><i class='fa fa-plus mr-2'>${t('Add a day')}</i></a></li>`;
            $ul.html(ulHtml);
            TourController.bindNavItemEvents();
            s("#btnAddDay").parent().off('click');
            s("#btnAddDay").off('click').on('click', function(e) {
                e.stopPropagation();
                e.preventDefault();
                TourController.addNewDay();
                TourController.emit();
            });
        };
        TourController.fetchPredefinedSpotsByLocation = function(location_id) {
            if (!location_id || location_id < 0) {
                return false;
            }
            $.get(
                "/itinerary/member/tour/getPredefinedSpots/" + location_id,
                {},
                function(resp) {
                    if (Array.isArray(resp)) {
                        TourController.deselectBlockSpot();
                        s('.block-spot.predefined-location').remove();
                        var loaded = [];
                        s('.block-spot.block-spot-predefined').each(function() {
                            loaded.push($(this).data('id'));
                        });
                        resp.forEach( function(spot) {
                            if (!(loaded.includes(spot.id))) {
                                var html = TourController.getBlockSpotHtml(spot, 'predefined predefined-location');
                                s('#panelSpots').find('.panel-body>.row').append(html);
                            }
                        });
                        TourController.bindBlockSpotEvents();
                    }
                }
            );
        };
        TourController.getData = function() {
            return TourController.data;
        };

        TourController.getPreDefinedSpotsData = function() {
            var data = [];
            s('#panelSpots').find('.block-spot:not(.block-spot-form)').each(function() {
                data.push(TourController.getSpotData($(this)));
            });
            return data;
        };

        TourController.drawBlockSpots = function(spots) {
            s('#panelSpots').find('.block-spot:not(.block-spot-form)').remove();
            var html = '';
            spots.forEach(function(spot) {
                html += TourController.getBlockSpotHtml(spot, 'predefined');
            });
            if (s('#panelSpots').find('.block-spot-form').length) {
                s('#panelSpots').find('.block-spot-form').after(html);
            } else {
                s('#panelSpots').find('.panel-body > .row').eq(0).html(html);
            }
        };

        TourController.getCurrentStatus = function() {
            return {
                data: TourController.data,
                routes: TourController.routes,
                current_day: TourController.current_day,
                selected_spot: TourController.selected_spot,
                selected_geolocation: TourController.selected_geolocation,
                predefined_spots: TourController.getPreDefinedSpotsData()
            };
        };

        TourController.updateStatus = function(status) {
            if (status.data) {
                ['data', 'routes', 'current_day', 'selected_spots', 'selected_geolocation'].forEach(function(key) {
                    TourController[key] = status[key];
                });
                if (TourController.selected_geolocation) {
                    $("input[name=spot_map_lat]").attr("value", TourController.selected_geolocation.map_lat);
                    $("input[name=spot_map_lng]").attr("value", TourController.selected_geolocation.map_lng);
                }
                this.drawDayNavbar();
                this.drawSidebar();
                this.showDay();
            }
            if (status.action) {
                let action = status.action;
                if (action.name == 'save_spot') {
                    var spot = action.data;
                    TourController.saveSpot(spot);
                } else if (action.name == 'remove_spot_from_list') {
                    var spot = action.data;
                    s(`.block-spot[data-id=${spot.id}]`).remove();
                } else if (action.name == 'change_location') {
                    var map_lat = action.data.map_lat;
                    var map_lng = action.data.map_lng;
                    var zoom = action.data.zoom;
                    TourController.navigateMap([parseFloat(map_lat), parseFloat(map_lng)], Number.isInteger(parseInt(zoom)) ? parseInt(zoom) : 8);
                }
            }
        };

        TourController.emit = function(data = TourController.getCurrentStatus()) {
            Object.assign(data, {room_id: $("input[name=room_id]").val()})
            if (window.socket) {
                socket.emit('sync', data);
            }  else {
                return false;
            }
        };
        TourController.bindBlockSpotEvents();

        s("select[name='location_id']").change(function() {
            var $option = $(this).children("option:selected");
            if (location_id = $option.val()) {
                TourController.fetchPredefinedSpotsByLocation(location_id);
                s('.block-spot.form').data('location-id', location_id);
                if ($option.data('lat') && $option.data('lng')) {
                    TourController.navigateMap([parseFloat($option.data('lat')), parseFloat($option.data('lng'))], Number.isInteger(parseInt($option.data('zoom'))) ? parseInt($option.data('zoom')) : 8);
                }
                var status = {};
                Object.assign(status, StatusController.getCurrentStatus());
                Object.assign(status, {
                    action: {
                        name: 'change_location',
                        data: {
                            map_lat: $option.data['lat'],
                            map_lng: $option.data['lng'],
                            zoom: $option.data['zoom']
                        }
                    }
                });
                TourController.emit(status);
            }
        });

        s('.btn-add-spot-to-list').on('click', function() {
            var spot = TourController.getSpotInputData();
            TourController.saveSpot(spot);
            TourController.emit({
               action: {
                   name: 'save_spot',
                   data: spot
               }
            });
            $('.btn-remove-spot-from-list').off('click').on('click', function() {
                var $block = $(this).parents('.block-spot');
                var spot = TourController.getSpotData($block);
                $block.remove();
                TourController.deselectBlockSpot();
                TourController.emit({
                    action: {
                        name: 'remove_spot_from_list',
                        data: spot
                    }
                })
            });

        });
        s('.btn-move-up').on('click', function() {
            var $selected = s('.tour-spot.active');
            if ($selected.length) {
                var visitOrder = $selected.data('visit-order');
                var dayOrder = $selected.parents('.tour-day').data('day-order');
                TourController.moveSpotUp(dayOrder, visitOrder);
                TourController.emit();
                return;

            }
            $selected = s('.tour-day.active');
            if ($selected.length) {
                dayOrder = $selected.data('day-order');
                TourController.moveDayUp(dayOrder);
            }
            TourController.emit();
        });
        s('.btn-move-down').on('click', function() {
            var $selected = s('.tour-spot.active');
            if ($selected.length) {
                var visitOrder = $selected.data('visit-order');
                var dayOrder = $selected.parents('.tour-day').data('day-order');
                TourController.moveSpotDown(dayOrder, visitOrder);
                TourController.emit();
                return;
            }
            $selected = s('.tour-day.active');
            if ($selected.length) {
                dayOrder = $selected.data('day-order');
                 TourController.moveDayDown(dayOrder);
            }
            TourController.emit();
        });
        s('.btn-remove-spot').on('click', function() {
            var  $selected = s('#divTour').find('.tour-spot.active');
            if ($selected.length) {
                var visitOrder = $selected.data('visit-order');
                var dayOrder = $selected.parents('.tour-day').data('day-order');
                TourController.removeSpot(dayOrder, visitOrder);
                TourController.emit();
            }
            $selected = $('.tour-day.active');
            if ($selected.length) {
                dayOrder = $selected.data('day-order');
                TourController.emptyDay(dayOrder);
                TourController.emit();
            }
        });

        TourController.drawSidebar();
        TourController.drawDayNavbar();
        TourController.fetchPredefinedSpotsByLocation(s("[name=location_id]").val());
        setTimeout(function() {
            TourController.showDay(1, true);
        }, 1000);

        if (window.socket !== undefined) {
            socket.on('sync', function (data) {
                TourController.updateStatus(data);
            });
        }

    });
})(jQuery, function(selector, context) {
    return jQuery("#locationControl").find(selector, context);
});

