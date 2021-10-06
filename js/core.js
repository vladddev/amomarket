var marketCore = function () {
    var self = this;
    var system = self.system;
    var user = AMOCRM.constant('user');
    var amouser = user.login;
    var amohash = user.api_key;
    var amodomain = AMOCRM.constant('account').subdomain;
    var widgetName = '';

    this.callbacks = {
        render: function () {
            widgetName = self.params.widget_code;
            email = user.login;

            if (window.location.href.indexOf('settings/widgets/' + widgetName + '/') != -1) {
                self.crm_post(
                    'https://marketplace.market.ru/domain/' + amodomain + '-amocrm-ru/', {},
                    function (answer) {
                        $('#work-area-' + widgetName).html(answer);
                    }
                );

                // fetch('https://marketplace.market.ru/domain/' + amodomain + '-amocrm-ru/', {
                //     mode: 'no-cors'
                // })
                //     .then(data => data.text()).then(data => {
                //         $('#work-area').html(data);
                //     });
            }
            //always

            return true;
        },
        init: function () {
            $(document).on('click', '.amomarketwidget', function (e) {
                const slug = $(this).attr('data-slug');

                self.crm_post(
                    'https://marketplace.market.ru/wp-content/themes/amomarket/widgets/files/' + slug + '/view.php', {},
                    function (answer) {
                        $('#work-area-' + widgetName).html(answer);
                    }
                );
            })
            //once
            return true;
        },
        bind_actions: function () {
            return true;
        },
        settings: function () {
            // console.log(123);
            return true;
        },
        onSave: function () {
            console.log("object");
            return true;
        },
        destroy: function () {
            return true;
        },
        loadPreloadedData: function () {
            $('#' + widgetName).prepend('<div id="market-core-tab"></div>');
            data = {
                login: amouser,
                hash: amohash,
                domain: amodomain,
                id: AMOCRM.constant('card_id')
            };

            self.crm_post(
                'https://core.market.ru/widgets/sales/?action=loadData', {
                    data
                },
                function (data) {
                    console.log(data)
                    data = JSON.parse(data)
                    $('#market-core-tab').html(data.html);
                }
            );

            return new Promise(_.bind(function (resolve, reject) {
                resolve()
            }), this);
        },
        loadElements: function () {
            return new Promise(_.bind(function (resolve, reject) {
                resolve()
            }), this);
        },
        linkCard: function () {
            return new Promise(_.bind(function (resolve, reject) {
                resolve();
            }), this);
        },
        searchDataInCard: function () {
            return new Promise(_.bind(function (resolve, reject) {
                resolve();
            }), this);
        },
        contacts: {
            //select contacts in list and clicked on widget name
            selected: function () {}
        },
        leads: {
            //select leads in list and clicked on widget name
            selected: function () {}
        },
        tasks: {
            //select taks in list and clicked on widget name
            selected: function () {}
        }
    };
    return this;
};