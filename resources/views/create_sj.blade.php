@extends('layouts.app')
@section('content')
<div class="container-full">
<div class="row">
<div class="col-md-6 col-md-offset-3"  style="border: 4px solid #a1a1a1;">
        <h2><center><font color="white">CREATE SJ</font></center></h2>
        <center>
        <form action="{{asset('create/sj')}}" class="form-horizontal" method="post">
        {{ csrf_field() }}
        <label for="">Tanggal Delivery</label>
        <input type="date" class="form-control" name="tanggal_delivery" />
        <label for="">Customer Name</label>
        <select name="customer_code" id="" class="form-control">
                @foreach($data as $row)
                <option value="{{$row->customer_code}}">{{$row->customer_name}}</option>
                @endforeach
        </select>
        <label for="">PDS Number</label>
        <input type="text" class="form-control" name="pdsnumber" />
        <label for="">SJ/DO AII</label>
        <input type="text" class="form-control" name="doaii" />
        <br>
        <input type="submit" value="Create" class="btn btn-md btn-success">
        <br> <hr>
        </center>
        </form>       
        </div>
</div>
</div>

@endsection
