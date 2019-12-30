<div id="widgets-total-profit" class="{{ $model->settings->width }}">
    <div class="card bg-gradient-success card-stats">
        <span>
            <div class="dropdown card-action-button">
                <a class="btn btn-sm items-align-center py-2 mr-0 shadow-none--hover" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fa fa-ellipsis-v text-white"></i>
                </a>

                <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                    {!! Form::button(trans('general.edit'), array(
                        'type'    => 'button',
                        'class'   => 'dropdown-item',
                        'title'   => trans('general.edit'),
                        '@click'  => 'onEditWidget(' . $model->id . ')'
                    )) !!}
                    <div class="dropdown-divider"></div>
                    {!! Form::deleteLink($model, 'common/widgets') !!}
                </div>
            </div>
        </span>

        <div class="card-body">
            <div class="row">
                <div class="col">
                    <h5 class="text-uppercase text-white mb-0">{{ $model->name }}</h5>
                    <span class="font-weight-bold text-white mb-0">@money($totals['current'], setting('default.currency'), true)</span>
                </div>

                <div class="col-auto">
                    <div class="icon icon-shape bg-white text-success rounded-circle shadow">
                        <i class="fa fa-heart"></i>
                    </div>
                </div>
            </div>

            <p class="mt-3 mb-0 text-sm" title="{{ trans('dashboard.open_profit') }}: {{ $totals['open'] }}<br>{{ trans('dashboard.overdue_profit') }}: {{ $totals['overdue'] }}" data-toggle="tooltip" data-html="true">
                <span class="text-white">{{ trans('general.upcoming') }}</span>
                <span class="text-white font-weight-bold float-right">{{ $totals['open'] }} / {{ $totals['overdue'] }}</span>
            </p>
        </div>
    </div>
</div>
