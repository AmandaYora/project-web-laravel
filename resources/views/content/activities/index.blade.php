@extends('layouts.app')
@section('title', 'Activity')
@section('content')
<div class="row">
  <div class="col-md-12 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h6 class="card-title mb-0">Data Activity</h6>
          <div>
            <button type="button" id="filterBtn" class="btn btn-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#filterModal">Filter</button>
            <button type="button" id="printBtn" class="btn btn-warning btn-sm">Print</button>
            <button type="button" id="addBtn" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addEditModal">
              <i class="fas fa-plus"></i>
            </button>
          </div>
        </div>
        @if (session('error'))
          <div class="alert alert-danger alert-dismissible fade show" role="alert" id="errorAlert">
            <strong>Error!</strong> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        @endif
        @if (session('success'))
          <div class="alert alert-success alert-dismissible fade show" role="alert" id="successAlert">
            <strong>Success!</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        @endif
        @if ($errors->any())
          <div class="alert alert-danger alert-dismissible fade show" role="alert" id="errorAlert">
            <strong>Error!</strong>
            <ul>
              @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        @endif
        <div class="table-responsive">
          <table id="dataTableExample" class="table">
            <thead>
              <tr>
                <th>Task</th>
                <th>Status</th>
                <th>Date</th>
                <th>Time</th>
                <th>Evidence</th>
                <th>Description</th>
                @if(session('user.role') == 'admin')
                  <th>Aksi</th>
                @endif
              </tr>
            </thead>
            <tbody>
              @foreach ($activities as $item)
              <tr data-item="{{ json_encode($item) }}">
                <td>{{ $item->task->check->name }}</td>
                <td>{{ $item->status }}</td>
                <td>{{ $item->date }}</td>
                <td>{{ substr($item->time, 0, 5) }}</td>
                <td>
                  <img src="{{ asset($item->evidence) }}" alt="Evidence" class="image-click" style="max-width:100px; cursor:pointer;">
                </td>
                <td>{{ $item->description }}</td>
                @if(session('user.role') == 'admin')
                <td>
                  <button type="button" class="btn btn-sm btn-info edit-btn" data-bs-toggle="modal" data-bs-target="#addEditModal">
                    <i class="fas fa-edit"></i>
                  </button>
                  <button type="button" class="btn btn-sm btn-danger delete-btn">
                    <i class="fas fa-trash"></i>
                  </button>
                  <form method="POST" action="{{ route('activities.destroy', $item->activity_id) }}" style="display:none;" class="delete-form">
                    @csrf
                    @method('DELETE')
                  </form>
                </td>
                @endif
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="addEditModal" tabindex="-1" aria-labelledby="addEditModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addEditModalLabel">Tambah Data Activity</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form method="POST" action="{{ route('activities.save') }}" id="dataForm" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="activity_id" id="activity_id">
        <div class="modal-body">
          <div class="mb-3">
            <label for="task_id" class="form-label">Task</label>
            <select class="form-select" id="task_id" name="task_id" required>
              <option value="" disabled selected>Pilih Task</option>
              @foreach ($tasks as $item)
              <option value="{{ $item->task_id }}">{{ $item->check->name }} - [{{ $item->atm->code }}] {{ $item->atm->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select class="form-select" id="status" name="status" required>
              <option value="" disabled selected>Select Status</option>
              <option value="Good">Good</option>
              <option value="Broken">Broken</option>
            </select>
          </div>
          <div class="mb-3">
            <label for="date" class="form-label">Date</label>
            <input type="date" class="form-control" id="date" name="date" required>
          </div>
          <div class="mb-3">
            <label for="time" class="form-label">Time</label>
            <input type="time" class="form-control" id="time" name="time" required>
          </div>
          <div class="mb-3">
            <label for="evidence" class="form-label">Evidence</label>
            <input type="file" class="form-control" id="evidence" name="evidence" accept="image/*">
          </div>
          <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <input type="text" class="form-control" id="description" name="description" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
          <button type="submit" class="btn btn-primary btn-sm">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>
<div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-body p-0">
        <img src="" id="modalImage" class="img-fluid w-100" alt="Evidence">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="filterModalLabel">Filter Tanggal</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label for="startDate" class="form-label">Start Date</label>
          <input type="date" class="form-control" id="startDate">
        </div>
        <div class="mb-3">
          <label for="endDate" class="form-label">End Date</label>
          <input type="date" class="form-control" id="endDate">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" id="resetFilter" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Reset</button>
        <button type="button" id="applyFilter" class="btn btn-primary btn-sm" data-bs-dismiss="modal">Filter</button>
      </div>
    </div>
  </div>
</div>
@endsection
@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.3/dist/jquery.validate.min.js"></script>
<script>
$(document).ready(function(){
  function filterRows(startDate, endDate){
    $("#dataTableExample tbody tr").each(function(){
      var rowDate = $(this).find("td:eq(2)").text();
      if(rowDate >= startDate && rowDate <= endDate){
        $(this).show();
      } else {
        $(this).hide();
      }
    });
  }
  var today = new Date();
  var dd = String(today.getDate()).padStart(2,"0");
  var mm = String(today.getMonth()+1).padStart(2,"0");
  var yyyy = today.getFullYear();
  var todayStr = yyyy+"-"+mm+"-"+dd;
  filterRows(todayStr, todayStr);
  $('#addEditModal').on('hidden.bs.modal', function(){
    $('#dataForm')[0].reset();
    $('#activity_id').val('');
    $('#addEditModalLabel').text('Tambah Data Activity');
    $('#dataForm').validate().resetForm();
  });
  $('#addBtn').on('click', function(){
    $('#dataForm')[0].reset();
    $('#activity_id').val('');
    $('#addEditModalLabel').text('Tambah Data Activity');
  });
  $('.edit-btn').on('click', function(){
    var row = $(this).closest('tr');
    var item = row.data('item');
    $('#activity_id').val(item.activity_id);
    $('#task_id').val(item.task_id);
    $('#status').val(item.status);
    $('#date').val(item.date);
    $('#time').val(item.time.substr(0,5));
    $('#description').val(item.description);
    $('#addEditModalLabel').text('Edit Data Activity');
  });
  $('.image-click').on('click', function(){
    var src = $(this).attr('src');
    $('#modalImage').attr('src', src);
    $('#imageModal').modal('show');
  });
  setTimeout(function(){
    $('#errorAlert').alert('close');
    $('#successAlert').alert('close');
  },3000);
  $('.delete-btn').on('click', function(e){
    e.preventDefault();
    var form = $(this).siblings('.delete-form');
    Swal.fire({
      title:'Apakah Anda yakin?',
      text:"Data akan dihapus secara permanen!",
      icon:'warning',
      showCancelButton:true,
      confirmButtonColor:'#3085d6',
      cancelButtonColor:'#d33',
      confirmButtonText:'Ya, hapus!',
      cancelButtonText:'Batal'
    }).then((result)=>{
      if(result.isConfirmed){
        form.submit();
      }
    });
  });
  $('#applyFilter').on('click', function(){
    var startDate = $('#startDate').val();
    var endDate = $('#endDate').val();
    if(startDate === "" || endDate === ""){
      return;
    }
    filterRows(startDate, endDate);
  });
  $('#resetFilter').on('click', function(){
    filterRows(todayStr, todayStr);
  });
  $('#printBtn').on('click', function(){
    var printTable = $('<table>').addClass('print-table');
    var header = $("#dataTableExample thead").clone();
    var tbody = $("<tbody>");
    $("#dataTableExample tbody tr:visible").each(function(){
      tbody.append($(this).clone());
    });
    printTable.append(header).append(tbody);
    var styles = `<style>
      body { font-family: "Arial", sans-serif; margin: 20px; }
      .print-header { text-align: center; margin-bottom: 20px; }
      .print-header h1 { margin: 0; font-size: 24px; }
      .print-header p { margin: 0; font-size: 14px; color: #555; }
      .print-table { width: 100%; border-collapse: collapse; }
      .print-table th, .print-table td { border: 1px solid #000; padding: 8px; text-align: left; }
      .print-table th { background-color: #f2f2f2; }
      .print-footer { text-align: center; margin-top: 20px; font-size: 12px; color: #777; }
    </style>`;
    var printContent = `
      <html>
        <head>
          <title>Print Data Activity</title>
          ${styles}
        </head>
        <body onload="window.print();window.close();">
          <div class="print-header">
            <h1>Laporan Data Activity Task</h1>
            <hr>
          </div>
          ${printTable.prop('outerHTML')}
          <div class="print-footer">
            <p>Dicetak pada: ${new Date().toLocaleString()}</p>
          </div>
        </body>
      </html>
    `;
    var newWin = window.open('', 'Print-Window');
    newWin.document.open();
    newWin.document.write(printContent);
    newWin.document.close();
  });
  $('#dataForm').validate({
    submitHandler: function(form){ form.submit(); }
  });
});
</script>
@endsection
