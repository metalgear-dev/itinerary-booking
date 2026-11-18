<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="{{$html_class ?? ''}}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php
        $favicon = setting_item('site_favicon');
    @endphp
    @if($favicon)
        @php
            $file = (new \Modules\Media\Models\MediaFile())->findById($favicon);
        @endphp
        @if(!empty($file))
            <link rel="icon" type="{{$file['file_type']}}" href="{{asset('uploads/'.$file['file_path'])}}" />
        @else:
            <link rel="icon" type="image/png" href="{{url('images/favicon.png')}}" />
        @endif
    @endif

    @include('layouts.parts.seo-meta')
    <link href="{{ asset('libs/bootstrap/css/bootstrap.css') }}" rel="stylesheet">
    <link href="{{ asset('libs/font-awesome/css/font-awesome.css') }}" rel="stylesheet">
    <link href="{{ asset('libs/ionicons/css/ionicons.min.css') }}" rel="stylesheet">
    <link href="{{ asset('libs/icofont/icofont.min.css') }}" rel="stylesheet">
    <link href="{{ asset('libs/select2/css/select2.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="{{ asset("libs/daterange/daterangepicker.css") }}" >
    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link rel='stylesheet' id='google-font-css-css'  href='https://fonts.googleapis.com/css?family=Poppins%3A300%2C400%2C500%2C600' type='text/css' media='all' />
    {!! \App\Helpers\Assets::css() !!}
    {!! \App\Helpers\Assets::js() !!}
    <script>
        var bookingCore = {
            url:'{{url( app_get_locale() )}}',
            url_root:'{{ url('') }}',
            booking_decimals:{{(int)setting_item('currency_no_decimal',2)}},
            thousand_separator:'{{setting_item('currency_thousand')}}',
            decimal_separator:'{{setting_item('currency_decimal')}}',
            currency_position:'{{setting_item('currency_format')}}',
            currency_symbol:'{{currency_symbol()}}',
            date_format:'{{get_moment_date_format()}}',
            map_provider:'{{setting_item('map_provider')}}',
            map_gmap_key:'{{setting_item('map_gmap_key')}}',
            routes:{
                login:'{{route('auth.login')}}',
                register:'{{route('auth.register')}}',
            },
            currentUser:{{(int)Auth::id()}}
        };
    </script>
    <!-- Styles -->
    @yield('head')
    {{--Custom Style--}}
    @include('layouts.parts.custom-css')
    <link href="{{ asset('libs/carousel-2/owl.carousel.css') }}" rel="stylesheet">
    @yield('custom-css')
</head>
<body class="{{$body_class ?? ''}}">
    {!! setting_item('body_scripts') !!}
    <div id="rtcWidget"></div>
    <div class="bravo_wrap">
{{--        @include('layouts.parts.adminbar')--}}
        @include('layouts.parts.topbar')
        @include('layouts.parts.header')
        @yield('content')
        @include('layouts.parts.footer')
    </div>
    {!! setting_item('footer_scripts') !!}


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
                        // remove save push token ajax request
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
