<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Time In/Out | Bigbyte Aqua Ventures WRM</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">
        <script src="/assets/js/qr_packed.js"></script>

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
                /* height: 100vh; */
            }

            .flex-center {
                align-items: center;
                display: flex;
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
                max-width: 680px;
                width: 100%;
                margin: 25px;
            }

            .title {
                font-size: 50px;
            }

            .links > a {
                color: #636b6f;
                padding: 0 25px;
                font-size: 12px;
                font-weight: 600;
                letter-spacing: .1rem;
                text-decoration: none;
                text-transform: uppercase;
            }

            .m-b-md {
                margin-bottom: 0px;
                font-family: sans-serif;
            }
            .time{
                margin-bottom: 30px;
            }
            .form-timein{
                text-align: left;
            }
            .form-timein input, select{
                display: block;
                width: 98%;
                height: 34px;
                padding: 5px 0px 5px 8px;
                font-size: 14px;
                line-height: 1.42857143;
                color: #555;
                background-color: #fff;
                background-image: none;
                border: 1px solid #ccc;
                border-radius: 4px;
                box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
                -webkit-transition: border-color ease-in-out .15s,box-shadow ease-in-out .15s;
                transition: border-color ease-in-out .15s,box-shadow ease-in-out .15s;
            }
            select{
                width: 100%;
                height: 45px;
                max-width: 250px;
            }
            .form-timein .btn{
                box-shadow: none;
                color: #fff;
                display: inline-block;
                padding: 12px 12px;
                margin-bottom: 0;
                font-size: 14px;
                font-weight: 600;
                line-height: 1.42857143;
                text-align: center;
                white-space: nowrap;
                vertical-align: middle;
                -ms-touch-action: manipulation;
                touch-action: manipulation;
                cursor: pointer;
                -webkit-user-select: none;
                -moz-user-select: none;
                -ms-user-select: none;
                user-select: none;
                background-image: none;
                border: 1px solid transparent;
                border-radius: 4px;
                background-color: #3c8dbc;
                border-color: #367fa9;
                width: 100%;
                max-width: 195px;
                text-decoration: unset;
                margin-bottom: 10px;
            }
            .form-timein label{
                font-weight: 600;
                display: block;
            }
            .form-timein .row{
                margin-bottom: 10px;
            }
            div.alert-success p.notification{
                color: #0B8010;
                font-weight: 600;
            }
            div.alert-warning p.notification{
                color: #FD0017;
                font-weight: 600;
            }
            p.notification{
                font-family: sans-serif;
            }
            p.note{
                font-family: sans-serif; 
                text-align: center; 
                line-height: 25px;
            }
            img{width: 100%;}


            /* Style the tab */
            .tab {
            overflow: hidden;
            border: 1px solid #3F8EBA;
            background-color: #f1f1f1;
            border-radius: 5px 5px 0 0;
            }

            /* Style the buttons that are used to open the tab content */
            .tab button {
            background-color: inherit;
            float: left;
            border: none;
            outline: none;
            cursor: pointer;
            padding: 14px 16px;
            transition: 0.3s;
            }

            /* Change background color of buttons on hover */
            .tab button:hover {
            background-color: #ddd;
            }

            /* Create an active/current tablink class */
            .tab button.active {
            background-color: #3F8EBA;
            color: #ffffff;
            }

            /* Style the tab content */
            .tabcontent {
            display: none;
            padding: 6px 12px;
            border: 1px solid #3F8EBA;
            border-top: none;
            padding-top: 25px;
            border-radius: 0 0 5px 5px;
            }

            body, input {font-size:14pt}
            input, label {vertical-align:middle}
            .qrcode-text {padding-right:1.7em; margin-right:0; background: none!important;}
            .qrcode-text-btn {display:inline-block!important; background:url(/uploads/images/1499401426qr_icon.svg) 50% 50% no-repeat; height:1em; width:1.7em; margin-left:-2em; cursor:pointer}
            .qrcode-text-btn > input[type=file] {position:absolute; overflow:hidden; width:1px; height:1px; opacity:0}
        </style>
    </head>
    <body>
        <div class="flex-center position-ref full-height">

            <div class="content">
                <div class="title m-b-md" style="font-family: unset; font-size: 60px;">
                    <img src="{{url('/uploads/images/sheyan_logo-reduce.jpg')}}" alt="Image" style="max-width: 520px;"/>
                </div>
                <div class="title m-b-md">
                    {{ date('F j, Y')}}
                </div>
                <div class="title m-b-md time">
                    {{ date('g:i A') }}
                </div>

                <div class="tab">
                    <button class="tablinks" onclick="openTab(event, 'tab1')" id="defaultOpen">Time IN/OUT</button>
                    <button class="tablinks" onclick="openTab(event, 'tab2')">Time IN/OUT via QR Code</button>
                </div>

                <div id="tab1" class="tabcontent">
                    <div class="row">
                        <div class="col-md-12 form-timein">
                            {!! Form::open(array('url' => 'save-time-in-or-out','method'=>'POST')) !!}
                            <div class="row">
                                <div class="col-sm-12">
                                    <label>Username:</label>
                                    <input type="text" name="username" class="form-control" placeholder="Enter username" required autocomplete="off"/>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <label>Password:</label>
                                    <input type="password" name="password" class="form-control" placeholder="Enter password" required autocomplete="off"/>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <label>Select Option:</label>
                                    <select name="status" class="form-control" required>
                                        <option value="">Select Option</option>
                                        <option value="In">Time In</option>
                                        <option value="Lunch">Lunch</option>
                                        <option value="Break">Break</option>
                                        <option value="Finish">Finish Lunch/Break</option>
                                        <option value="Out">Time Out</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <label>Note:</label>
                                    <input type="text" name="notes" class="form-control" placeholder="Optional only" data-lpignore="true"/>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                    <a href="{{ url('dtr-verification') }}" class="btn btn-primary">View Time IN/OUT History</a>
                                </div>
                            </div>
                            {!! Form::close() !!}
                        </div>
                    </div>
                </div>
                <div id="tab2" class="tabcontent">
                    <div class="row">
                        <div class="col-md-12 form-timein">
                            {!! Form::open(array('url' => 'save-time-in-or-out','method'=>'POST')) !!}
                            <div class="row">
                                <div class="col-sm-12">
                                    <label>QR Code:</label>
                                    <input type="text" name="token" class="qrcode-text" placeholder="QR Code" required readonly data-lpignore="true" style="display: inline-block;" autocomplete="off"/><label class = qrcode-text-btn><input type=file accept="image/*" capture=environment onchange="openQRCamera(this);" tabindex=-1></label> 
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <label>Select Option:</label>
                                    <select name="status" class="form-control" required>
                                        <option value="">Select Option</option>
                                        <option value="In">Time In</option>
                                        <option value="Lunch">Lunch</option>
                                        <option value="Break">Break</option>
                                        <option value="Finish">Finish Lunch/Break</option>
                                        <option value="Out">Time Out</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <label>Note:</label>
                                    <input type="text" name="notes" class="form-control" placeholder="Optional only" data-lpignore="true"/>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                    <a href="{{ url('dtr-verification') }}" class="btn btn-primary">View Time IN/OUT History</a>
                                    <a href="{{ url('access-verification') }}" class="btn btn-primary">Create QR code</a>
                                </div>
                            </div>
                            {!! Form::close() !!}
                        </div>
                    </div>
                </div>

                @if ($message = Session::get('success'))
                <div class="alert alert-success alert-dismissible">
                    <p class="notification">{{ $message }}</p>
                </div>
                @endif

                @if ($message = Session::get('warning'))
                <div class="alert alert-warning alert-dismissible">
                    <p class="notification">{!! nl2br($message) !!}</p>
                </div>
                @endif

            </div>
        </div>
    </body>
</html>

<script>
    function openTab(evt, tabName) {
    // Declare all variables
    var i, tabcontent, tablinks;

    // Get all elements with class="tabcontent" and hide them
    tabcontent = document.getElementsByClassName("tabcontent");
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
    }

    // Get all elements with class="tablinks" and remove the class "active"
    tablinks = document.getElementsByClassName("tablinks");
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(" active", "");
    }

    // Show the current tab, and add an "active" class to the button that opened the tab
    document.getElementById(tabName).style.display = "block";
    evt.currentTarget.className += " active";
    }

    // Get the element with id="defaultOpen" and click on it
    document.getElementById("defaultOpen").click();

    // Scan QR code
    function openQRCamera(node) {
    var reader = new FileReader();
    reader.onload = function() {
        node.value = "";
        qrcode.callback = function(res) {
        if(res instanceof Error) {
            alert("No QR code found. Please make sure the QR code is within the camera's frame and try again.");
        } else {
            node.parentNode.previousElementSibling.value = res;
        }
        };
        qrcode.decode(reader.result);
    };
    reader.readAsDataURL(node.files[0]);
    }

</script>