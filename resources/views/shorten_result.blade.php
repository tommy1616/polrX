@extends('layouts.base')

@section('css')
<link rel='stylesheet' href='/css/shorten_result.css' />
@endsection

@section('content')
<div class="polrx-page-card" style="max-width: 640px; margin: 0 auto; text-align: center;">
    <h3 style="margin-top: 0; margin-bottom: 20px;">短網址已建立</h3>
    <div class="input-group" style="margin-bottom: 20px;">
        <input type='text' class='result-box form-control' value='{{$short_url}}' id='short_url' readonly />
        <div class='input-group-addon' id='clipboard-copy' data-clipboard-target='#short_url' data-toggle='tooltip' data-placement='bottom' data-title='已複製' style="cursor: pointer;">
            <i class='fa fa-clipboard' aria-hidden='true' title='複製到剪貼簿'></i>
        </div>
    </div>
    <div style="display: flex; gap: 10px; justify-content: center; flex-wrap: wrap;">
        <a id="generate-qr-code" class='btn btn-default'>產生 QR Code</a>
        <a href='{{route('index')}}' class='btn btn-primary'>再縮一筆</a>
    </div>

    <div class="qr-code-container" style="margin-top: 20px;"></div>
</div>
@endsection


@section('js')
<script src='/js/qrcode.min.js'></script>
<script src='/js/clipboard.min.js'></script>
<script src='/js/shorten_result.js'></script>
@endsection
