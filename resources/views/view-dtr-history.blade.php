<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Bigbyte Aqua Ventures</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">
        <link href="{{ asset('/assets/css/main.css')}}" rel="stylesheet" type="text/css" />

        <!-- Styles -->
        <style>
            html, body {
                background-color: #fff;
                color: #636b6f;
                font-family: 'Raleway', sans-serif;
                font-weight: 100;
                height: 100vh;
                margin: 0;
            }

            .full-height {
                height: 100vh;
            }

            .flex-center {
                align-items: center;
                display: block;
                justify-content: center;
            }

            .position-ref {
                position: relative;
            }

            .top-right {
                position: absolute;
                right: 10px;
                top: 18px;
            }

            .content {
                text-align: center;
                max-width: 920px;
                width: 100%;
            }

            .title {
                font-size: 60px;
            }

            .links > a {
                color: #636b6f;
                padding: 0 25px;
                font-size: 16px;
                font-weight: 600;
                letter-spacing: .1rem;
                text-decoration: none;
                text-transform: uppercase;
            }

            .m-b-md {
                margin-bottom: 30px;
                margin-top: 25px;
            }
            thead{
                color: #000;
                font-weight: 600;
            }
            td{
                color: #000000;
                font-weight: 600;
            }
        </style>
    </head>
    <body>

    <?php
    use Carbon\Carbon;
    
        // Function to get hours and minutes
        function getHoursMinutes($seconds, $format = '%02d:%02d') {
    
            if (empty($seconds) || ! is_numeric($seconds)) {
                return false;
            }
    
            $minutes = round($seconds / 60);
            $hours = floor($minutes / 60);
            $remainMinutes = ($minutes % 60);
    
            return sprintf($format, $hours, $remainMinutes);
        }
    
        // Function to get hourMinute2Minutes
        function hourMinute2Minutes($strHourMinute) {
            $from = date('Y-m-d 00:00:00');
            $to = date('Y-m-d '.$strHourMinute.':00');
            $diff = strtotime($to) - strtotime($from);
            $minutes = $diff / 60;
            return (int) $minutes;
        }
    
    ?>
        <div class="flex-center position-ref full-height">

            <div class="content">
                <div class="title m-b-md">
                    Daily Time Record History
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover" style="width:100%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Time In</th>
                                <th>Time Out</th>
                                <th>Date</th>
                                <th>Time Consumed</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                        {{-- */
                            $x=0;
                        /* --}}
                        @foreach ($view_dtr_history as $dtr_history)
                            {{-- */
                                $x++;

                                $timein = new Carbon(date("Y-m-d H:i", strtotime($dtr_history->time_in)));
                                $timeout = new Carbon(date("Y-m-d H:i", strtotime($dtr_history->time_out)));
                                
                                $consumehours = $timein->diffInHours($timeout).':'.$timein->diff($timeout)->format('%I');
                            /* --}}
                            <tr>
                                <td>{{ $x }}</td>
                                <td align="right">{{ date('g:i A', strtotime($dtr_history->time_in)) }}</td>
                                <td align="right">@if($dtr_history->time_out == '0000-00-00 00:00:00') @else {{ date('g:i A', strtotime($dtr_history->time_out)) }} @endif</td>
                                <td align="right">{{ $dtr_history->date }}</td>
                                <td align="right">@if($dtr_history->time_out == '0000-00-00 00:00:00') @else {{ $consumehours }}@endif</td>
                                <td align="right">{{ $dtr_history->status }}</td>
                                </tr>
                                @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="links">
                    <a href="{{ url('time-in-time-out') }}"><<< Back to TIME IN/OUT</a>
                </div>
            </div>
        </div>
    </body>
    @include('setcookies');
</html>