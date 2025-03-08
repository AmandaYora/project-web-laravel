@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="card-header">
                        <h6 class="card-title mb-0">Scan QR Code</h6>
                    </div>

                    <div class="alert alert-info alert-dismissible fade show d-none" role="alert" id="scanAlert">
                        <span id="scanMessage"></span>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>

                    <div class="text-center my-4">
                        <div id="reader" style="width: 100%; max-width: 640px; margin: 0 auto;"></div>
                    </div>

                    <div class="text-center">
                        <button class="btn btn-primary" id="startButton">
                            <i class="fas fa-camera me-2"></i>Start Scanner
                        </button>
                        <button class="btn btn-secondary d-none" id="stopButton">
                            <i class="fas fa-stop me-2"></i>Stop Scanner
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    <script>
        $(document).ready(function() {
            let html5QrcodeScanner = null;

            function showMessage(message, type = 'info') {
                $('#scanAlert')
                    .removeClass('d-none alert-info alert-success alert-danger')
                    .addClass('alert-' + type);
                $('#scanMessage').text(message);
            }

            function onScanSuccess(decodedText, decodedResult) {
                // Stop scanning
                if (html5QrcodeScanner) {
                    html5QrcodeScanner.stop().then(() => {
                        $('#startButton').removeClass('d-none');
                        $('#stopButton').addClass('d-none');
                    });
                }

                // Process the scanned code
                $.ajax({
                    url: '{{ route("attendance.process") }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        barcode: decodedText
                    },
                    success: function(response) {
                        showMessage(response.message, 'success');
                    },
                    error: function(xhr) {
                        let message = 'An error occurred';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        }
                        showMessage(message, 'danger');
                    }
                });
            }

            function onScanFailure(error) {
                // handle scan failure, usually better to ignore and keep scanning
                console.warn(`QR scan failed = ${error}`);
            }

            $('#startButton').on('click', function() {
                $(this).addClass('d-none');
                $('#stopButton').removeClass('d-none');
                $('#scanAlert').addClass('d-none');

                html5QrcodeScanner = new Html5Qrcode("reader");
                html5QrcodeScanner.start(
                    { facingMode: "environment" }, // Use back camera
                    {
                        fps: 10,
                        qrbox: { width: 250, height: 250 },
                        aspectRatio: 1.0
                    },
                    onScanSuccess,
                    onScanFailure
                ).catch((err) => {
                    showMessage('Error starting scanner: ' + err, 'danger');
                    $('#startButton').removeClass('d-none');
                    $('#stopButton').addClass('d-none');
                });
            });

            $('#stopButton').on('click', function() {
                if (html5QrcodeScanner) {
                    html5QrcodeScanner.stop().then(() => {
                        $('#startButton').removeClass('d-none');
                        $('#stopButton').addClass('d-none');
                        html5QrcodeScanner = null;
                    });
                }
            });

            // Clean up on page unload
            $(window).on('beforeunload', function() {
                if (html5QrcodeScanner) {
                    html5QrcodeScanner.stop();
                }
            });
        });
    </script>
@endpush
