<div class="g-header">
    <div class="left">
        <h2>{{$translation->title}}</h2>
        @if($translation->address)
            <p class="address"><i class="fa fa-map-marker"></i>
                {{$translation->address}}
            </p>
        @endif
    </div>
    <div class="right">
        @if($review_score)
            <div class="review-score">
                <div class="head">
                    <div class="left">
                        <span class="head-rating">{{$review_score['score_text']}}</span>
                        <span class="text-rating">{{__("from :number reviews",['number'=>$review_score['total_review']])}}</span>
                    </div>
                    <div class="score">
                        {{$review_score['score_total']}}<span>/5</span>
                    </div>
                </div>
                <div class="foot">
                    {{__(":number% of guests recommend",['number'=>$row->recommend_percent])}}
                </div>
            </div>
        @endif
    </div>
</div>
<div class="g-tour-feature">
    <div class="row">
        @if($row->duration)
            <div class="col-xs-6 col-lg-3 col-md-6">
                <div class="item">
                    <div class="icon">
                        <i class="icofont-wall-clock"></i>
                    </div>
                    <div class="info">
                        <h4 class="name">{{__("Duration")}}</h4>
                        <p class="value">
                            @if($row->duration > 1)
                                {{ __(":number hours",array('number'=>$row->duration)) }}
                            @else
                                {{ __(":number hour",array('number'=>$row->duration)) }}
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        @endif
        @if(!empty($row->category_tour->name))
            @php $cat =  $row->category_tour->translateOrOrigin(app()->getLocale()) @endphp
            <div class="col-xs-6 col-lg-3 col-md-6">
                <div class="item">
                    <div class="icon">
                        <i class="icofont-beach"></i>
                    </div>
                    <div class="info">
                        <h4 class="name">{{__("Tour Type")}}</h4>
                        <p class="value">
                            {{$cat->name ?? ''}}
                        </p>
                    </div>
                </div>
            </div>
        @endif
        @if($row->max_people)
            <div class="col-xs-6 col-lg-3 col-md-6">
                <div class="item">
                    <div class="icon">
                        <i class="icofont-travelling"></i>
                    </div>
                    <div class="info">
                        <h4 class="name">{{__("Group Size")}}</h4>
                        <p class="value">
                            @if($row->max_people > 1)
                                {{ __(":number persons",array('number'=>$row->max_people)) }}
                            @else
                                {{ __(":number person",array('number'=>$row->max_people)) }}
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        @endif
        @if(!empty($row->location->name))
                @php $location =  $row->location->translateOrOrigin(app()->getLocale()) @endphp
            <div class="col-xs-6 col-lg-3 col-md-6">
                <div class="item">
                    <div class="icon">
                        <i class="icofont-island-alt"></i>
                    </div>
                    <div class="info">
                        <h4 class="name">{{__("Location")}}</h4>
                        <p class="value">
                            {{$location->name ?? ''}}
                        </p>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@if($row->getGallery())
    <div class="g-gallery">
        <div class="fotorama" data-width="100%" data-thumbwidth="135" data-thumbheight="135" data-thumbmargin="15" data-nav="thumbs" data-allowfullscreen="true">
            @foreach($row->getGallery() as $key=>$item)
                <a href="{{$item['large']}}" data-thumb="{{$item['thumb']}}"></a>
            @endforeach
        </div>
    </div>
@endif
@if($translation->content)
    <div class="g-overview">
        <h3>{{__("Overview")}}</h3>
        <div class="description">
            <?php echo $translation->content ?>
        </div>
    </div>
@endif
{{--@include('Tour::frontend.layouts.details.tour-attributes')--}}
@if($translation->faqs)
<div class="g-faq">
    <h3> {{__("FAQs")}} </h3>
    @foreach($translation->faqs as $item)
        <div class="item">
            <div class="header">
                <i class="field-icon icofont-support-faq"></i>
                <h5>{{$item['title']}}</h5>
                <span class="arrow"><i class="fa fa-angle-down"></i></span>
            </div>
            <div class="body">
                {!! clean($item['content']) !!}
            </div>
        </div>
    @endforeach
</div>
@endif
@if((!empty($tour)))
    <link href="{{asset('module/itinerary/css/map-controller.css')}}" rel="stylesheet">
    <style>
        #map_content {
            height: 480px;
        }
        .btn-spot-preview {
            padding: 3px 9px;
        }
        #panelSpots {
            overflow-x: hidden;
        }
        #divTour {
            font-size: 90%;
        }
    </style>
    <div class="row" id="locationControl" style="">
        <div class="col-md-3">
            <div class="panel">
                <div class="panel-body">
                    <div class="form-group mb-1">
                        <label class="control-label"><strong>{{__('Tour Schedule')}}</strong></label>
                    </div>
                    <div class="row">
                        <div class="col" id="divTour">

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-9">
            <div class="panel" id="panelLocation">
                <div class="panel-title mb-2">
                    <div class="row">
                        <div class="col d-flex align-items-center ">
                            <strong>{{__('Tour Locations')}}</strong>
                        </div>
                    </div>
                </div>
                <div class="panel-body">
                    <ul class="nav nav-tabs">
                        <li class="nav-item " data-day="1">
                            <a class="nav-link active" data-toggle="tab">1日目</a>
                        </li>
                    </ul>

                    <div class="form-group">
                        <div class="control-map-group">
                            <div id="map_content"></div>

                        </div>
                    </div>
                    <textarea class="d-none"  name="tour_spots"><?php echo old('tour_spots', json_encode($tour->getSpotsData())); ?></textarea>
                    <textarea class="d-none" name="tour_routes"><?php echo old('tour_routes', $tour->getMeta('routes', ''))?></textarea>
                </div>
            </div>
            <div class="panel" id="panelSpots">
                <div class="panel-body">
                    <div class="row">
                        @php
                            $predefined_spots = $tour->spots()->orderBy('itn_tour_has_spots.day_order', 'asc')->orderBy('itn_tour_has_spots.visit_order', 'asc')->groupBy('itn_tour_has_spots.spot_id')->get();
                        @endphp
                        @foreach($predefined_spots as $spot)
                            <div class="block-spot block-spot-predefined col-md-4 col-sm-6" data-location="{{$spot->location_id}}" data-lat="{{$spot->map_lat}}" data-lng="{{$spot->map_lng}}" data-id="{{$spot->id}}" data-image-id="{{$spot->image_id}}">
                                <div class="block-spot-wrapper">
                                    <div class="overlay">
                                        <button type="button" class="btn btn-success btn-spot-preview"><i class=" fa fa-search"></i></button>
                                    </div>
                                    <div class="row">
                                        <div class="col text-center spot-image">
                                            @if($spot->image_id)
                                                <img src="{{get_file_url($spot->image_id, 'full')}}" height="96">
                                            @elseif($spot->image_url)
                                                <img src="{{$spot->image_url}}" height="96" alt="{{$spot->title}}">
                                            @else
                                                <div style="width:100%; height:96px; background-color:#eee;"></div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col text-center">
                                            <p class="mt-1 mb-0 spot-title"><strong>{{$spot->title}}</strong></p>
                                            <p class="mt-1 mb-0 spot-address"><i>{{$spot->address}}</i></p>
                                            <p class="mt-1 mb-0 text-left spot-content">{{$spot->content}}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

        </div>

        <!-- Modal -->
        <div id="modalPreview" class="modal fade" role="dialog">
            <div class="modal-dialog">
                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title"></h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" data-dismiss="modal">{{__('OK')}}</button>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <script src="{{ asset('libs/jquery-3.3.1.min.js') }}"></script>
    @php
        $me = null;
        if (auth()->user()) $me = new Modules\Itinerary\Models\ItineraryUser(auth()->user());
        $guider = $tour->guider()->first();
        if ($guider) {
            $guider = new Modules\Itinerary\Models\ItineraryUser($guider);
            $form = $guider->getForm();
            if ($form) $fields = $form->fields()->get();
        }

    @endphp
    @if (!empty($form) && ($me && !$me->isMember()))
        <div class="modal fade hidden w-100" id="queryModal" style = "max-width: initial;">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">

                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h4 class="modal-title">{{$guider->getDisplayName()}}</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>

                    <!-- Modal body -->
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12">
                                <img src="{{get_file_url($form->image_id, 'full')}}" class="img-responsive" width="100%">
                            </div>
                        </div>
                        @if ($form->content)
                            <div class="row mt-2">
                                <div class="col-12">
                                    @php echo $form->content;
                                    @endphp
                                </div>
                            </div>
                        @endif
                        @if (!$guider->isLoggedIn() && $form->afk_message )
                            <div class="row mt-2">
                                <div class="col-12">
                                    @php echo $form->afk_message; @endphp
                                </div>
                            </div>
                        @endif
                        <div class="row mt-2 py-2">
                            <div class="col-12">
                                <form action="{{url('itinerary/inquiry/create')}}" id="frmRegister">
                                    @csrf
                                    <input type="hidden" value="{{$tour->id}}" name="tour_id">
                                    @foreach($fields as $index => $field)
                                        <div class="form-group">
                                            <label class="control-label">{{t('itinerary::form', $field->name)}}</label>
                                            @if ($field->type == 'textarea')
                                                <textarea name="{{$field->name}}" class="form-control" {{$field->attr ?? ''}} placeholder="{{$field->placeholder ? t('itinerary::form', $field->placeholder) : ''}}"></textarea>
                                            @elseif ($field->type == 'daterange')
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <input type="datetime-local" name="{{$field->name}}_from" class="form-control">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <input type="datetime-local" name="{{$field->name}}_to" class="form-control">
                                                    </div>
                                                </div>
                                            @else
                                                @if($field->name == "お名前" || $field->name=="name")
                                                    <input name="{{$field->name}}" type="{{$field->type}}" class="form-control" {{$field->attr ?? ''}} placeholder="{{$field->placeholder ? t('itinerary::form', $field->placeholder) : ''}}" required readonly value="{{$me->getDisplayName()}}">
                                                @elseif($field->name=="email" || $field->name=="メールアドレス")
                                                    <input name="{{$field->name}}" type="{{$field->type}}" class="form-control" {{$field->attr ?? ''}} placeholder="{{$field->placeholder ? t('itinerary::form', $field->placeholder) : ''}}" required readonly value="{{$me->email}}">
                                                @else
                                                    <input name="{{$field->name}}" type="{{$field->type}}" class="form-control" {{$field->attr ?? ''}} placeholder="{{$field->placeholder ? t('itinerary::form', $field->placeholder) : ''}}">
                                                @endif

                                            @endif
                                        </div>
                                    @endforeach
                                    <div class="form-group">
                                        <label class="control-label">予約時間</label>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <input type="datetime-local" name="reservation_date" class="form-control reservation-control">
                                            </div>
                                            <div class="col-md-6">
                                                <input type="datetime-local" name="reservation_time" class="form-control reservation-control">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group date-error" style = "display: none;">
                                        <label class="control-label" style = "color: red;">予約時間を今より早くすることはできません</label>
                                    </div>

                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Modal footer -->
                    <div class="modal-footer modal-footer d-flex align-items-center justify-content-center mt-2">
                        <button type="button" class="btn btn-success" data-dismiss="modal">{{__('Cancel')}}</button>
                        <button type="button" class="btn btn-primary" id="btnSubmit">送信</button>
                    </div>

                </div>\

            </div>
        </div>
        <script>
            $(document).ready(function() {

                function zero_pad(number, length) {

                    let str = '' + number;
                    while (str.length < length) {
                        str = '0' + str;
                    }

                    return str;

                }

                function time_maker(min_interval){
                    let return_array = [];
                    for(let cur_hour = 0; cur_hour < 24; cur_hour++){
                        for(let cur_min = 0; cur_min < 60; cur_min += min_interval){
                            return_array.push(zero_pad(cur_hour, 2) + ":" + zero_pad(cur_min, 2));
                        }
                    }
                    return return_array;
                }

                function formatDate(date) {
                    var d = new Date(date),
                        month = '' + (d.getMonth() + 1),
                        day = '' + d.getDate(),
                        year = d.getFullYear();

                    if (month.length < 2)
                        month = '0' + month;
                    if (day.length < 2)
                        day = '0' + day;

                    return [year, month, day].join('-');
                }

                @if($me && !$me->isMember())
                    $('.bravo_detail_tour .bravo_content .owner-info').parent().removeClass('col-md-12').addClass('col-md-9').after(`<div class='col-md-3'><button type="button" class = "query-btn btn btn-lg btn-primary w-100" data-toggle="modal" data-target="#queryModal">{{__('Ask For Information')}}</button></div>`);
                    if ($("input[name=calendar_from]").length) {
                        $("input[name=calendar_from]").attr('type', 'text').datetimepicker({
                            startDate: new Date,
                            lang: 'ja',
                            allowTimes: [
                                '0:00','0:30','1:00','1:30','2:00','2:30','3:00','3:30','4:00','4:30','5:00','5:30','6:00','6:30',
                                '7:00','7:30','8:00','8:30','9:00','9:30','10:00','10:30','11:00','11:30','12:00','12:30',
                                '13:00','13:30','14:00','14:30','15:00','15:30','16:00','16:30','17:00','17:30','18:00','18:30',
                                '19:00','19:30','20:00','20:30','21:00','21:30','22:00','22:30','23:00','23:30'
                            ]
                        });
                    }
                    if ($("input[name=calendar_to]").length) {
                        $("input[name=calendar_to]").attr('type', 'text').datetimepicker({
                            lang: 'ja',
                            allowTimes: [
                                '0:00','0:30','1:00','1:30','2:00','2:30','3:00','3:30','4:00','4:30','5:00','5:30','6:00','6:30',
                                '7:00','7:30','8:00','8:30','9:00','9:30','10:00','10:30','11:00','11:30','12:00','12:30',
                                '13:00','13:30','14:00','14:30','15:00','15:30','16:00','16:30','17:00','17:30','18:00','18:30',
                                '19:00','19:30','20:00','20:30','21:00','21:30','22:00','22:30','23:00','23:30'
                            ]
                        });
                    }

                    // set reservation date and time format
                    let date_control = $("input[name=reservation_date]");
                    let time_control = $("input[name=reservation_time]");
                    date_control.attr('type', 'text').attr('placeholder', '予約日').datetimepicker({
                        lang: 'ja',
                        timepicker: false,
                        format: 'Y-m-d'
                    });
                    time_control.attr('type', 'text').attr('placeholder', "予約時間").datetimepicker({
                        lang: 'ja',
                        datepicker: false,
                        format: 'H:i',
                        allowTimes: time_maker(1)
                    });

                    <?php date_default_timezone_set('Asia/Tokyo'); ?>

                    $.datetimepicker.setLocale('ja');
                    @php
                        $inquiry = $me ? $me->getAppliedInquiryByTour($tour)->first() : null;
                    @endphp
                    @if ($inquiry && $guider && $guider->isLoggedIn())

                    var btnHtml = `<a href="{{url('itinerary/consult') . "/" . $inquiry->id}}" class = "btn-chatting btn btn-lg btn-success w-100 mt-1 text-white" >{{__('Live consult')}}</a>`;
                    if ($('.query-btn').length) {
                        $('.query-btn').after(btnHtml);
                    } else {
                        $('.bravo_detail_tour .bravo_content .owner-info').parent().removeClass('col-md-12').addClass('col-md-9').after(btnHtml);
                    }

                    @endif
                    $('#btnSubmit').on('click', function() {
                        $("#frmRegister").find(".date-error").hide();
                        var data = {};
                        var  $form = $('#frmRegister');
                        var controls = $form.find(".form-control");
                        var isValid = true;
                        controls.each(function() {
                            $(this).removeClass('invalid');
                            if ($(this).get(0).hasAttribute("required") && !$(this).val()) {
                                $(this).addClass("invalid");
                                isValid = false;
                            }
                        });
                        if (!isValid) {
                            return isValid;
                        }
                        controls.each(function() {
                            let $this = $(this);
                            let name = $this.attr('name');
                            if($this.hasClass('reservation-control'))
                                return;
                            if (name.endsWith("_from") && $form.find("input[name='" + name.substring(0, name.length - 5) + "_to" + "']").length) {
                                $that = $form.find("input[name='" + name.substring(0, name.length - 5) + "_to" + "']");
                                data[$this.parent().parent().parent().find('.control-label').text()] = ($this.val() ? $this.val() : "指定なし") + " ~ " + ($that.val() ? $that.val() : "指定なし");
                            } else if (name.endsWith("_to") && $form.find("input[name='" + name.substring(0,name.length - 3) + "_from" +  "']").length) {

                            } else {
                                data[$this.parent().find('.control-label').text()] = $(this).val();
                            }
                        });
                        let reservation_date = date_control.val() + " " + time_control.val() + ":00";
                        if(date_control.val() === "" || time_control.val() === "")
                            reservation_date = null;

                        $.post(
                            $("#frmRegister").attr('action'),
                            {
                                reservation_date: reservation_date,
                                form_content: JSON.stringify(data),
                                '_token' : $("input[name='_token']").val(),
                                tour_id : $("input[name='tour_id']").val()
                            },
                            function(resp) {
                                if (resp.success) {
                                    alert('お問い合わせありがとうございます。');
                                    if (resp.inquiry_id) {
                                        {{--location.href = "{{url('itinerary/consult')}}" + "/" + resp.inquiry_id;--}}
                                        let controls = $("#frmRegister .form-control");
                                        controls.each(function() {
                                            $(this).val('');
                                        });
                                        $("#queryModal").modal('toggle');
                                        localStorage.setItem('inquiry_id', resp.inquiry_id);
                                    //    @terminator
                                        messaging.getToken()
                                            .then(function(currentToken) {
                                                if (currentToken) {
                                                    $.ajax({
                                                        method: 'post',
                                                        url: bookingCore.url + '/itinerary/inquiry/setUserPushToken',
                                                        data: {
                                                            push_token: currentToken
                                                        },
                                                        dataType: 'json',
                                                        success: function success(data) {
                                                            if (data.status) {
                                                                console.log('Push token successfully saved!')
                                                            } else {
                                                                console.log('Save push token failed.')
                                                            }
                                                        }
                                                    })
                                                } else {
                                                    // Show permission request.
                                                    console.log('No Instance ID token available. Request permission to generate one.');
                                                }
                                            })
                                            .catch(function(err) {
                                                console.log('An error occurred while retrieving token. ', err);
                                            });
                                    }
                                    {{--location.href = "{{url('itinerary/consult') . "/" . $inquiry->id}}";--}}
                                } else {
                                    alert('お問い合わせのプロセスに問題が発生しました。再度お問い合わせください。');
                                    location.reload();
                                }
                            }
                        )
                    });
                    $("#frmRegister .reservation-control").on("change", function(){
                        $("#frmRegister").find(".date-error").hide();
                        $(this).removeClass('invalid')
                    });
                    $("#queryModal").on('hidden.bs.modal', function() {
                        $("#queryModal .invalid").removeClass("invalid");
                    });
                @endif


            })
        </script>
    @endif
    @if(!$me)
        <script>
            $(document).ready(function() {
                $('.bravo_detail_tour .bravo_content .owner-info').parent().removeClass('col-md-12').addClass('col-md-9').after(`<div class='col-md-3'><button type="button" class = "query-btn btn btn-lg btn-primary w-100" data-toggle="modal" data-target="#login">{{__('Ask For Information')}}</button></div>`);
            })
        </script>
    @endif
    <script src="{{asset('module/itinerary/js/tour-controller-util.js')}}"></script>
    <script src="{{asset('module/itinerary/js/user-map-controller.js')}}"></script>
    <style>
        .form-control.invalid {
            border-color: red!important;
        }
    </style>
@endif
