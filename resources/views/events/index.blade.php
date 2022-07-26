@extends('layouts.app')

@section('content')
<div class="container">
  <h2>Event Details</h2>
  <div class="fullwith text-right">
    <button class="btn btn-primary" id="add_event" onclick="manageEvent('add','')">Add Event</button>
  </div>
  <table id="datatable" class="table table-bordered table-hover table-responsive-sm">
    <thead>
        <tr>
            <th>#</th>
            <th>Event Name</th>
            <th>Event Time</th>
            <th>Event Date</th>
            <th>Total Guest</th>
            <th>Address</th>
            <th>Updated At</th>
            <th class="text-end">Action</th>
        </tr>
    </thead>
</table>
</div>
<div class="model-form" id="modelform" style="display: none">
    <div class="fullwith text-right">
        <button class="btn btn-primary" id="clse_event" onclick="closeModal()">Close</button>
      </div>
    <form>
        <input type="hidden" name="id" value="" id="event_id">
        <div class="form-group">
            <input type="text" class="form-control" id="eventname" placeholder="Event Name*" name="name">
            <div class="errormsg" id="div_name"></div>
        </div>
        <div class="form-group">
            <input type="text" class="form-control" id="eventdate" placeholder="Event Date*" name="date" readonly>
            <div class="errormsg" id="div_date"></div>
        </div>
        <div class="form-group">
            <input type="text" class="form-control" id="eventtime" placeholder="Event Time*" name="time" readonly>
            <div class="errormsg" id="div_time"></div>
        </div>
        <div class="form-group">
            <input type="number" class="form-control" onkeypress="return isNumber(event)" id="totalGuest" placeholder="Total Guest*" name="totalGuest">
            <div class="errormsg" id="div_totalGuest"></div>
        </div>
        <div class="form-group">
            <input type="text" class="form-control" id="addressLine1" placeholder="Address Line 1*" name="addressLine1">
            <div class="errormsg" id="div_addressLine1"></div>
        </div>
        <div class="form-group">
            <input type="text" class="form-control" id="addressLine2" placeholder="Address Line 2" name="addressLine2" >
            <div class="errormsg"></div>
        </div>
        <div class="form-group">
            <select name="country" id="country" class="form-control select2">
                <option value="" selected>Select Country</option>
            </select>
            <div class="errormsg" id="div_country"></div>
        </div>
        <div class="form-group">
            <select name="state" id="state" class="form-control select2">
                <option value="">Select Country First</option>
            </select>
            <div class="errormsg" id="div_state"></div>
        </div>
        <div class="form-group">
            <select name="city" id="city" class="form-control select2">
                <option value="">Select State First</option>
            </select>
            <div class="errormsg" id="div_city"></div>
        </div>
        <div class="form-group">
            <input type="number" class="form-control" id="pincode" onkeypress="return isNumber(event)" placeholder="Pin Code*" name="pincode">
            <div class="errormsg" id="div_pincode"></div>
        </div>
        <button type="button" class="btn btn-primary" onclick="saveData()">Submit</button>
      </form>
</div>
@endsection
@push ('after-styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.12.1/css/jquery.dataTables.min.css">
@endpush
@push ('after-scripts')
<script type="text/javascript" src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
<script type="text/javascript">
var defaultTime = "9:00 AM";
function isNumber(evt) {
    evt = (evt) ? evt : window.event;
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    if (charCode > 31 && (charCode < 48 || charCode > 57)) {
        // alert("Please enter only Numbers.");
        return false;
    }
    return true;
}
function formClearInput(){
    document.getElementById('eventname').value = "";
    document.getElementById('eventtime').value = "";
    document.getElementById('eventdate').value = "";
    document.getElementById('totalGuest').value = "";
    document.getElementById('addressLine1').value = "";
    document.getElementById('addressLine1').value = "";
    document.getElementById('pincode').value = "";
    document.getElementById('event_id').value = "";
}
function manageEvent(type,id){
    if(type=='add'){
        document.getElementById('modelform').style.display='block';
    }
    if(type=='edit'){
        jQuery.ajax({
            url: "event/"+id+"/edit",
            method: 'get',
            success: function(result){
                if(result.status == 200){
                    formClearInput();
                    var eventData = result.data.event;
                    var addressData = result.data.address;
                    defaultTime = "10:00 AM";
                    document.getElementById('event_id').value = eventData.id;
                    document.getElementById('eventname').value = eventData.name;
                    document.getElementById('eventtime').value = eventData.time;
                    document.getElementById('eventdate').value = eventData.date;
                    document.getElementById('totalGuest').value = eventData.guest_count;
                    document.getElementById('addressLine1').value = addressData.address_line1;
                    document.getElementById('addressLine2').value = addressData.address_line2;

                    callCountryApi(addressData.country.id);
                    callStateApi(addressData.country.id,addressData.state.id);
                    callCityApi(addressData.state.id,addressData.city.id);
                    document.getElementById('pincode').value = result.data.address.pincode;
                    document.getElementById('modelform').style.display='block';
                }
                else{
                    toastr["error"](result.message)
                }
            }
        });
    }
    if(type=='delete'){
        jQuery.ajax({
            url: "event/"+id,
            method: 'DELETE',
            data:{"_token": "{{ csrf_token() }}"},
            success: function(result){
                if(result.status == 200){
                    $('#datatable').DataTable().ajax.reload();
                    toastr["success"](result.message)
                }
                else{
                    toastr["error"](result.message)
                }
            }
        });
    }
}
function closeModal(){
    formClearInput();
    document.getElementById('modelform').style.display='none';
}
function saveData(){
    $("#div_name span").remove("");
    $("#div_time span").remove("");
    $("#div_date span").remove("");
    $("#div_totalGuest span").remove("");
    $("#div_addressLine1 span").remove("");
    $("#div_pincode span").remove("");
    var errorMsg = "is Required";
    var name = document.getElementById('eventname').value;
    var time = document.getElementById('eventtime').value;
    var date = document.getElementById('eventdate').value;
    var totalGuest = document.getElementById('totalGuest').value;
    var addressLine1 = document.getElementById('addressLine1').value;
    var addressLine2 = document.getElementById('addressLine1').value;
    var city = document.getElementById('city').value;
    var state = document.getElementById('state').value;
    var country = document.getElementById('country').value;
    var pincode = document.getElementById('pincode').value;
    var event_id = document.getElementById('event_id').value;
    if(name == ''){
        $("#div_name").append("<span class='error-msg'> Name "+errorMsg+"</span>");
        $( "#eventname" ).focus();
        return false;
    }
    if(date == ''){
        $("#div_date").append("<span class='error-msg'> Date "+errorMsg+"</span>");
        $( "#eventdate" ).focus();
        return false;
    }
    if(time == ''){
        $("#div_time").append("<span class='error-msg'> Time "+errorMsg+"</span>");
        $( "#eventtime" ).focus();
        return false;
    }
    // return false;
    if(totalGuest == ''){
        $("#div_totalGuest").append("<span class='error-msg'> Total Guest "+errorMsg+"</span>");
        $( "#totalGuest" ).focus();
        return false;
    }
    if(addressLine1 == ''){
        $("#div_addressLine1").append("<span class='error-msg'> Address Line 1 "+errorMsg+"</span>");
        $( "#addressLine1" ).focus();
        return false;
    }
    if(country == ''){
        $("#div_country").append("<span class='error-msg'> Country "+errorMsg+"</span>");
        $( "#country" ).focus();
        return false;
    }
    if(state == ''){
        $("#div_state").append("<span class='error-msg'> State "+errorMsg+"</span>");
        $( "#state" ).focus();
        return false;
    }
    if(city == ''){
        $("#div_city").append("<span class='error-msg'> City "+errorMsg+"</span>");
        $( "#city" ).focus();
        return false;
    }
    if(pincode == ''){
        $("#div_pincode").append("<span class='error-msg'> Pincode "+errorMsg+"</span>");
        $( "#pincode" ).focus();
        return false;
    }
    var url= "/event";
    var method = "POST";
    if(event_id){
        url= "/event/"+event_id;
        method = "PUT";
    }
    var data = {"_token": "{{ csrf_token() }}",name:name,time:time,date:date,guest_count:totalGuest,address_line1:addressLine1,address_line2:addressLine2,city:city,state:state,country:country,pincode:pincode};
    jQuery.ajax({
        url: url,
        method: method,
        data: data,
        success: function(result){
            if(result.status == 200){
                $('#datatable').DataTable().ajax.reload();
                formClearInput();
                document.getElementById('modelform').style.display='none';
                toastr["success"](result.message)
            }
            else{
                toastr["error"](result.message)
            }
        },
        error: function (error) {
            if(error.responseJSON.errors != undefined){
                var bError = error.responseJSON.errors;
                for (var key in bError) {
                    if (bError.hasOwnProperty(key)) {
                        $("#div_"+key).append("<span class='error-msg'> "+bError[key]+"</span>");
                        $( "#"+key ).focus();
                        toastr["error"](bError[key])
                    }
                }
            }
        }
    });
}
$('#datatable').DataTable({
    processing: true,
    serverSide: true,
    autoWidth: true,
    responsive: true,
    ajax: '{{ route("event.index_data") }}',
    columns: [
        {data: 'id',name: 'id'},
        {data: 'name', name: 'name'},
        {data: 'time',name: 'time'},
        {data: 'date',name: 'date'},
        {data: 'guest_count',name: 'guest_count'},
        {data: 'address',name: 'address'},
        {data: 'updated_at',name: 'updated_at'},
        {data: 'action',name: 'action',orderable: false,searchable: false}
    ]
});

function callCountryApi(country_id=''){
    $.ajax({
        url: "/country-list/",
        type: "POST",
        data: {"_token": "{{ csrf_token() }}"},
        cache: false,
        success: function(result){
            if(result.status == 200){
                var data = result.data;
                if(country_id == ''){
                    var option = '<option value="">Select Country</option>';
                }
                if(data[0] != undefined){
                    data.forEach(element => {
                        if(country_id != '' && element.id == country_id){
                            option +="<option value='"+element.id+"' selected>"+element.name+"</option>";
                        }
                        else{
                            option +="<option value='"+element.id+"'>"+element.name+"</option>";
                        }
                    });
                    $("#country").html(option);
                    $('#state').html('<option value="">Select Country First</option>');
                }
            }
            else{
                $("#div_country").append("<span class='error-msg'>"+result.message+"</span>");
            }
        }
    });
}

function callStateApi(country_id,state_id=''){
    $.ajax({
        url: "/state-list/",
        type: "POST",
        data: {"_token": "{{ csrf_token() }}",country_id: country_id},
        cache: false,
        success: function(result){
            if(result.status == 200){
                var data = result.data;
                if(state_id == ''){
                    var option = '<option value="">Select State</option>';
                }
                if(data[0] != undefined){
                    data.forEach(element => {
                        if(state_id != '' && element.id == state_id){
                            option +="<option value='"+element.id+"' selected>"+element.name+"</option>";
                        }
                        else{
                            option +="<option value='"+element.id+"'>"+element.name+"</option>";
                        }
                    });
                    $("#state").html(option);
                    $('#city').html('<option value="">Select State First</option>');
                }
            }
            else{
                $("#div_country").append("<span class='error-msg'>"+result.message+"</span>");
            }
        }
    });
}

function callCityApi(state_id,city_id=''){
    $.ajax({
        url: "/city-list/",
        type: "POST",
        data: {"_token": "{{ csrf_token() }}",state_id: state_id},
        cache: false,
        success: function(result){
            if(result.status == 200){
                var data = result.data;
                if(city_id == ''){
                    var option = '<option value="">Select City</option>';
                }
                if(data[0] != undefined){
                    data.forEach(element => {
                        if(city_id != '' && element.id == city_id){
                            option +="<option value='"+element.id+"' selected>"+element.name+"</option>";
                        }
                        else{
                            option +="<option value='"+element.id+"'>"+element.name+"</option>";
                        }
                    });
                    $("#city").html(option);
                }
            }
            else{
                $("#div_state").append("<span class='error-msg'>"+result.message+"</span>");
            }
        }
    });
}
$(document).ready(function() {
    callCountryApi();
    $( "#eventdate" ).datepicker({
        minDate: new Date(),
        dateFormat: "yy-mM-d",
    });
    $('#eventtime').timepicker({
        timeFormat: 'hh:mm p',
        interval: 15,
        minTime: '08:00am',
        maxTime: '10:00pm',
        defaultTime: defaultTime,
        startTime: '10:00',
        dynamic: true,
        dropdown: true,
        scrollbar: true
    });
    $('.select2').select2();
    $('#country').on('change', function() {
        $("#div_country span").remove("");
        $("#state").html('<option value="">Select Country First</option>');
        $('#city').html('<option value="">Select State First</option>');
        var country_id = this.value;
        if(country_id == ''){
            $("#div_country").append("<span class='error-msg'>Please Select Valid Country</span>");
            $( "#country" ).focus();
            return false;
        }
       callStateApi(country_id);
    });
    $('#state').on('change', function() {
        $("#div_state span").remove("");
        $('#city').html('<option value="">Select State First</option>');
        var state_id = this.value;
        if(state_id == ''){
            $("#div_state").append("<span class='error-msg'>Please Select Valid State</span>");
            $( "#state" ).focus();
            return false;
        }
       callCityApi(state_id);
    });
    $('#city').on('change', function() {
        $("#div_city span").remove("");
        var state_id = this.value;
        if(state_id == ''){
            $("#div_city").append("<span class='error-msg'>Please Select Valid City</span>");
            $( "#div_city" ).focus();
            return false;
        }
    });
})
</script>
@endpush
