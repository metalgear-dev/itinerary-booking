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
            "Routes calculation has not been completed. Would you save it anyway?" : "ルートの計算は完了していません。とにかく保存しますか？"
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
            s('.nav-link:not(#btnAddDay)').off('click').on('click', function() {
                var day = $(this).parent().data('day');
                TourController.showDay(day);
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
        };
        TourController.defaultMapOption = {
            disableScripts: true,
            fitBounds: true,
            ready: function (engineMap) {

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
                var m = new google.maps.Marker(markerOption)
                TourController.infowindow = null;
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
                        var infowindow = TourController.infowindow;
                        if (infowindow) {
                            infowindow.close();
                        }
                        TourController.infowindow = new google.maps.InfoWindow({
                            width: 320,
                            maxWidth: 320,
                            content
                        });
                        TourController.infowindow.open(markerOption.map, m);
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
        var dayTour = TourController.data[TourController.current_day];
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
            var dayTour = this.data[day - 1];

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
                    console.log(label);
                    TourController.engineMap.addMarker([parseFloat(spot.map_lat), parseFloat(spot.map_lng)], {
                        icon_options:{},
                        label});
                });
            }
        };
        TourController.getBlockSpotHtml = function(data, clazz='new') {
            return `
                    <div class="block-spot block-spot-${clazz} col-md-4 col-sm-6" data-id="${data.id}" data-location="${data.location_id}" data-lat="${data.map_lat}" data-lng="${data.map_lng}" data-image-id="${data.image_id}">
                        <div class="block-spot-wrapper">
                            <div class="overlay">
                                <button type="button" class="btn btn-success mr-1 btn-spot-preview"><i class=" fa fa-search"></i></button>
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
            TourController.selected_spot = undefined;
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
            var data = this.data;
            var dayHtml = '';
            data.forEach(function(spots, dayOrder) {
                var day = dayOrder + 1;

                var dayRoutes = TourController.routes[dayOrder];
                if (dayRoutes) {
                    var totalRouteInfo = TourController.calcTotalDistance(dayRoutes);
                    dayHtml += `<div class="tour-day" data-day-order="${dayOrder}"><label class="tour-day-title">${day} 日目<label class="float-right mb-0 text-dark font-weight-normal">${totalRouteInfo.distance.text} (${totalRouteInfo.duration.text})</label></label><ol>`;
                    spots.forEach(function(spot, visitOrder) {
                        if (dayRoutes[visitOrder]) {
                            routeInfo = TourController.formatRoute(dayRoutes[visitOrder]);
                            dayHtml += `<li class="tour-spot" data-lat="${spots.map_lat}" data-lng="${spots.map_lng}" data-visit-order="${visitOrder}">
                                                    <div class="row">
                                                        <div class="col"><strong>${spot.title}</strong>
                                                            <button type='button' class='btn btn-sm p-0 float-right remove-spot-from-list'></button></div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col text-right text-black-50">${routeInfo.distance.text} (${routeInfo.duration.text})</div>
                                                    </div>
                                                </li>`;
                        } else {
                            dayHtml += `<li class="tour-spot" data-lat="${spots.map_lat}" data-lng="${spots.map_lng}" data-visit-order="${visitOrder}"><strong>${spot.title}</strong><button type='button' class='btn btn-sm p-0 float-right remove-spot-from-list'></button></li>`;
                        }

                    });
                } else {
                    dayHtml += `<div class="tour-day" data-day-order="${dayOrder}"><label class="tour-day-title">${day} 日目</label><ol>`;
                    spots.forEach(function(spot, visitOrder) {
                        dayHtml += `<li class="tour-spot" data-lat="${spots.map_lat}" data-lng="${spots.map_lng}" data-visit-order="${visitOrder}"><strong>${spot.title}</strong><button type='button' class='btn btn-sm p-0 float-right remove-spot-from-list'></button></li>`;
                    });
                }

                dayHtml += "</ol></div>";
            });
            s('#divTour').html('').html(dayHtml);
            s('#divTour').find('ol li').off('click').on('click', function() {
                s('#divTour').find('ol li').removeClass('active');
                $(this).addClass('active');
                s('.remove-spot-from-list').off('click').on('click', function() {
                    var visitOrder = $(this).parents('li').data('visit-order');
                    var dayOrder = $(this).parents('div.tour-day').data('day-order');
                    TourController.removeSpot(dayOrder, visitOrder);
                });
            });
        };

        TourController.drawDayNavbar = function() {
            var $ul = s('#panelLocation').find('ul.nav-tabs');
            var day_count = this.data.length;
            var ulHtml = '';
            for (var i=0; i<day_count; i++) {
                var day = i + 1;
                ulHtml += `
                        <li class="nav-item" data-day="${day}">
                            <a class="nav-link ${day==TourController.current_day ? 'active' : ''}" data-toggle="tab">${day} ${t('Day Tour')}
                            </a>
                        </li>`;
            }
            $ul.html(ulHtml);
            TourController.bindNavItemEvents();
            s("#btnAddDay").parent().off('click');
            s("#btnAddDay").off('click').on('click', function(e) {
                e.stopPropagation();
                e.preventDefault();
                TourController.addNewDay();
                TourController.showDay();
            });
        };
        TourController.bindBlockSpotEvents();
        TourController.drawSidebar();
        TourController.drawDayNavbar();
        setTimeout(function() {
            TourController.showDay(1, true);
        }, 1000);
    });
})(jQuery, function(selector, context) {
    return jQuery("#locationControl").find(selector, context);
});

