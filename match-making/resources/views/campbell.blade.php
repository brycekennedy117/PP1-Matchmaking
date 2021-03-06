@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Profile</div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif
                        <div>
                            <div>
                                <h2 class="" value="name">Campbell</h2>
                                <h2 value="age">20+</h2>
                            </div>
                            <div>
                                <img src="https://content-static.upwork.com/uploads/2014/10/02123010/profilephoto_goodcrop.jpg" alt="match"/>
                                <info value="description">
                                    I am Campbell Brobbel
                                </info>
                            </div>
                            <button class="btn btn-lg btn-primary btn-block" href="">Send message</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
