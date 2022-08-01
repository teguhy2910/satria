@extends('layouts.app')
@section('content')
<div class="container-full">
    <div class="row">        
        <div class="col-md-12">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                <li class="active"><a href={{asset("/data_outstanding_sj_7_day")}}><font face="calibri" color="black"><big><big><big>Outstanding SJ > 7 Hari </big></big></big> </font> <span class="label label-success"></span></a></li>
                </ul>
                
                <div class="panel-body">  
                <a href="{{asset("/terima_finance")}}" class="btn btn-md btn-warning">Scan Disini >> SJ/DO From Customer</a>
                    <br><br>
                    <table id="sj_ppic_more_7_days_finance" class="table table-bordered table-condensed dt-responsive" width="100%">                    
                <thead> 
                <tr class="info">
                <th><small>TANGGAL WAKTU UPLOAD</small></th>    
                <th><small>TANGGAL_DELIVERY</small></th>
                <th><small>CUSTOMER_CODE</small></th>
                <th><small>CUSTOMER_NAME</small></th>
                <th><small>PDSNUMBER</small></th>
                <th><small>DOAII</small></th>
                <th><small>SJ BALIK</small></th>
                <th><small>FINANCE</small></th>           
            </tr>
        </thead>                    
            </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
