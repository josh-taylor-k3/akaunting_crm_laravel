/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./../../bootstrap');

import Vue from 'vue';

import DashboardPlugin from './../../plugins/dashboard-plugin';

import Global from './../../mixins/global';
import {getQueryVariable} from './../../plugins/functions';

import AkauntingDashboard from './../../components/AkauntingDashboard';
import AkauntingWidget from './../../components/AkauntingWidget';

import {DatePicker, Tooltip} from 'element-ui';

Vue.use(DatePicker, Tooltip);

// plugin setup
Vue.use(DashboardPlugin);

const dashboard = new Vue({
    el: '#main-body',

    components: {
        [DatePicker.name]: DatePicker,
        [Tooltip.name]: Tooltip,
        AkauntingDashboard,
        AkauntingWidget
    },

    mixins: [
        Global
    ],

    data: function () {
        return {
            dashboard_modal: false,
            dashboard: {
                name: '',
                enabled: 1,
                type: 'create',
                dashboard_id: 0
            },
            widget_modal: false,
            widgets: {},
            widget: {
                id: 0,
                name: '',
                class: '',
                width: '',
                action: 'create',
                sort: 0,
            },
            filter_date: [],

        };
    },

    mounted() {
        let start_date = getQueryVariable('start_date');

        if (start_date) {
            let end_date = getQueryVariable('end_date');

            this.filter_date.push(start_date);
            this.filter_date.push(end_date);
        }

        this.getWidgets();
    },

    methods:{
        // Create Dashboard form open
        onCreateDashboard() {
            this.dashboard_modal = true;
            this.dashboard.name = '';
            this.dashboard.enabled = 1;
            this.dashboard.type = 'create';
            this.dashboard.dashboard_id = 0;
        },

        // Edit Dashboard information
        onEditDashboard(dashboard_id) {
            var self = this;

            axios.get(url + '/common/dashboards/' + dashboard_id + '/edit')
                .then(function (response) {
                    self.dashboard.name = response.data.name;
                    self.dashboard.enabled = response.data.enabled;
                    self.dashboard.type = 'edit';
                    self.dashboard.dashboard_id = dashboard_id;

                    self.dashboard_modal = true;
                })
                .catch(function (error) {
                    self.dashboard_modal = false;
                });
        },

        // Get All Widgets
        getWidgets() {
            var self = this;

            axios.get(url + '/common/widgets')
            .then(function (response) {
                self.widgets = response.data;
            })
            .catch(function (error) {
            });
        },

        // Add new widget on dashboard
        onCreateWidget() {
            this.widget_modal = true;
        },

        // Edit Dashboard selected widget setting.
        onEditWidget(widget_id) {
            var self = this;

            axios.get(url + '/common/widgets/' + widget_id + '/edit')
            .then(function (response) {
                self.widget.id = widget_id;
                self.widget.name = response.data.name;
                self.widget.class = response.data.class;
                self.widget.width = response.data.settings.width;
                self.widget.action = 'edit';
                self.widget.sort = response.data.sort;

                self.widget_modal = true;
            })
            .catch(function (error) {
                self.widget_modal = false;
            });
        },

        onCancel() {
            this.dashboard_modal = false;

            this.dashboard.name = '';
            this.dashboard.enabled = 1;
            this.dashboard.type = 'create';
            this.dashboard.dashboard_id = 0;

            this.widget_modal = false;

            this.widget.id = 0;
            this.widget.name = '';
            this.widget.class = '';
            this.widget.width = '';
            this.widget.action = 'create';
            this.widget.sort = 0;
        },

        onChangeFilterDate() {
            if (this.filter_date) {
                window.location.href = url + '?start_date=' + this.filter_date[0] + '&end_date=' + this.filter_date[1];
            } else {
                window.location.href = url;
            }
        },
    }
});
