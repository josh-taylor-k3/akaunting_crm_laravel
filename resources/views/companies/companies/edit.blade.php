@extends('layouts.admin')

@section('title', trans('general.title.edit', ['type' => trans_choice('general.companies', 1)]))

@section('content')
        <!-- Default box -->
        <div class="box box-success">
            {!! Form::model($company, [
                'method' => 'PATCH',
                'url' => ['companies/companies', $company->id],
                'files' => true,
                'role' => 'form'
            ]) !!}

            <div class="box-body">
                {{ Form::textGroup('company_name', trans('general.name'), 'id-card-o') }}

                {{ Form::textGroup('domain', trans('companies.domain'), 'globe') }}

                {{ Form::emailGroup('company_email', trans('general.email'), 'envelope') }}

                {{ Form::selectGroup('default_currency', trans_choice('general.currencies', 1), 'money', $currencies) }}

                {{ Form::textareaGroup('company_address', trans('general.address')) }}

                {{ Form::fileGroup('company_logo', trans('companies.logo')) }}

                {{ Form::radioGroup('enabled', trans('general.enabled')) }}
            </div>
            <!-- /.box-body -->

            @permission('update-companies-companies')
            <div class="box-footer">
                {{ Form::saveButtons('companies/companies') }}
            </div>
            <!-- /.box-footer -->
            @endpermission

            {!! Form::close() !!}
        </div>
@endsection

@push('js')
    <script src="{{ asset('public/js/bootstrap-fancyfile.js') }}"></script>
@endpush

@push('css')
    <link rel="stylesheet" href="{{ asset('public/css/bootstrap-fancyfile.css') }}">
@endpush

@push('scripts')
    <script type="text/javascript">
        var text_yes = '{{ trans('general.yes') }}';
        var text_no = '{{ trans('general.no') }}';

        $(document).ready(function(){
            $("#default_currency").select2({
                placeholder: "{{ trans('general.form.select.field', ['field' => trans_choice('general.currencies', 1)]) }}"
            });

            $('#company_logo').fancyfile({
                text  : '{{ trans('general.form.select.file') }}',
                style : 'btn-default',
                @if($company->logo)
                placeholder : '<?php echo $company->logo->basename; ?>'
                @endif
            });

            @if($company->logo)
                attachment_html  = '<span class="attachment">';
                attachment_html += '    <a href="{{ url('uploads/' . $company->logo->id . '/download') }}">';
                attachment_html += '        <span id="download-attachment" class="text-primary">';
                attachment_html += '            <i class="fa fa-file-{{ $company->logo->aggregate_type }}-o"></i> {{ $company->logo->basename }}';
                attachment_html += '        </span>';
                attachment_html += '    </a>';
                attachment_html += '    {!! Form::open(['id' => 'attachment-' . $company->logo->id, 'method' => 'DELETE', 'url' => [url('uploads/' . $company->logo->id)], 'style' => 'display:inline']) !!}';
                attachment_html += '    <a id="remove-attachment" href="javascript:void();">';
                attachment_html += '        <span class="text-danger"><i class="fa fa fa-times"></i></span>';
                attachment_html += '    </a>';
                attachment_html += '    {!! Form::close() !!}';
                attachment_html += '</span>';

                $('.fancy-file .fake-file').append(attachment_html);

                $(document).on('click', '#remove-attachment', function (e) {
                    confirmDelete("#attachment-{!! $company->logo->id !!}", "{!! trans('general.attachment') !!}", "{!! trans('general.delete_confirm', ['name' => '<strong>' . $company->logo->basename . '</strong>', 'type' => strtolower(trans('general.attachment'))]) !!}", "{!! trans('general.cancel') !!}", "{!! trans('general.delete')  !!}");
                });
            @endif
        });
    </script>
@endpush
