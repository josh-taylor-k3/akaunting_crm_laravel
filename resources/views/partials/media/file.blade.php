<ul class="mailbox-attachments clearfix margin-top">
    <li>
        @if ($file->aggregate_type != 'image')
        <span class="mailbox-attachment-icon">
            <i class="fa fa-file-{{ $file->aggregate_type }}-o"></i>
        </span>
        @else
        <span class="mailbox-attachment-icon has-img">
            <img src="{{ url('uploads/' . $file->id) }}" alt="{{ $file->basename }}">
        </span>
        @endif

        <div class="mailbox-attachment-info">
            <a href="javascript:void();" class="mailbox-attachment-name">
                <p class="mailbox-attachment-file-name">
                    <i class="fa fa-paperclip"></i> {{ $file->basename }}
                </p>
            </a>

            <span class="mailbox-attachment-size">
              {{ $file->readableSize() }}

                {!! Form::open([
                'id' => 'attachment-' . $file->id,
                'method' => 'DELETE',
                'url' => [url('uploads/' . $file->id)],
                'style' => 'display:inline'
            ]) !!}
                <a id="remove-attachment" href="javascript:void();" class="btn btn-danger btn-xs pull-right">
                    <i class="fa fa fa-times"></i>
                </a>
                {!! Form::close() !!}

                <a href="{{ url('uploads/' . $file->id . '/download') }}" class="btn btn-info btn-xs pull-right mailbox-attachment-download">
                    <i class="fa fa-cloud-download"></i>
                </a>
            </span>
        </div>
    </li>
</ul>
