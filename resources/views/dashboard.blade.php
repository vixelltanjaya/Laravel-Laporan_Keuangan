@extends('layouts.user_type.auth')

@section('content')

<div class="row">
  @if (Auth::user()->role_id == 1)
  <!-- Kas dan Setara Kas Card -->
  <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
    <div class="card">
      <div class="card-body p-3">
        <div class="row">
          <div class="col-8">
            <div class="numbers">
              <p class="text-sm mb-0 text-capitalize font-weight-bold">Kas dan Setara Kas</p>
              <h5 class="font-weight-bolder mb-0">
                Rp. {{ number_format($dashboard['group1'], 0, ',', '.') }}
                <span class="text-success text-sm font-weight-bolder"></span>
              </h5>
            </div>
          </div>
          <div class="col-4 text-end">
            <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
              <i class="ni ni-money-coins text-lg opacity-10" aria-hidden="true"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Armada Pariwisata Card -->
  <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
    <div class="card">
      <div class="card-body p-3">
        <div class="row">
          <div class="col-8">
            <div class="numbers">
              <p class="text-sm mb-0 text-capitalize font-weight-bold">Armada Pariwisata</p>
              <h5 class="font-weight-bolder mb-0">
                {{ $countBus }}
                <span class="text-success text-sm font-weight-bolder"></span>
              </h5>
            </div>
          </div>
          <div class="col-4 text-end">
            <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
              <i class="ni ni-world text-lg opacity-10" aria-hidden="true"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Hutang Jangka Pendek -->
  <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
    <div class="card">
      <div class="card-body p-3">
        <div class="row">
          <div class="col-8">
            <div class="numbers">
              <p class="text-sm mb-0 text-capitalize font-weight-bold">Hutang Jangka Pendek</p>
              <h5 class="font-weight-bolder mb-0">
                Rp. {{ number_format($dashboard['group2'], 0, ',', '.') }}
                <span class="text-danger text-sm font-weight-bolder"></span>
              </h5>
            </div>
          </div>
          <div class="col-4 text-end">
            <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
              <i class="ni ni-paper-diploma text-lg opacity-10" aria-hidden="true"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Piutang -->
  <div class="col-xl-3 col-sm-6">
    <div class="card">
      <div class="card-body p-3">
        <div class="row">
          <div class="col-8">
            <div class="numbers">
              <p class="text-sm mb-0 text-capitalize font-weight-bold">Piutang</p>
              <h5 class="font-weight-bolder mb-0">
                Rp. {{ number_format($dashboard['group3'], 0, ',', '.') }}
                <span class="text-success text-sm font-weight-bolder"></span>
              </h5>
            </div>
          </div>
          <div class="col-4 text-end">
            <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
              <i class="ni ni-cart text-lg opacity-10" aria-hidden="true"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endif


  <div class="row mt-4">
    <div class="col-12">
      <div class="card mb-4">
        <div class="card-header pb-0 text-center">
          <h4> Pendapatan Harian vs Pendapatan Pariwisata</h4>
        </div>
        <div class="card-body">
          <canvas id="myChart"></canvas>
        </div>
      </div>
    </div>
  </div>


<div class="row mt-4">
  <div class="col-12">
    <div class="card mb-4">
      <div class="card-header pb-0 text-left">
        <h3>Calendar</h3>
      </div>
      <div class="card-body">
        <div id="calendar-container" style="display:flex; justify-content:center; margin:0 auto;">
          <div id="calendar" style="max-width:800px; width:100%;" class="text-center"></div>
        </div>
      </div>
    </div>
  </div>
</div>

@endsection

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.0/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.min.js" integrity="sha512-7U4rRB8aGAHGVad3u2jiC7GA5/1YhQcQjxKeaVms/bT66i3LVBMRcBI9KwABNWnxOSwulkuSXxZLGuyfvo7V1A==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
      selectable: true,
      headerToolbar: {
        left: 'prev,next today',
        center: 'title',
        right: 'dayGridMonth,listWeek'
      },
      buttonText: {
        dayGridMonth: 'Booking',
        listWeek: 'List Booking'
      },
      events: function(fetchInfo, successCallback, failureCallback) {
        $.ajax({
          url: `{{ route('listBookDashboard') }}`,
          type: 'GET',
          success: function(data) {
            successCallback(data);
          },
          error: function(xhr, status, error) {
            console.error('Failed fetching events data:', error);
            failureCallback(error);
          }
        });
      },
      eventContent: function(arg) {
        var customer = document.createElement('div');
        customer.innerHTML = `Customer: ${arg.event.extendedProps.customer}`;

        var description = document.createElement('div');
        description.innerHTML = `${arg.event.extendedProps.description}`;

        var bus = document.createElement('div');
        bus.innerHTML = `<strong>Plat: ${arg.event.extendedProps.bus}</strong>`;

        return {
          domNodes: [bus]
        };
      },
      eventDidMount: function(info) {
        if (info.event.extendedProps.color) {
          info.el.style.backgroundColor = info.event.extendedProps.color;
        }
      }
    });
    calendar.render();

    // Fetch chart data from the server
    $.ajax({
      url: `{{ route('listPenjualanHarianVsPariwisata') }}`,
      type: 'GET',
      success: function(data) {
        const ctx = document.getElementById('myChart').getContext('2d');
        new Chart(ctx, {
          type: 'line', // Chart type
          data: {
            labels: data.labels, // Labels from the server
            datasets: data.datasets // Datasets from the server
          },
          options: {
            responsive: true,
            plugins: {
              legend: {
                position: 'top',
              },
              tooltip: {
                callbacks: {
                  label: function(tooltipItem) {
                    return tooltipItem.dataset.label + ': Rp. ' + number_format(tooltipItem.raw, 0, ',', '.'); // Format number
                  }
                }
              }
            },
            scales: {
              x: {
                title: {
                  display: true,
                  text: 'Bulan'
                }
              },
              y: {
                title: {
                  display: true,
                  text: 'Pendapatan'
                },
                beginAtZero: true,
                ticks: {
                  callback: function(value) {
                    return 'Rp. ' + number_format(value, 0, ',', '.'); // Format number
                  }
                }
              }
            }
          }
        });
      },
      error: function(xhr, status, error) {
        console.error('Failed fetching chart data:', error);
      }
    });

    function number_format(number, decimals, dec_point, thousands_sep) {
      number = (number + '').replace(/[^0-9+\-.]/g, '');
      var n = !isFinite(+number) ? 0 : +number,
        prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
        sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
        dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
        s = '',
        toFixedFix = function(n, prec) {
          var k = Math.pow(10, prec);
          return '' + Math.round(n * k) / k;
        };
      s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
      if (s[0].length > 3) {
        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
      }
      if ((s[1] || '').length < prec) {
        s[1] = s[1] || '';
        s[1] += new Array(prec - s[1].length + 1).join('0');
      }
      return s.join(dec);
    }
  });
</script>