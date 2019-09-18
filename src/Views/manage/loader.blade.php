@extends('layout')

@section('content')
<div id="opx-manage" class="opx-manage" v-bind="{class: { loaded: loaded }}">
    <div class="opx-manage__navigation">
        <opx-navigation :navigation="navigation"></opx-navigation>
    </div>
    <div class="opx-manage__body">
        <keep-alive>
            <router-view :key="$route.path"></router-view>
        </keep-alive>
    </div>

    <div id="opx-manage-loader">
        <svg id="opx-manage-loader__container" viewBox="0 0 100 100">
            <polygon class="part-0 part-color" points="30.1,45 30.1,38.5 35.7,35.3 28.3,22.5 15.4,30 15.4,45"></polygon>
            <polygon class="part-1 part-color" points="44.4,30.3 50,27 55.6,30.3 63,17.5 50,10 37,17.5"></polygon>
            <polygon class="part-2 part-color" points="64.3,35.3 69.9,38.5 69.9,45 84.6,45 84.6,30 71.7,22.5"></polygon>
            <polygon class="part-3 part-color" points="69.9,55 69.9,61.5 64.3,64.8 71.7,77.5 84.6,70 84.6,55"></polygon>
            <polygon class="part-4 part-color" points="55.6,69.8 50,73 44.4,69.8 37,82.5 50,90 63,82.5"></polygon>
            <polygon class="part-5 part-color" points="35.7,64.8 30.1,61.5 30.1,55 15.4,55 15.4,70 28.3,77.5"></polygon>
        </svg>
    </div>
</div>
@endsection

@push('styles')
    <style>
        body, html {margin: 0; padding: 0; width: 100%; height: 100%;}
        #opx-manage-loader {position: fixed; top: 0; left: 0; width: 100%; height: 100%; align-items: center; display: flex; justify-content: center; background-color: rgba(255,255,255,1);}
        .loaded #opx-manage-loader {display: none;}
        #opx-manage-loader__container {width: 20vh; height: 20vh;}
        #opx-manage-loader__container .part-color{fill:#FF931E;}
        #opx-manage-loader__container * {transform-origin:50% 50%; animation: loader-animation; animation-duration: 0.6s; animation-timing-function: linear; animation-iteration-count: infinite;}
        #opx-manage-loader__container .part-0 {animation-delay: 0.1s;}
        #opx-manage-loader__container .part-1 {animation-delay: 0.2s;}
        #opx-manage-loader__container .part-2 {animation-delay: 0.3s;}
        #opx-manage-loader__container .part-3 {animation-delay: 0.4s;}
        #opx-manage-loader__container .part-4 {animation-delay: 0.5s;}
        #opx-manage-loader__container .part-5 {animation-delay: 0.6s;}
        @-webkit-keyframes loader-animation {
            0%   {transform: scale(1)}
            12.5%  {transform: scale(1.2)}
            25%  {transform: scale(1)}
            100% {transform: scale(1)}
        }
        @keyframes loader-animation {
            0%   {transform: scale(1)}
            12.5%  {transform: scale(1.2)}
            25%  {transform: scale(1)}
            100% {transform: scale(1)}
        }
    </style>
@endpush