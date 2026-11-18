<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $page_title ?? 'Dashboard'}} - {{setting_item('site_title') ?? 'Itinerary'}}</title>
    <link rel="icon" type="image/png" href="{{url('images/favicon.png')}}" />

    <meta name="robots" content="noindex, nofollow" />
    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <!-- FCM -->
    <link rel="manifest" href="{{asset('manifest.json')}}">
    <!-- Styles -->
    <link href="{{ asset('libs/select2/css/select2.min.css') }}" rel="stylesheet">
    <link href="{{ asset('libs/flags/css/flag-icon.min.css') }}" rel="stylesheet">
    <link href="{{ asset('dist/admin/css/vendors.css') }}" rel="stylesheet">
    <link href="{{asset('libs/toastr/toastr.min.css')}}" rel="stylesheet">
    <link href="{{ asset('dist/admin/css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('module/user/css/user.css') }}" rel="stylesheet">
    <link href="{{ asset('module/itinerary/css/app.css')  }}" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="{{ asset("libs/daterange/daterangepicker.css") }}" >
{{--    <link href="{{ asset('module/itinerary/js/jquery-scrollbar/prettify/prettify.css')  }}" rel="stylesheet">--}}
    <link href="{{ asset('module/itinerary/js/jquery-scrollbar/jquery.scrollbar.css')  }}" rel="stylesheet">
    <link href="{{ asset('libs/fancybox/css/jquery.fancybox.min.css') }}" rel = "stylesheet">
    <link href="{{ asset('libs/fancybox/css/fancybox_custom.css') }}" rel = "stylesheet">
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link rel='stylesheet' id='google-font-css-css'  href='https://fonts.googleapis.com/css?family=Poppins%3A400%2C500%2C600' type='text/css' media='all' />
    <style type="text/css">
        .bravo_topbar, .bravo_header, .bravo_footer {
            display: none;
        }
        html, body, .bravo_wrap, .bravo_user_profile,
        .bravo_user_profile > .container-fluid > .row-eq-height > .col-md-3 {
            min-height: 100vh !important;
        }
    </style>
    <link href="{{ asset('libs/carousel-2/owl.carousel.css') }}" rel="stylesheet">


    @yield('custom-css')
    {!! \App\Helpers\Assets::css() !!}

    @yield('script.head')

</head>
<body class="{{($enable_multi_lang ?? '') ? 'enable_multi_lang' : '' }} @if(setting_item('site_enable_multi_lang')) site_enable_multi_lang @endif">
{!! setting_item('body_scripts') !!}
<div id="app" class="scrollbar-outer">
    <div class="main-header d-flex inner-content scrollbar-dynamic">
        @include('Itinerary::layouts.member.parts.header')
    </div>
    <div class="main-sidebar inner-content scrollbar-dynamic">
        @php
            $user = auth()->user();
        @endphp
        @include('Itinerary::layouts.member.parts.sidebar')
    </div>
    <div class="main-content">
        @include('Itinerary::layouts.member.parts.bc')
        @yield('content')
        <footer class="main-footer">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-6 copy-right" >
                        {{date('Y')}} &copy; Itinerary by <a href="Javascript:void(0)" target="_blank">Itinerary Team</a>
                    </div>
                    <div class="col-md-6">
                        <div class="text-md-right footer-links d-none">
                            <a href="Javascript:void(0)" target="_blank">About Us</a>
                            <a href="https://m.me/bookingcore" target="_blank">Contact Us</a>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
    </div>
    <div class="backdrop-sidebar-mobile"></div>
</div>

<div class="modal" tabindex="-1" role="dialog" id="modalInquiryDetail">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{__('Inquiry Detail')}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body px-4">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">閉じる</button>
                <a href="#" class="btn btn-success" data-dismiss="modal" target="_blank">対応</a>
            </div>
        </div>
    </div>
</div>
<audio id="fcmReceiveAudio" class="d-none" src="{{ asset('module/itinerary/widgets/sounds/notify_alarm.wav') }}" controls></audio>
<script>
    var bookingCore  = {
        url:'{{url('/')}}',
        map_provider:'{{setting_item('map_provider')}}',
        map_gmap_key:'{{setting_item('map_gmap_key')}}',
        csrf:'{{csrf_token()}}'
    };
</script>
<!-- Scripts -->
@include('Media::browser')
<script src="{{ asset('libs/fancybox/js/jquery-3.4.1.min.js') }}" ></script>
<script src="{{ asset('libs/fancybox/js/jquery.fancybox.min.js') }}" ></script>
<script src="{{ asset('dist/admin/js/vendor.js?_ver='.config('app.version')) }}" ></script>
<script src="{{ asset('dist/admin/js/manifest.js?_ver='.config('app.version')) }}" ></script>
<script src="{{ asset('libs/tinymce/js/tinymce/tinymce.min.js') }}" ></script>

<script src="{{ asset('dist/admin/js/app.js?_ver='.config('app.version')) }}" ></script>
{!! \App\Helpers\Assets::css(true) !!}
{!! \App\Helpers\Assets::js(true) !!}
{!! \App\Helpers\Assets::js() !!}

@yield('script.body')

<script src="{{ asset('libs/select2/js/select2.min.js') }}" ></script>
<script src="{{url('libs/daterange/moment.min.js')}}"></script>
<script src="{{url('libs/daterange/daterangepicker.min.js')}}"></script>

<script src="{{asset('libs/toastr/toastr.min.js')}}"></script>
{!! setting_item('footer_scripts') !!}
{{--<script src="{{asset('module/itinerary/js/jquery-scrollbar/prettify/prettify.js')}}"></script>--}}
<script src="{{asset('module/itinerary/js/jquery-scrollbar/jquery.scrollbar.min.js')}}"></script>
<script src="{{asset('module/itinerary/js/member.js')}}"></script>
<script>
    jQuery(document).ready(function($){
        $('.scrollbar-outer').scrollbar();
    });
    $("#modalInquiryDetail a[data-dismiss=modal]").click(function(e) {
        window.open($(this).attr('href'));
    });
</script>

{{-- @termnator --}}

<!-- FCM -->

<!-- The core Firebase JS SDK is always required and must be listed first -->
<script src="https://www.gstatic.com/firebasejs/7.7.0/firebase-app.js"></script>

<!-- TODO: Add SDKs for Firebase products that you want to use
     https://firebase.google.com/docs/web/setup#available-libraries -->
<script src="https://www.gstatic.com/firebasejs/7.7.0/firebase-messaging.js"></script>

<script>
    // Your web app's Firebase configuration
    var firebaseConfig = {
        apiKey: "",
        authDomain: "",
        databaseURL: "",
        projectId: "",
        storageBucket: "",
        messagingSenderId: "",
        appId: ""
    };
    // Initialize Firebase
    firebase.initializeApp(firebaseConfig);

    // [START get_messaging_object]
    // Retrieve Firebase Messaging object.
    const messaging = firebase.messaging();

    // Add the public key generated from the console here.
    messaging.usePublicVapidKey("");

    // [END get_messaging_object]
    // IDs of divs that display Instance ID token UI or request permission UI.
    const tokenDivId = 'token_div';
    const permissionDivId = 'permission_div';
    // [START refresh_token]
    // Callback fired if Instance ID token is updated.
    messaging.onTokenRefresh(function() {
        messaging.getToken()
            .then(function(refreshedToken) {
                console.log('Token refreshed.');
                // Indicate that the new Instance ID token has not yet been sent to the
                // app server.
                setTokenSentToServer(false);
                // Send Instance ID token to app server.
                sendTokenToServer(refreshedToken);
                // [START_EXCLUDE]
                // Display new Instance ID token and clear UI of all previous messages.
                resetUI();
                // [END_EXCLUDE]
            })
            .catch(function(err) {
                console.log('Unable to retrieve refreshed token ', err);
                showToken('Unable to retrieve refreshed token ', err);
            });
    });
    // [END refresh_token]
    // [START receive_message]
    // Handle incoming messages. Called when:
    // - a message is received while the app has focus
    // - the user clicks on an app notification created by a sevice worker
    //   `messaging.setBackgroundMessageHandler` handler.
    messaging.onMessage(function(payload) {
        console.log("Message received. ", payload);
        // [START_EXCLUDE]
        // Update the UI to include the received message.
        appendMessage(payload);
        // [END_EXCLUDE]
    });
    // [END receive_message]
    function resetUI() {
        clearMessages();
        showToken('loading...');
        // [START get_token]
        // Get Instance ID token. Initially this makes a network call, once retrieved
        // subsequent calls to getToken will return from cache.
        messaging.getToken()
            .then(function(currentToken) {
                if (currentToken) {
                    $.ajax({
                        method: 'post',
                        url: bookingCore.url + '/itinerary/member/inquiry/setUserPushToken',
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
                    sendTokenToServer(currentToken);
                    updateUIForPushEnabled(currentToken);
                } else {
                    // Show permission request.
                    console.log('No Instance ID token available. Request permission to generate one.');
                    requestPermission()
                    // Show permission UI.
                    updateUIForPushPermissionRequired();
                    setTokenSentToServer(false);
                }
            })
            .catch(function(err) {
                console.log('An error occurred while retrieving token. ', err);
                showToken('Error retrieving Instance ID token. ', err);
                setTokenSentToServer(false);
            });
    }
    // [END get_token]
    function showToken(currentToken) {
        // Show token in console and UI.
        console.log("showToken: ", currentToken)
        // var tokenElement = document.querySelector('#token');
        // tokenElement.textContent = currentToken;
    }
    // Send the Instance ID token your application server, so that it can:
    // - send messages back to this app
    // - subscribe/unsubscribe the token from topics
    function sendTokenToServer(currentToken) {
        if (!isTokenSentToServer()) {
            console.log('sendTokenToServer')
            console.log('Sending token to server...');
            // TODO(developer): Send the current token to your server.
            setTokenSentToServer(true);
        } else {
            console.log('Token already sent to server so won\'t send it again ' +
                'unless it changes');
        }
    }
    function isTokenSentToServer() {
        if (window.localStorage.getItem('sentToServer') == 1) {
            return true;
        }
        return false;
    }
    function setTokenSentToServer(sent) {
        window.localStorage.setItem('sentToServer', sent ? 1 : 0);
    }
    function showHideDiv(divId, show) {
        // const div = document.querySelector('#' + divId);
        if (show) {
            console.log("should show div:", divId)
            // div.style = "display: visible";
        } else {
            console.log("should hide div:", divId)
            // div.style = "display: none";
        }
    }
    function requestPermission() {
        console.log('Requesting permission...');
        // [START request_permission]
        messaging.requestPermission()
            .then(function() {
                console.log('Notification permission granted.');
                // TODO(developer): Retrieve an Instance ID token for use with FCM.
                // [START_EXCLUDE]
                // In many cases once an app has been granted notification permission, it
                // should update its UI reflecting this.
                resetUI();
                // [END_EXCLUDE]
            })
            .catch(function(err) {
                console.log('Unable to get permission to notify.', err);
                deleteToken();
            });
        // [END request_permission]
    }
    function deleteToken() {
        // Delete Instance ID token.
        // [START delete_token]
        messaging.getToken()
            .then(function(currentToken) {
                messaging.deleteToken(currentToken)
                    .then(function() {
                        console.log('Token deleted.');
                        setTokenSentToServer(false);
                        // [START_EXCLUDE]
                        // Once token is deleted update UI.
                        resetUI();
                        // [END_EXCLUDE]
                    })
                    .catch(function(err) {
                        console.log('Unable to delete token. ', err);
                    });
                // [END delete_token]
            })
            .catch(function(err) {
                console.log('Error retrieving Instance ID token. ', err);
                showToken('Error retrieving Instance ID token. ', err);
            });
    }
    // Add a message to the messages element.
    function appendMessage(payload) {
        // console.log("appendMessage", payload)
        toastr.info(payload.notification.body);
        $('#fcmReceiveAudio')[0].play();
        // const messagesElement = document.querySelector('#messages');
        // const dataHeaderELement = document.createElement('h5');
        // const dataElement = document.createElement('pre');
        // dataElement.style = 'overflow-x:hidden;'
        // dataHeaderELement.textContent = 'Received message:';
        // dataElement.textContent = JSON.stringify(payload, null, 2);
        // messagesElement.appendChild(dataHeaderELement);
        // messagesElement.appendChild(dataElement);
    }
    // Clear the messages element of all children.
    function clearMessages() {
        console.log("clearMessages.")
        // const messagesElement = document.querySelector('#messages');
        // while (messagesElement.hasChildNodes()) {
        //   messagesElement.removeChild(messagesElement.lastChild);
        // }
    }
    function updateUIForPushEnabled(currentToken) {
        showHideDiv(tokenDivId, true);
        showHideDiv(permissionDivId, false);
        showToken(currentToken);
    }
    function updateUIForPushPermissionRequired() {
        showHideDiv(tokenDivId, false);
        showHideDiv(permissionDivId, true);
    }
    resetUI();
</script>
</body>
</html>
