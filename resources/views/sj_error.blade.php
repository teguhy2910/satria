@extends('layouts.app')
@section('content')
<div class="container-full">
    <div class="row">        
        <div class="col-md-12">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                <li class="active"><a class=""><big><big><big><font face="calibri">Surat Jalan </font></big></big></big> <span class="label label-warning"></span></a></li>
                </ul>
                <div class="panel-body">                            
                <table id="sj_filter" class="table table-bordered table-condensed table-hover dt-responsive" width="100%">
                <thead>                 
                <tr class="info">
                <th><small>NO SJ/DO</small></th>
                <th><small>User PPIC Scan</small></th>
                <th><small>Waktu Scan</small></th>                
                </tr>
                </thead>
                <tbody>
                    @foreach($data as $row)
                    <tr>
                        <td>{{$row->doaii}}</td>
                        <td>{{$row->user_scan}}</td>
                        <td>{{$row->created_at}}</td>
                    </tr>
                    @endforeach
                </tbody>                    
            </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

