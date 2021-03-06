@extends('admin.layout')

@section('content')
    
  {!! Form::model($settings, ['url' => 'admin/update-settings', 'files' => true]) !!}
      
      {!! Html::panelOpen('<strong>Application Settings</strong>') !!}
        {!! Html::panelBodyOpen() !!}

              @include('admin.modules.general-settings')
              @include('admin.modules.api-keys')
              @include('admin.modules.web-app-enhancements')
              @include('admin.modules.social-media-connections')
              @include('admin.modules.company-details')
              @include('admin.modules.subscriptions-payment-widget')
              
        {!! Html::panelBodyClose() !!}
      {!! Html::panelClose() !!}
  {!! Form::close() !!}

  {!! Form::model($settings, ['url' => 'admin/update-settings']) !!}
      
      {!! Html::panelOpen('<strong>Legal Mumbo Jumbo</strong>') !!}
        {!! Html::panelBodyOpen() !!}

          @include('admin.modules.terms-of-use')
          @include('admin.modules.privacy-policy')
              
        {!! Html::panelBodyClose() !!}
      {!! Html::panelClose() !!}
  {!! Form::close() !!}  

@endsection