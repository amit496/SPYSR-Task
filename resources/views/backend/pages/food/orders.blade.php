<style>
    .modal-sm
    {
        margin-top: 200px;
        left: 40%;
    }
</style>
@extends('backend.layouts.master')

@section('title')
    @include('backend.pages.food.partials.title')
@endsection

@section('admin-content')
    @include('backend.pages.food.partials.header-breadcrumbs')
    <div class="container-fluid">
        @include('backend.pages.food.partials.top-show')
        @include('backend.layouts.partials.messages')
        <div class="fixed d-none">
            <input type="text">
        </div>
        {{-- Calender --}}
        <div id='calendar'></div>

        <button id="openModal" data-target="#ordermodal" data-toggle="modal" ></button>
        <div class="modal " id="ordermodal"  aria-hidden="true">
            <form method="post" action="" id="order" >
               @csrf
                    <div class="modal-content modal-sm">
                        <div class="modal-header">
                            <h6> What schedules would you pick ?&nbsp;</h6>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body pt-2" id="copy-container">
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
                            <button class="btn btn-dark" data-dismiss="modal">Close</button>
                            <button class="btn btn-success">Order</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection


@section('scripts')
<script>
    var eventData = [ { title: 'Start din', start: '2024-04-01' }, { title: 'End Din', start: '2024-04-26', }];

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
                left: 'prevYear,prev,today',
                center: 'title',
                right: 'next,nextYear'
            },
            initialView: 'dayGridMonth',
            events: eventData,
            customButtons: {},
            dateClick(info)
            {
                if($(`td[data-date=${info.dateStr}]`).find('.fc-daygrid-event-harness').length){
                    console.log('nahi jaane doonga')
                    return false
                }
                $(`#openModal`).trigger('click')
                $(`input[name=order_date]`).val(info.dateStr)
            },
            eventClick(calEvent, jsEvent, view)
            {
                eventClickId = calEvent.event._def.groupId;
                console.log(calEvent.event)
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
                    // window.location.reload()
                    $('.close').click()
                    $(window).scrollTop(0);
                    getData(eventData)
                    $('#order')[0].reset()
                }else{
                    alert(res.message)
                }
            }
        })
    })
</script>
@endsection















{{-- @extends('backend.layouts.master') --}}

{{-- @section('title')
    {{ config('app.name') }} | {{ config('app.description') }}
@endsection --}}









{{-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://hub.horts.com.au/cdn-cache/cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.js"></script> --}}
    {{-- <script>
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
    </script> --}}

