@extends('layouts.contractheader')

@section('page-title')
    {{ __('Education Certificate') }}
@endsection

@section('action-button')
    <a href="#" class="btn btn-sm btn-primary" id="download-certificate">{{ __('Download') }}</a>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card mt-4">
                <div class="card-body" id="certificate-box">
                    <div class="text-center mb-4">
                        <h2>{{ __('Certificate of Completion') }}</h2>
                        <p class="mb-0">{{ __('This certifies that') }}</p>
                        <h4 class="mt-2">{{ $employee ? $employee->name : __('Employee') }}</h4>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <p class="mb-1"><strong>{{ __('Course') }}:</strong> {{ $course ? $course->name : '-' }}</p>
                            <p class="mb-1"><strong>{{ __('Trainer') }}:</strong> {{ $trainer ? $trainer->firstname : '-' }}</p>
                            <p class="mb-1"><strong>{{ __('Issued At') }}:</strong> {{ $certificate->issued_at ? $certificate->issued_at->format('d/m/Y') : '' }}</p>
                            <p class="mb-1"><strong>{{ __('Certificate No') }}:</strong> {{ $certificate->certificate_number }}</p>
                        </div>
                        <div class="col-md-6 text-end">
                            <div class="d-inline-block text-center">
                                {!! DNS2D::getBarcodeHTML($certificate->qr_payload, 'QRCODE', 2, 2) !!}
                                <div class="mt-2">{{ __('Verification QR') }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4">
                        <p>{{ __('Scan the QR code or visit the verification link to validate this certificate.') }}</p>
                        <p><a href="{{ $certificate->qr_payload }}">{{ $certificate->qr_payload }}</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script-page')
    <script type="text/javascript" src="{{ asset('js/html2pdf.bundle.min.js') }}"></script>
    <script>
        document.getElementById('download-certificate').addEventListener('click', function (event) {
            event.preventDefault();
            var element = document.getElementById('certificate-box');
            var opt = {
                filename: '{{ $certificate->certificate_number }}.pdf',
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { scale: 2 },
                jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
            };
            html2pdf().set(opt).from(element).save();
        });
    </script>
@endpush
