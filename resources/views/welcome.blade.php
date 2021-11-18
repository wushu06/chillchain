@extends('layouts.app')

@section('content')

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <h1>{{__('Shippers')}}</h1>
                <div>
                    <shippers-table/>
                </div>
            </div>
        </div>
    </div>

@endsection
