        <ul class="nav right_links">
          <!-- <li class="">
            <a href="#" class="">
              <i class="fa fa-clock-o"></i>
            </a>
          </li> -->
          <li class="dropdown right_content-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <i class="fa fa-download"></i>
            </a>
            <ul class="dropdown-menu">
              <li>
                <a href="{{ url('report-old') . '?'. http_build_query(array_merge(Request::all(), ['ac' => 'dlpdf'])) }}">
                  <i class="fa fa-file-pdf-o text-danger"></i> PDF
                </a>
              </li>
              {{--<li>
                <a href="">
                  <i class="fa fa-file-excel-o text-success"></i> Excel
                </a>
              </li>--}}
            </ul>
          </li>
        </ul>
