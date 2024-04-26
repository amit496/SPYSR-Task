@extends('backend.layouts.master')

@section('title')
    {{ config('app.name') }} | {{ config('app.description') }}
@endsection
<style>
    .modal-content{
        display: block;
        margin: auto;
    }
    .modal-sm{
        border-radius: 20px!important;
        margin-top:200px;
    }
</style>

<link rel="stylesheet" href="https://hub.horts.com.au/cdn-cache/cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.css" />
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/5.10.1/main.min.css" rel="stylesheet">


<div id='calendar'></div>
<button id="openModal" data-target="#order-modal" data-toggle="modal" ></button>
<div class="modal fade ui-draggable ui-draggable-handle" id="order-modal" tabindex="-1" role="dialog" aria-labelledby="" data-keyboard="false" data-backdrop="static" style="display: none;" aria-hidden="true">
    <form method="post" action="" id="order" >
       @csrf
            <div class="modal-content modal-sm">
                <div class="modal-header">
                    <h6> What schedules would you pick ?&nbsp;</h6>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i class="fad fa-times"></i></span>
                    </button>
                </div>
                <div class="modal-body pt-0" id="copy-container">
                    <div class="form-control">
                        <input type="checkbox" class="schedule" name="schedule[0]" value="Breakfast" id="">
                        <label for="">Breakfast</label>
                    </div>
                    <div class="form-control">
                        <input type="checkbox" class="schedule" name="schedule[1]" value="Lunch" id="">
                        <label for="">Lunch</label>
                    </div>
                    <div class="form-control">
                        <input type="checkbox" class="schedule" name="schedule[2]" value="Dinner" id="">
                        <label for="">Dinner</label>
                    </div>
                    <input type="hidden" name="order_date">
                </div>
                <div class="modal-footer">
                    <button class="btn btn-success">Order</button>
                </div>
            </div>
        </div>
    </form>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://hub.horts.com.au/cdn-cache/cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.js"></script>
<script>
    var eventData = [
    {
        title: 'Accha din',
        start: '2024-04-01'
    },
    {
        title: 'Bura Din',
        start: '2024-04-26',
    }]
    $(document).ready(function() {
        getEventData()
    });

function getData(data)
{
    var calendarEl = document.getElementById('calendar');
    let click=function (dateFormat)
    {
        let calendarm = document.getElementById('calendar');
        let mcal = new FullCalendar.Calendar(calendarm);
        let date = mcal.getDate();
        let currentMonth =  moment(date).format(dateFormat);
        calendar.gotoDate( currentMonth );
    }

    let calenData={
        headerToolbar: {
            left: 'prevYear today btnJan btnFeb btnMar btnApr btnMay btnJun',
            center: 'title',
            right: 'btnJul btnAug btnSep btnOct btnNov btnDec nextYear'
        },
        initialView: 'dayGridMonth',
        events: eventData,
        customButtons: {},
        dateClick(info)
        {
            alert(info.dateStr)
            $(`#openModal`).trigger('click')
            $(`input[name=order_date]`).val(info.dateStr)
            // yaha se add hota hai
        },
        eventClick(calEvent, jsEvent, view)
        {
            eventClickId = calEvent.event._def.groupId;
            console.log(calEvent.event)
            // ye event pr jab click karoge tab fire hoga
            alert('click kia kya?')
        },
        eventDidMount(info)
        {
            $('.fc-daygrid-event').addClass('project-thickness fc-sticky');
        }
    };
    let thisYear=(new Date()).getYear()+1900;
    for (let i = 0; i < 12; i++)
    {
        let date=new Date(thisYear, i);
        let shortMonth=(new Intl.DateTimeFormat('en', {month: 'short'})).format(date);
        let longMonth=(new Intl.DateTimeFormat('en', {month: 'long'})).format(date);
        let monthNum=("00"+(i+1)).slice(-2);
        calenData.customButtons['btn'+shortMonth]={
            text: longMonth,
            click: click.bind(null, 'YYYY-'+monthNum+'-01')
        };
    }
    let calendar = new FullCalendar.Calendar(calendarEl, calenData);
    calendar.render();
}

function getEventData(){
    $.ajax({
        url : '{{ route("admin.order.getData") }}',
        type:'get',
        datatype:'JSON',
        success:data=>{
            console.log(data)
            // let AllData = JSON.parse(data)
            eventData = data
            getData(eventData)
        }
    })
}

$(`#order`).on('submit', function(e){
    e.preventDefault()
    let formData = new FormData($(`#order`)[0]);
    formData.append('_token','{{csrf_token()}}')
    jQuery.ajax({
        url:'{{route("admin.order.place")}}',
        type:'POST',
        data:formData,
        processData:false,
        contentType:false,
        success:res=>{
            console.log(res)
            console.log('should not be changed till now', eventData)
            if(res.status){
                let order={ title : res.output.food_timing, start: res.output.date }
                eventData.push(order)
                $('.close').trigger('click')
                getData(eventData)
                $('#order')[0].reset()
            }else{
                alert(res.message)
            }
        }
    })
})
    </script>
  </head>

