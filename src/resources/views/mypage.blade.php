@extends('layouts.app')

@section('content')
<div style="text-align: center; padding-top: 60px;">
    <h1>ようこそ、{{ Auth::user()->username }}さん！</h1>
    <p>ログインが完了しました 🎉</p>
</div>
@endsection