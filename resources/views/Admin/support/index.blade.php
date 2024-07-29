@extends('admin.master')
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
                                        <h6 class="ms-3 mt-2">User Messages</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="chat-history">
                            <ul class="m-b-0">
                                @foreach ($messages as $message)
                                    <li class="clearfix">
                                        <div class="message other-message float-start">{{ $message->message }}</div>
                                    </li>
                                @endforeach
                                @livewire('chat', ['adminId' => Auth::guard('admin')->user()->id])
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
