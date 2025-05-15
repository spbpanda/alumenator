<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Maintenance Mode') }}</title>
    <style>
        html { width: 100%; height: 100%; }
        body { text-align: center; margin: 0px; padding: 0px; height: 100%; color: #fff; font-family: sans-serif;
            background: linear-gradient(-45deg, #282828, #282828);
            background-size: 400% 400%;
            -webkit-animation: Gradient 15s ease infinite;
            -moz-animation: Gradient 15s ease infinite;
            animation: Gradient 15s ease infinite;}
        .vh { height: 100%; align-items: center; display: flex; }
        .vh > div { width: 100%; text-align: center; vertical-align: middle; }
        img { max-width: 100%; }
        .wrap {text-align: center;}
        .wrap h1 {color: #ff6d1d; text-shadow: 0 0 5px #ff5a00; font-size: 30px;font-weight: 700;margin: 0 0 40px;}
        .wrap h2 {font-size: 24px;font-weight: 400;line-height: 1.6;margin: 0 0 50px;}
        @-webkit-keyframes Gradient {
            0% {background-position: 0% 50%}
            50% {background-position: 100% 50%}
            100% {background-position: 0% 50%}
        }
        @-moz-keyframes Gradient {
            0% {background-position: 0% 50%}
            50% {background-position: 100% 50%}
            100% {background-position: 0% 50%}
        }
        @keyframes Gradient {
            0% {background-position: 0% 50%}
            50% {background-position: 100% 50%}
            100% {background-position: 0% 50%}
        }
    </style>
</head>
<body>
    <div class="vh">
      <div>
       <div class="wrap">
         <div class="logos" style="margin-bottom: 30px;">
           <img src="https://{{ request()->getHost() }}/img/logo.png">
         </div>
        <h1>{{ __('Maintenance Mode') }}</h1>
        <h2><p>{{ __('Sorry for the inconvenience') }}.<br>{{ $exception->getMessage() ?: __('You are not authorized to access this page.') }}</p></h2>
         <img class="logo" style="max-height:100px;" src="https://minestorecms.com/assets/img/logos/logo-white.png">
        </div>
      </div>
    </div>
</body>
