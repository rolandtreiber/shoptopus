<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $documentName }} - {{env('APP_NAME')}}</title>

    <style type="text/css">
        @page {
            margin: 0cm 0cm;
        }

        body {
            margin-top: 6cm;
            margin-left: 1cm;
            margin-right: 0cm;
            margin-bottom: 2cm;
        }

        * {
            font-family: Verdana, Arial, sans-serif;
        }

        a {
            color: #fff;
            text-decoration: none;
        }

        table {
            font-size: x-small;
        }

        tfoot tr td {
            font-weight: bold;
            font-size: small;
        }

        .content {
            font-size: smaller;
        }

        header {
            position: fixed;
            top: 0cm;
            left: 0cm;
            right: 0cm;
            height: 3cm;
        }

        footer {
            position: fixed;
            bottom: 0cm;
            left: 0cm;
            right: 0cm;
            height: 1cm;
        }

        .information .logo {
            margin: 5px;
        }

        .information table {
            padding: 0px;
        }

        .title {
            font-size: x-large;
            font-weight: bold;
            color: white;
        }

        .title-2 {
            font-size: small;
            font-weight: lighter;
            color: white;
        }

        .icon {
            width: 12px;
            height: 12px;
            vertical-align: middle;
            display: inline-block;
        }

        .small-margin-p {
            margin-block-end: 0.5em !important;
            margin-block-start: 0.5em !important;
            margin-bottom: 5px !important;
            margin-top: 5px !important;
        }

        .field {
            padding:5px;
            background-color: #ebebeb;
            border-radius: 5px;
            min-height:16px;
            height:16px;
            margin:0;
            display:block;
            width:100%;
            overflow:hidden;
        }

        .field-tall {
            min-height:40px
        }

        .ib {
            display:inline-block;
        }

        .w-07 {
            padding:0 5px;
            width:70px;
        }

        .w-1 {
            padding:0 5px;
            width:100px;
        }

        .w-2 {
            padding:0 5px;
            width:200px;
        }

        .w-25 {
            padding:0 5px;
            width:250px;
        }

        .w-3 {
            padding:0 5px;
            width:300px;
        }

        .w-35 {
            padding:0 5px;
            width:350px;
        }

        .w-4 {
            padding:0 5px;
            width:400px;
        }

        .w-5 {
            padding:0 5px;
            width:500px;
        }

        .w-6 {
            padding:0 5px;
            width:600px;
        }

        .w-7 {
            padding:0 5px;
            width:700px;
        }

        .w-8 {
            padding:0 5px;
            width:800px;
        }

        .m-0 {
            margin: 0px !important;
        }

        .mb-0 {
            margin-bottom: 0px !important;
        }

        .mb-5 {
            margin-bottom: 5px !important;
        }

        .mt-5 {
            margin-top: 5px !important;
        }

        .pt-5 {
            padding-top: 5px !important;
        }

        .mb-10 {
            margin-bottom: 10px !important;
        }

        .mb-15 {
            margin-bottom: 15px !important;
        }

        .mb-20 {
            margin-bottom: 20px !important;
        }

        .mt-0 {
            margin-top: 0px !important;
        }

        .mt-5 {
            margin-top: 5px !important;
        }

        .mt-10 {
            margin-top: 10px !important;
        }
        .content-table {
            width: calc(100% - 40px);
        }

        .content-table td {
            padding:5px;
        }

        .block {
            display:block;
            position: relative;
            overflow: auto;
        }

        .page-width {
            width:19cm;
        }

        .title-3 {
            background-color: #f44336;
            color: white;
            padding:5px;
            text-align: center;
            border-radius: 5px;
            font-size: 1.4em;
            margin-bottom: 15px;
            width: 705px;
        }

        .title-4 {
            background-color: #ccc;
            padding:5px;
            text-align: center;
            border-radius: 5px;
            margin-bottom: 5px;
            width: 705px;
        }

        .left {
            float: left;
        }

        .height-auto {
            height:auto;
        }

        .paid {
            font-size: 200px;
            position: absolute;
            color: red;
            opacity: 0.2;
            top:200px;
            left: 180px;
            transform: rotate(-30deg);
        }

        .main-title {
            margin-left:0px;
            margin-top:0;
        }
    </style>

</head>
<body>

<header>
    <img src="images/receipt-header.jpg" width="100%"/>
</header>
@yield('content')

<footer style="display:block; background-color: #fff; padding:15px; text-align: center; color: #353535">
    &copy; {{ date('Y') }} {{ config('app.url') }} - All rights reserved.
</footer>
</body>
</html>
