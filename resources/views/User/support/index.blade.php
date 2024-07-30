@extends('user.master')
@section('support-active','active')
@section('title', 'Contact Support')
<link rel="stylesheet" href="{{ asset('assets') }}/css/chat.css">
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="container">
            <div class="row clearfix">
                <div class="col-lg-12">
                    <div class="chat">
                        <div class="chat-header clearfix">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="chat-about d-flex">
                                        <div wire:offline.remove class="avatar avatar-online">
                                            <img src="{{ Auth::user()->avatar }}" alt
                                                class="w-px-40 h-px-40 rounded-circle" />
                                        </div>
                                        <h6 class="ms-3 mt-2">{{ Auth::user()->name }}</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="chat-history">
                            <ul class="m-b-0">
                                {{-- @foreach ($messages as $message)
                                    <li class="clearfix">
                                        <div class="message my-message">{{ $message->message }}</div>
                                    </li>
                                @endforeach --}}
                                @livewire('user.support.chat')
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
