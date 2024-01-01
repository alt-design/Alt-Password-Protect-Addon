@extends('statamic::layout')

@section('content')
    <div>
        <publish-form
            title="Alt Password Protect Settings"
            action="{{ cp_route('alt-password-protect.update') }}"
            :blueprint='@json($blueprint)'
            :meta='@json($meta)'
            :values='@json($values)'
        ></publish-form>
    </div>
@endsection
