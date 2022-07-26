<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Address;
use App\Models\Country;
use App\Models\State;
use App\Models\City;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\EventRequest;


class EventController extends Controller
{
    public function index(Request $request)
    {
        return view('events.index');
    }

    public function index_data(){
        $module_name = Event::join('addresses','addresses.event_id','events.id')->join('countries','countries.id','addresses.country')->join('states','states.id','addresses.state')->join('cities','cities.id','addresses.city')->select('events.*','addresses.*','countries.name as country_name','states.name as state_name','cities.name as city_name')->get();
        $data = $module_name;
        return DataTables::of($module_name)
        ->addColumn('address', function ($data){
            return $data->address_line1.' ,'.$data->address_line2.' ,'.$data->country_name.' ,'.$data->state_name.' ,'.$data->city_name.', '.$data->pincode;
        })
        ->addColumn('action', function ($data){
            return '<button class="btn btn-primary action" onclick="manageEvent(\'edit\','.$data->id.')"><i class="fa fa-edit"></i></button><button class="btn btn-danger action" onclick="manageEvent(\'delete\','.$data->id.')"><i class="fa fa-trash"></i></button>';
        })
        ->editColumn('updated_at', function ($data) {
            $diff = Carbon::now()->diffInHours($data->updated_at);
            if ($diff < 25) {
                return $data->updated_at->diffForHumans();
            } else {
                return $data->updated_at->isoFormat('LLLL');
            }
        })
        ->rawColumns(['action'])
        ->make(true);
    }

    public function store(EventRequest $request)
    {
        // $validated = $request->validated();
        $event_data = $request->only('name','time','date','guest_count');
        $event = Event::create($event_data);
        if($event == null){
            return ['success' => false, 'message' => 'Something was wrong',"status"=>400];
        }
        $request['event_id'] = $event->id;
        $address_data = $request->only('address_line1','address_line2','city','state','country','pincode','event_id');
        $address = Address::create($address_data);
        if($address == null){
            $event->delete();
            return ['success' => false, 'message' => 'Something was wrong',"status"=>400];
        }
        return ['success' => true, 'message' => 'Inserted Successfully',"status"=>200];
    }

    public function edit($id)
    {
        $event = Event::where(['id'=>$id])->first();
        if($event == null){
            return ['success' => false, 'message' => 'Something was wrong',"status"=>400];
        }
        $address = Address::with('country','state','city')->where(['event_id'=>$event->id])->first();
        if($address == null){
            return ['success' => false, 'message' => 'Something was wrong',"status"=>400];
        }
        $data = ['event'=>$event,'address'=>$address];
        return ['success' => true, 'message' => 'Data Found',"status"=>200,"data"=>$data];
    }

    public function update(EventRequest $request,$id)
    {
        // $validated = $request->validated();
        $event = Event::find($id);
        if($event != null){
            $event_data = $request->only('name','time','date','guest_count');
            $event->update($event_data);
            if($event == null){
                return ['success' => false, 'message' => 'Something was wrong',"status"=>400];
            }
            $address_data = $request->only('address_line1','address_line2','city','state','country','pincode');
            $address = Address::where(['event_id'=>$event->id])->update($address_data);
            return ['success' => true, 'message' => 'Update Successfully',"status"=>200];
        }
    }

    public function destroy($id)
    {
        $data = Event::where(['id'=>$id])->first();
        if($data == null){
            return ['success' => false, 'message' => 'Something was wrong',"status"=>400];
        }
        $data->delete();
        return ['success' => true, 'message' => 'Deleted Successfully',"status"=>200];
    }

    function countryList(){
        $countryLists = Country::where(['status'=>1])->get();
        if(!isset($countryLists[0])){
            return ['success' => false, 'message' => 'State Not Found In this country',"status"=>400];
        }
        return ['success' => true, 'message' => 'Country Fetched',"status"=>200,"data"=>$countryLists];
    }

    function stateList(Request $req){
        $validated = $req->validate([
            'country_id' => 'required|integer',
        ]);
        $country_id = $req->country_id;
        $stateList = State::where(['country_id'=>$country_id,'status'=>1])->get();
        if(!isset($stateList[0])){
            return ['success' => false, 'message' => 'State Not Found In this country',"status"=>400];
        }
        return ['success' => true, 'message' => 'State Fetched',"status"=>200,"data"=>$stateList];
    }

    function cityList(Request $req){
        $validated = $req->validate([
            'state_id' => 'required|integer',
        ]);
        $state_id = $req->state_id;
        $cityList = City::where(['state_id'=>$state_id,'status'=>1])->get();

        if(!isset($cityList[0])){
            return ['success' => false, 'message' => 'City Not Found In this State',"status"=>400];
        }
        return ['success' => true, 'message' => 'City Fetched',"status"=>200,"data"=>$cityList];
    }
}
