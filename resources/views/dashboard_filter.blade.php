@extends('layouts.app')
@section('content')
<div class="container-full">
    <div class="row">        
        <div class="col-md-12">
            @if(Session::has('message'))
            <p class="alert {{ Session::get('alert-class', 'alert-info') }}">{{ Session::get('message') }}</p>
            @endif
            @if(Session::has('warning'))
            <p class="alert {{ Session::get('alert-class', 'alert-danger') }}">{{ Session::get('warning') }}</p>
            @endif
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                <li class="active"><a class=""><big><big><big><font face="calibri">Surat Jalan </font></big></big></big> <span class="label label-warning"></span></a></li>
                </ul>
                <div class="panel-body">
                    <table id="sj_filter" class="table table-bordered table-condensed table-hover dt-responsive" width="100%">
                <thead>          
                       
                <tr class="info">                
                <th><small>ID</small></th>    
                <th><small>TANGGAL WAKTU UPLOAD</small></th>    
                <th><small>TANGGAL_DELIVERY</small></th>    
                <th><small>CUSTOMER_NAME</small></th>
                <th><small>PDSNUMBER</small></th>
                <th><small>DOAII</small></th>
                <th><small>SJ BALIK</small></th>
                <th><small>FINANCE</small></th>
                </tr>
                </thead> 
                @foreach($data as $row)
                <tr>
                <td>{{$row->id}}</td>
                <td>{{$row->created_at}}</td>
                <td>{{$row->tanggal_delivery}}</td>
                <td>{{$row->customer_name}}</td>
                <td>{{$row->pdsnumber}}</td>
                <td>{{$row->doaii}}</td>
                <td>{{$row->sj_balik}}</td>
                <td>{{$row->terima_finance}}</td>
                </tr>
                @endforeach
                          
            </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
