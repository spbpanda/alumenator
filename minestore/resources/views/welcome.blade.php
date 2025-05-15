<?php if(isset($_GET["ref"])) setcookie("ref", $_GET["ref"], time()+259200); ?>
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
<meta charset="utf-8">
<meta name="author" content="MineStoreCMS Development Team">
<title>{{ $site_name }}</title>
<meta property="og:title" content="{{ $site_name }}"/>
<meta property="og:image" content="{{ asset('/assets/img/banner.png') }}"/>
<meta property="og:description" content="{{ $site_desc }}"/>
<meta name="description" content="{{ $site_desc }}">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<link rel="shortcut icon" href="{{ asset('/assets/img/favicon.png') }}" type="image/png">
</head>
<body>
<div id="app">
<app></app>
</div>
</body>
<script>const locale = "{{ App::getLocale() }}";</script>
<script src="{{ asset('js/common.js?v27') }}"></script>
</html>
