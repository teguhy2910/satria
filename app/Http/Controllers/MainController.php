<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Input;
use Auth;
use DB;
use App\sj;
use App\customer;
use App\invoice;
use App\sj_error;
use Carbon\Carbon;
use Excel;
use Illuminate\Support\Facades\Session;
use Datatables;
use Illuminate\Http\Request;

class MainController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function data_sj()
    {
        $applyBlankFilter = function($query, $column, $keyword) {
            $value = trim((string) $keyword);
            if ($value === '__BLANK__') {
                $query->where(function($q) use ($column) {
                    $q->whereNull($column)
                      ->orWhere($column, '')
                      ->orWhereRaw("TRIM($column) = ''");
                });
            } elseif ($value === '__NOT_BLANK__') {
                $query->whereNotNull($column)
                      ->whereRaw("TRIM($column) <> ''");
            }
        };

        $sjBalikKeyword = request()->input('sj_balik_filter', request()->input('columns.7.search.value', ''));
        $financeKeyword = request()->input('terima_finance_filter', request()->input('columns.8.search.value', ''));

        if(Auth::user()->name === 'finance1'||Auth::user()->name === 'finance2'||Auth::user()->name === 'finance3'){
        $data = sj::select('sjs.id','sjs.created_at','sjs.tanggal_delivery','sjs.customer_code','customers.customer_name','sjs.pdsnumber','sjs.doaii','sjs.sj_balik','sjs.terima_finance','sj_errors.updated_at')
        ->leftjoin('customers','customers.customer_code','sjs.customer_code')
        ->leftJoin('sj_errors','sj_errors.doaii','sjs.doaii')
            ->groupBy('sjs.doaii');
            $applyBlankFilter($data, 'sjs.sj_balik', $sjBalikKeyword);
            $applyBlankFilter($data, 'sjs.terima_finance', $financeKeyword);
            return Datatables::of($data)
            ->editColumn('tanggal_delivery', function($row) {
                return $row->tanggal_delivery ? Carbon::parse($row->tanggal_delivery)->format('Y-m-d') : '';
            })
            ->editColumn('sj_balik', function($row) {
                if (!empty($row->sj_balik)) {
                    return $row->sj_balik;
                }

                return $row->updated_at ? Carbon::parse($row->updated_at)->format('Y-m-d H:i') : '';
            })
            ->editColumn('terima_finance', function($row) {
                return $row->terima_finance;
            })
            ->addColumn('action', function ($data) {
                    return '<a class="btn btn-warning btn-xs" href="edit_sj/'.$data->id.'">Edit</a>
                    <a class="btn btn-danger btn-xs" href="delete_sj/'.$data->id.'">Del</a>
                    ';
                })
            ->make();
        }else{
        $data = sj::select('sjs.id','sjs.created_at','sjs.tanggal_delivery','sjs.customer_code','customers.customer_name','sjs.pdsnumber','sjs.doaii','sjs.sj_balik','sjs.terima_finance','sj_errors.updated_at')
        ->leftjoin('customers','customers.customer_code','sjs.customer_code')
        ->leftJoin('sj_errors','sj_errors.doaii','sjs.doaii')
	->where('sjs.doaii','like','%SJ/%')
	->groupBy('sjs.doaii');       
            $applyBlankFilter($data, 'sjs.sj_balik', $sjBalikKeyword);
            $applyBlankFilter($data, 'sjs.terima_finance', $financeKeyword);
            return Datatables::of($data)
            ->editColumn('tanggal_delivery', function($row) {
                return $row->tanggal_delivery ? Carbon::parse($row->tanggal_delivery)->format('Y-m-d') : '';
            })
            ->editColumn('sj_balik', function($row) {
                if (!empty($row->sj_balik)) {
                    return $row->sj_balik;
                }

                return $row->updated_at ? Carbon::parse($row->updated_at)->format('Y-m-d H:i') : '';
            })
            ->addColumn('action', function ($data) {
                    return '<a class="btn btn-warning btn-xs" href="edit_sj/'.$data->id.'">Edit</a>
                    ';
                })
            ->make();
        }
    }
    public function data_outstanding_sj()
    {
        $start_date=Carbon::now()->addDays(-7);                
            $data=sj::select('sjs.created_at','sjs.tanggal_delivery','sjs.customer_code','customers.customer_name','sjs.pdsnumber','sjs.doaii','sjs.sj_balik','sjs.terima_finance')
            ->join('customers','customers.customer_code','sjs.customer_code')
            ->where('sjs.tanggal_delivery','>=',$start_date)
            ->whereNull('sjs.terima_finance')
            ->groupBy('sjs.doaii');                        
        return Datatables::of($data)->make();
    }
    public function data_outstanding_sj_7_day()
    {
        $start_date=Carbon::now()->addDays(-7);        
        $data=sj::select('sjs.created_at','sjs.tanggal_delivery','sjs.customer_code','customers.customer_name','sjs.pdsnumber','sjs.doaii','sjs.sj_balik','sjs.terima_finance')
        ->join('customers','customers.customer_code','sjs.customer_code')
        ->where('sjs.tanggal_delivery','<=',$start_date)
        ->groupBy('sjs.doaii')
        ->whereNull('sjs.sj_balik');
        return Datatables::of($data)->make();
    }    
    public function data_outstanding_sj_7_day_finance()
    {
        $start_date=Carbon::now()->addDays(-7);        
        $data=sj::select('sjs.created_at','sjs.tanggal_delivery','sjs.customer_code','customers.customer_name','sjs.pdsnumber','sjs.doaii','sjs.sj_balik','sjs.terima_finance')
        ->join('customers','customers.customer_code','sjs.customer_code')
        ->where('sjs.tanggal_delivery','<=',$start_date)
        ->groupBy('sjs.doaii')
        ->whereNull('sjs.terima_finance')
        ->whereNotNull('sjs.sj_balik');
        return Datatables::of($data)->make();
    }    
    public function sj_outstanding_finance()
    {
        return view('sj_outstanding_finance');
    }
    public function index()
    {
        return redirect('/dashboard');
    }
    public function sj_error()
    {
        return view('sj_error');
    }
    public function data_sj_error()
    {
        $data = sj_error::select('id', 'doaii', 'user_scan', 'created_at');
        return Datatables::of($data)
            ->editColumn('created_at', function($row) {
                return $row->created_at ? \Carbon\Carbon::parse($row->created_at)->format('d/m/Y H:i:s') : '';
            })
            ->make();
    }
          
    public function dashboard()
    {
        return view('dashboard');
    }
    public function filter_view()
    {
        $from = $_POST['from'] ?? null;
        $to = $_POST['to'] ?? null;
        
        if (!$from || !$to) {
            Session::flash('danger', 'Tanggal filter tidak lengkap');
            return redirect('/dashboard');
        }
        
        // Parse date format: 2026-03-18
        $fromDate = Carbon::createFromFormat('Y-m-d', $from)->startOfDay();
        $toDate = Carbon::createFromFormat('Y-m-d', $to)->endOfDay();
        
        $data = sj::select('sjs.id','sjs.created_at','sjs.tanggal_delivery','sjs.customer_code','customers.customer_name','sjs.pdsnumber','sjs.doaii','sjs.sj_balik','sjs.terima_finance','sj_errors.updated_at')
                  ->leftjoin('customers','customers.customer_code','sjs.customer_code')
                  ->leftJoin('sj_errors','sj_errors.doaii','sjs.doaii')
                  ->whereBetween('sjs.tanggal_delivery', [$fromDate, $toDate])
                  ->groupBy('sjs.doaii')
                  ->get();
        
        return view('dashboard_filter', compact('data'));
    }
    public function sj_dashboard()
    {        
        return view('sj_dashboard');
    }
    public function sj_outstanding()
    {
        return view('sj_outstanding');
    }  
    public function upload_sj_dashboard()
    {
        return view('upload_sj_dashboard');
    }
    public function invoice()
    {
        return view('upload_invoice');
    }    
    public function upload_sj_dashboard_store()
    {        
	ini_set('memory_limit', '2048M');
        if(Input::hasFile('sj')){
            $path = Input::file('sj')->getRealPath();
            $data = Excel::load($path)->get();
            if(!empty($data) && $data->count()){
                foreach ($data as $key => $value) {
                    $insert[] = 
                    [
                    'tanggal_delivery' => $value->acgi_date,
                    'customer_code' => $value->sold_to,
                    'pdsnumber' => $value->external_delivery_id,
                    'doaii' => $value->nomor_surat_jalan,                    
                    ];
                }
                $insert=array_map("unserialize", array_unique(array_map("serialize", $insert)));
                $insert=array_filter($insert, function($value) { return !is_null($value['doaii']) && $value['doaii'] !== ''; });
                if(!empty($insert)){
                    foreach($insert as $row) {
                    if($row['tanggal_delivery']!=null){
                    sj::create($row);                    
                    }
                    }
                    $total_upload="Sukses Scan SJ, Total Upload=".count($insert)." SJ";
                    Session::flash('message', $total_upload); 
                }else{
                    Session::flash('danger', 'Gagal Upload SJ');
                }
            }
        }
        Session::flash('danger', 'Something Wrong Contact Administrator'); 
        return redirect('/sj/dashboard');
    }
    public function customer_store(Request $request)
    {
        // If file is uploaded, process Excel import
        if(Input::hasFile('customer')){
            $path = Input::file('customer')->getRealPath();
            $data = Excel::load($path)->get();
            if(!empty($data) && $data->count()){
                foreach ($data as $key => $value) {
                    $insert[] = 
                    [
                    'customer_code' => $value->customer_code,
                    'customer_name' => $value->customer_name,
                    ];
                    }
                }               
                if(!empty($insert)){
                    foreach($insert as $row) {                    
                        customer::create($row);                    
                    }
                    Session::flash('message', 'Sukses Upload Customer, Total=' . count($insert));
                }else{
                    Session::flash('danger', 'Gagal Upload, File kosong atau format salah');
                }
        }
        // If direct form submission from modal
        elseif($request->has('customer_code') && $request->has('customer_name')){
            $this->validate($request, [
                'customer_code' => 'required|string',
                'customer_name' => 'required|string',
            ]);
            
            customer::create([
                'customer_code' => $request->input('customer_code'),
                'customer_name' => $request->input('customer_name'),
            ]);
            
            return response()->json(['success' => true, 'message' => 'Customer berhasil ditambahkan']);
        }else{
            Session::flash('danger', 'Something Wrong Contact Administrator');
        }
        
        return redirect('/customer');
    }
    public function data_customer()
    {
        $data = customer::select('id', 'customer_code', 'customer_name', 'created_at');
        return Datatables::of($data)
            ->editColumn('created_at', function($row) {
                return $row->created_at ? \Carbon\Carbon::parse($row->created_at)->format('d/m/Y H:i') : '';
            })
            ->addColumn('action', function ($row) {
                return '<a class="btn btn-warning btn-xs" href="edit_customer/'.$row->id.'">Edit</a>
                <a class="btn btn-danger btn-xs" href="delete_customer/'.$row->id.'">Delete</a>
                ';
            })
            ->rawColumns(['action'])
            ->make();
    }
    public function customer()
    {
        $data = customer::all();
        return view('customer', compact('data'));
    }
    public function customer_create()
    {
        return view('customer_create');
    }
    public function customer_store_form(Request $request)
    {
        $this->validate($request, [
            'customer_code' => 'required|string',
            'customer_name' => 'required|string',
        ]);
        
        customer::create([
            'customer_code' => $request->input('customer_code'),
            'customer_name' => $request->input('customer_name'),
        ]);
        
        Session::flash('message', 'Customer berhasil ditambahkan');
        return redirect('/customer');
    }
    public function customer_edit($id)
    {
        $data = customer::find($id);
        if (!$data) {
            Session::flash('danger', 'Customer tidak ditemukan');
            return redirect('/customer');
        }
        return view('customer_edit', compact('data'));
    }
    public function customer_update(Request $request, $id)
    {
        $customer = customer::find($id);
        if (!$customer) {
            Session::flash('danger', 'Customer tidak ditemukan');
            return redirect('/customer');
        }
        
        $this->validate($request, [
            'customer_code' => 'required|string',
            'customer_name' => 'required|string',
        ]);
        
        $customer->update([
            'customer_code' => $request->input('customer_code'),
            'customer_name' => $request->input('customer_name'),
        ]);
        
        Session::flash('message', 'Customer berhasil diupdate');
        return redirect('/customer');
    }
    public function customer_delete($id)
    {
        $customer = customer::find($id);
        if (!$customer) {
            Session::flash('danger', 'Customer tidak ditemukan');
            return redirect('/customer');
        }
        
        $customer->delete();
        Session::flash('message', 'Customer berhasil dihapus');
        return redirect('/customer');
    }
    public function update_sj_balik_ppic_upload()
    {
        if(Input::hasFile('update_sj_balik_ppic')){
            $path = Input::file('update_sj_balik_ppic')->getRealPath();
            $data = Excel::load($path)->get();
            if(!empty($data) && $data->count()){
                foreach ($data as $key => $value) {
                    $cek=sj::where('doaii',$value->doaii)->whereNotNull('doaii')->get();
                    if($cek->toArray()!=null){
                    $insert[] = 
                    [
                    'doaii' => $value->doaii,
                    ];
                    }else{
                    $error[] = 
                    [
                    'doaii' => $value->doaii,
                    ];  
                    }                    
                } 
                $insert=array_filter($insert, function($value) { return !is_null($value['doaii']) && $value['doaii'] !== ''; });     
                $no=0;
                $noo=0;
                if(!empty($insert)){                    
                    foreach($insert as $row) {
                    $cek=sj::where('doaii',$row)->first();
                    if($cek['sj_balik']===null){                    
                    $no++;
                    $sukses_upload[] = 
                    [
                    'doaii' => $cek['doaii'],
                    ];
                    sj::where('doaii',$row)->update(['sj_balik' =>\Carbon\Carbon::now()]);  
                    $total_upload="Sukses Scan SJ, Total Upload=".$no." SJ";
                    Session::flash('message', $total_upload);    
                    }else{
                        $sudah_balik[] = 
                            [
                            'doaii' => $cek['doaii'],
                            ];
                        $noo++;
                        Session::flash('danger', 'Gagal Upload ' .$noo. ' SJ Sudah Balik');  
                    }                                                            
                } 
                }else{
                    Session::flash('danger', 'Gagal Upload SJ');
                }
            }
        }else{
        Session::flash('danger', 'Something Wrong Contact Administrator'); 
        }
        if(!empty($error)&&!empty($sudah_balik)&&!empty($sukses_upload)){
        Excel::create('SJ Error', function($excel) use($error,$sudah_balik,$sukses_upload) {
            $excel->sheet('SJ Tidak Ada Di Master', function($sheet) use($error) {
                $sheet->fromArray($error);
            });
            $excel->sheet('SJ Sudah Balik', function($sheet) use($sudah_balik) {
                $sheet->fromArray($sudah_balik);
            });
            $excel->sheet('SJ Sukses Upload', function($sheet) use($sukses_upload) {
                $sheet->fromArray($sukses_upload);
            });

        })->export('xlsx');
        }elseif(!empty($error)&&!empty($sukses_upload)){
            Excel::create('SJ Error', function($excel) use($error,$sukses_upload) {
                $excel->sheet('SJ Tidak Ada Di Master', function($sheet) use($error) {
                    $sheet->fromArray($error);
                });
                $excel->sheet('SJ Sukses Upload', function($sheet) use($sukses_upload) {
                    $sheet->fromArray($sukses_upload);
                });
    
            })->export('xlsx');
            }
            elseif(!empty($sudah_balik)&&!empty($sukses_upload)){
                Excel::create('SJ Error', function($excel) use($sudah_balik,$sukses_upload) {
                    $excel->sheet('SJ Sudah Balik', function($sheet) use($sudah_balik,$sukses_upload) {
                        $sheet->fromArray($sudah_balik);
                    });
                    $excel->sheet('SJ Sukses Upload', function($sheet) use($sukses_upload) {
                        $sheet->fromArray($sukses_upload);
                    });
        
                })->export('xlsx');
                }
        elseif(!empty($error)&&!empty($sudah_balik)){
            Excel::create('SJ Error', function($excel) use($error,$sudah_balik) {
                $excel->sheet('SJ Tidak Ada Di Master', function($sheet) use($error,$sudah_balik) {
                    $sheet->fromArray($error);
                });
                $excel->sheet('SJ Sudah Balik', function($sheet) use($sudah_balik) {
                    $sheet->fromArray($sudah_balik);
                });
    
            })->export('xlsx');
            }
        elseif(!empty($error)){
            Excel::create('SJ Error', function($excel) use($error) {
                $excel->sheet('SJ Tidak Ada Di Master', function($sheet) use($error) {
                    $sheet->fromArray($error);
                });
    
            })->export('xlsx'); 
        }elseif(!empty($sudah_balik)){
            Excel::create('SJ Error', function($excel) use($sudah_balik) {
                $excel->sheet('SJ Sudah Balik', function($sheet) use($sudah_balik) {
                    $sheet->fromArray($sudah_balik);
                });
    
            })->export('xlsx'); 
        }
        return redirect('/sj/dashboard');
    }
    public function update_invoice_upload()
    {
        if(Input::hasFile('invoice')){
            $path = Input::file('invoice')->getRealPath();
            $data = Excel::load($path)->get();
            if(!empty($data) && $data->count()){
                foreach ($data as $key => $value) {
                    $insert[] = 
                    [
                    'doaii' => $value->nomor_surat_jalan,
                    'invoice' => $value->no_invoice,
                    ];                                        
                    }
                    $insert=array_filter($insert, function($value) { return !is_null($value['invoice']) && $value['invoice'] !== ''; });
                    foreach($insert as $row) {                                            
                    sj::where('doaii',$row['doaii'])->update(['invoice' =>$row['invoice']]);  
                    }                                                            
                }               
        }else{
        Session::flash('danger', 'Something Wrong Contact Administrator'); 
        }        
        return redirect('/dashboard');
    }
    public function data_invoice()
    {
        $data = sj::select('invoice',DB::raw('COUNT(terima_finance) as terima_finance_count'),
        DB::raw('COUNT(doaii) as do_aii_count'))
        ->groupBy('invoice')
        ->havingRaw('count(terima_finance)!=count(doaii)');      
        return Datatables::of($data)        
        ->make();
    }  
    public function invoicing()
    {                
        return view('invoicing');
    }
    public function sj_balik()
    {
        return view('sj_balik');
    }
    public function sj_balik_store()
    {
        if (sj::where('doaii', $_POST['doaii'])->count('doaii')==null) {
            $sj_error = new sj_error;    
            $sj_error->doaii = $_POST['doaii'];    
            $sj_error->user_scan = Auth::user()->name;
            $sj_error->save();
            Session::flash('danger', 'SJ/DO Error !!! Nomor SJ/DO '.$_POST['doaii']); 
            return redirect('/sj_balik');
        }elseif(sj::where('doaii', $_POST['doaii'])->count('sj_balik')!=null){
            Session::flash('danger', 'SJ Sudah BALIK !!! Nomor SJ/DO '.$_POST['doaii']); 
            return redirect('/sj_balik');
        }else{
        sj::where('doaii', $_POST['doaii'])
             ->update(
                ['sj_balik' =>\Carbon\Carbon::now(),
                 'user_ppic_scan' => Auth::user()->name
                ]);
             Session::flash('message', 'Sukses Simpan Nomor DO/SJ = '.$_POST['doaii']); 
             return redirect('/sj_balik');
             }
        
    }    
    public function terima_finance()
    {
        $data=sj::groupBy('doaii')->get();
        return view('terima_finance',compact('data'));
    }    
    public function update_fin_upload()
    {
        if(Input::hasFile('update_fin_upload')){
            $path = Input::file('update_fin_upload')->getRealPath();
            $data = Excel::load($path)->get();
            if(!empty($data) && $data->count()){
                foreach ($data as $key => $value) {
                    $cek=sj::where('doaii',$value->doaii)->whereNotNull('doaii')->get();
                    if($cek->toArray()!=null){
                    $insert[] = 
                    [
                    'doaii' => $value->doaii,
                    ];
                    }else{
                    $error[] = 
                    [
                    'doaii' => $value->doaii,
                    ];  
                    }                    
                } 
                $insert=array_filter($insert, function($value) { return !is_null($value['doaii']) && $value['doaii'] !== ''; });     
                $no=0;
                $noo=0;
                if(!empty($insert)){                    
                    foreach($insert as $row) {
                    $cek=sj::where('doaii',$row)->first();
                    if($cek['terima_finance']===null){                    
                    $no++;
                    $sukses_upload[] = 
                    [
                    'doaii' => $cek['doaii'],
                    ];
                    sj::where('doaii',$row)->update(['terima_finance' =>\Carbon\Carbon::now()]);  
                    $total_upload="Sukses Scan SJ, Total Upload=".$no." SJ";
                    Session::flash('message', $total_upload);    
                    }else{
                        $terima_finance[] = 
                            [
                            'doaii' => $cek['doaii'],
                            ];
                        $noo++;
                        Session::flash('danger', 'Gagal Upload ' .$noo. ' SJ Sudah Kirim Finance');  
                    }                                                            
                } 
                }else{
                    Session::flash('danger', 'Gagal Upload SJ');
                }
            }
        }else{
        Session::flash('danger', 'Something Wrong Contact Administrator'); 
        }
        if(!empty($error)&&!empty($terima_finance)&&!empty($sukses_upload)){
        Excel::create('SJ Error', function($excel) use($error,$terima_finance,$sukses_upload) {
            $excel->sheet('SJ Tidak Ada Di Master', function($sheet) use($error) {
                $sheet->fromArray($error);
            });
            $excel->sheet('SJ Sudah Terima Finance', function($sheet) use($terima_finance) {
                $sheet->fromArray($terima_finance);
            });
            $excel->sheet('SJ Sukses Upload', function($sheet) use($sukses_upload) {
                $sheet->fromArray($sukses_upload);
            });

        })->export('xlsx');
        }elseif(!empty($error)&&!empty($sukses_upload)){
            Excel::create('SJ Error', function($excel) use($error,$sukses_upload) {
                $excel->sheet('SJ Tidak Ada Di Master', function($sheet) use($error) {
                    $sheet->fromArray($error);
                });
                $excel->sheet('SJ Sukses Upload', function($sheet) use($sukses_upload) {
                    $sheet->fromArray($sukses_upload);
                });
    
            })->export('xlsx');
            }
            elseif(!empty($terima_finance)&&!empty($sukses_upload)){
                Excel::create('SJ Error', function($excel) use($terima_finance,$sukses_upload) {
                    $excel->sheet('SJ Sudah Terima Finance', function($sheet) use($terima_finance,$sukses_upload) {
                        $sheet->fromArray($terima_finance);
                    });
                    $excel->sheet('SJ Sukses Upload', function($sheet) use($sukses_upload) {
                        $sheet->fromArray($sukses_upload);
                    });
        
                })->export('xlsx');
                }
        elseif(!empty($error)&&!empty($terima_finance)){
            Excel::create('SJ Error', function($excel) use($error,$terima_finance) {
                $excel->sheet('SJ Tidak Ada Di Master', function($sheet) use($error,$terima_finance) {
                    $sheet->fromArray($error);
                });
                $excel->sheet('SJ Sudah Terima Finance', function($sheet) use($terima_finance) {
                    $sheet->fromArray($terima_finance);
                });
    
            })->export('xlsx');
            }
        elseif(!empty($error)){
            Excel::create('SJ Error', function($excel) use($error) {
                $excel->sheet('SJ Tidak Ada Di Master', function($sheet) use($error) {
                    $sheet->fromArray($error);
                });
    
            })->export('xlsx'); 
        }elseif(!empty($terima_finance)){
            Excel::create('SJ Error', function($excel) use($terima_finance) {
                $excel->sheet('SJ Sudah Terima Finance', function($sheet) use($terima_finance) {
                    $sheet->fromArray($terima_finance);
                });
    
            })->export('xlsx'); 
        }
        return redirect('/sj/dashboard');
    }

    public function terima_finance_store()
    {
	    if (sj::where('doaii', $_POST['doaii'])->count('doaii')==null) 
            {
                $sj_error = new sj_error;    
                $sj_error->doaii = $_POST['doaii'];    
                $sj_error->user_scan = Auth::user()->name;
                $sj_error->save();
                Session::flash('danger', 'SJ ERROR, NOMOR SJ = '.$_POST['doaii']); 
		return redirect('/terima_finance');
            }else{	
            $data=sj::where('doaii', $_POST['doaii'])
             ->update(['terima_finance' =>\Carbon\Carbon::now()]);
             $invoice=sj::select('invoice')->where('doaii', $_POST['doaii'])->first();
             Session::flash('message', 'Sukses Simpan Nomor Invoice = '.$invoice->invoice); 
             return redirect('/terima_finance')->with(['success' => 'Berhasil']);         
    }
}    
    
    public function del_ppic($id)
    {
        sj::where('id',$id)->delete();
        Session::flash('warning', 'PDS NUMBER berhasil dihapus');
        return redirect('/dashboard');
    }
    public function sj_update($id)
    {
        $data=sj::where('id',$id)->first();
        return view('sj_update',compact('data'));
    }
    public function sj_update_store($id)
    {
        sj::where('id',$id)->update(request()->except(['_token']));
        Session::flash('warning', 'EDIT data BERHASIL Bro');
        return redirect('dashboard');
    }     
    public function create_sj()
    {
        $data=customer::all();
        return view('create_sj',compact('data'));
    }
    public function create_sj_store()
    {
        sj::create(request()->all());
        return redirect('create/sj');
    }
    public function download_sj()
    {
        $sj = sj::all();
        Excel::create('sj', function($excel) use($sj) {
            $excel->sheet('Sheet 1', function($sheet) use($sj) {
                $sheet->fromArray($sj);
            });
        })->export('xlsx');
    }

}
