@extends('layout')

@section('content')
    <h1>Login</h1>

    <textarea id="status" rows="10" style="width:800px">
        Login to get facebook access token..
    </textarea>


    <div id="fb-root"></div>
    <script async defer crossorigin="anonymous"
            src="https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v8.0&appId=841934066635265&autoLogAppEvents=1"
            nonce="eBTxD4CO"></script>


    <script>

        function statusChangeCallback(response) {  // Called with the results from FB.getLoginStatus().

            console.log('statusChangeCallback',response);                   // The current login status of the person.

            if (response.status === 'connected') {   // Logged into your webpage and Facebook.

                let token = response.authResponse.accessToken;
                console.log("asdfasdf",token)

                document.getElementById("status").value = token;

            } else {                                 // Not logged into your webpage or we are unable to tell.
                document.getElementById('status').innerHTML = 'Please log into this webpage.';
            }
        }

        function checkLoginState() {               // Called when a person is finished with the Login Button.
            FB.getLoginStatus(function (response) {   // See the onlogin handler
                statusChangeCallback(response);
            });
        }

        window.fbAsyncInit = function () {
            FB.init({
                appId: '{app-id}',
                cookie: true,                     // Enable cookies to allow the server to access the session.
                xfbml: true,                     // Parse social plugins on this webpage.
                version: '{api-version}'           // Use this Graph API version for this call.
            });

            FB.getLoginStatus(function (response) {   // Called after the JS SDK has been initialized.
                statusChangeCallback(response);        // Returns the login status.
            });
        };

    </script>

    <!-- The JS SDK Login Button -->

    <fb:login-button scope="public_profile,email" onlogin="checkLoginState();">
    </fb:login-button>

    <!-- Load the JS SDK asynchronously -->
    <script async defer crossorigin="anonymous" src="https://connect.facebook.net/en_US/sdk.js"></script>


@endsection
