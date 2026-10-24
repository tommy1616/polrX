polr.directive('editLongLinkModal', function () {
    return {
        scope: {
            oldLongLink: '=',
            linkEnding: '=',
            cleanModals: '='
        },
        templateUrl: '/directives/editLongLinkModal.html',
        transclude: true,
        controller: function ($scope, $element, $timeout) {
            var i18n = window.POLR_I18N || {};
            var toastTitles = i18n.toast || { success: 'Success', error: 'Error' };
            var jsMsg = i18n.admin_js || {};
            $scope.modalI18n = i18n.modals || {};

            $scope.init = function () {
                $element.find('.modal').on("hidden.bs.modal", function () {
                    $scope.$destroy();
                    $scope.cleanModals('editLongLink');
                });
            };

            $scope.saveChanges = function () {
                apiCall('admin/edit_link_long_url', {
                    'link_ending': $scope.linkEnding,
                    'new_long_url': $element.find('input').val()
                }, function(data) {
                    toastr.success(jsMsg.link_updated || 'Updated', toastTitles.success);
                }, function(err) {
                    toastr.error(jsMsg.invalid_url || 'Invalid URL', toastTitles.error);
                });
            };

            $scope.init();
        }
    };
});

polr.directive('editUserApiInfoModal', function () {
    return {
        scope: {
            userId: '=',
            apiActive: '=',
            apiKey: '=',
            apiQuota: '=',
            generateNewApiKey: '=',
            cleanModals: '='
        },
        templateUrl: '/directives/editUserApiInfoModal.html',
        transclude: true,
        controller: function ($scope, $element, $timeout) {
            var i18n = window.POLR_I18N || {};
            var toastTitles = i18n.toast || { success: 'Success', error: 'Error' };
            var jsMsg = i18n.admin_js || {};
            $scope.modalI18n = i18n.modals || {};

            $scope.init = function () {
                $element.find('.modal').on("hidden.bs.modal", function () {
                    $scope.$destroy();
                    $scope.cleanModals('editUserApiInfo');
                });

                $scope.apiActive = res_value_to_text($scope.apiActive);
            };

            $scope.toggleAPIStatus = function() {
                apiCall('admin/toggle_api_active', {
                    'user_id': $scope.userId,
                }, function(new_status) {
                    $scope.apiActive = res_value_to_text(new_status);
                    $scope.$digest();
                });
            };

            $scope.parentGenerateNewAPIKey = function($event) {
                $scope.generateNewApiKey($event, $scope.userId, false);
            };

            $scope.updateAPIQuota = function() {
                apiCall('admin/edit_api_quota', {
                    'user_id': $scope.userId,
                    'new_quota': parseInt($scope.apiQuota)
                }, function(next_action) {
                    toastr.success(jsMsg.quota_updated || 'Updated', toastTitles.success);
                });
            };

            $scope.init();
        }
    };
});

polr.controller('AdminCtrl', function($scope, $compile, $timeout) {
    var i18n = window.POLR_I18N || {};
    var dtI18n = i18n.datatables || {};
    var jsMsg = i18n.admin_js || {};
    var toastTitles = i18n.toast || { success: 'Success', error: 'Error' };
    var usersI18n = i18n.users || {};
    var linksI18n = i18n.links || {};

    $scope.state = {
        showNewUserWell: false
    };
    $scope.datatables = {};
    $scope.modals = {
        editLongLink: [],
        editUserApiInfo: []
    };
    $scope.newUserParams = {
        username: '',
        userPassword: '',
        userEmail: '',
        userRole: ''
    };

    $scope.syncHash = function() {
        var url = document.location.toString();
        if (url.match('#')) {
            $('.admin-nav a[href=#' + url.split('#')[1] + ']').tab('show');
        }
    };

    $scope.cleanModals = function(modalType) {
        $timeout(function () {
            $scope.modals[modalType].shift();
        });

        $scope.reloadLinkTables();
    };

    $scope.initTables = function() {
        var datatables_config = {
            'autoWidth': false,
            'processing': true,
            'serverSide': true,
            'language': {
                'sProcessing': dtI18n.processing || 'Processing...',
                'sLengthMenu': dtI18n.length_menu || 'Show _MENU_ entries',
                'sZeroRecords': dtI18n.zero_records || 'No matching records found',
                'sInfo': dtI18n.info || 'Showing _START_ to _END_ of _TOTAL_ entries',
                'sInfoEmpty': dtI18n.info_empty || 'Showing 0 to 0 of 0 entries',
                'sInfoFiltered': dtI18n.info_filtered || '(filtered from _MAX_ total entries)',
                'sSearch': dtI18n.search || 'Search:',
                'sLoadingRecords': dtI18n.loading || 'Loading...',
                'oPaginate': {
                    'sPrevious': dtI18n.previous || 'Previous',
                    'sNext': dtI18n.next || 'Next'
                }
            },

            'drawCallback': function () {
                $compile($(this))($scope);
            }
        };

        if ($('#admin_users_table').length) {
            $scope.datatables['admin_users_table'] = $('#admin_users_table').DataTable($.extend({
                "ajax": BASE_API_PATH + 'admin/get_admin_users',

                "columns": [
                    {className: 'wrap-text', data: 'username', name: 'username'},
                    {className: 'wrap-text', data: 'email', name: 'email'},
                    {data: 'created_at', name: 'created_at'},

                    {data: 'toggle_active', name: 'toggle_active', orderable: false, searchable: false},
                    {data: 'api_action', name: 'api_action', orderable: false, searchable: false},
                    {data: 'change_role', name: 'change_role', orderable: false, searchable: false},
                    {data: 'delete', name: 'delete', orderable: false, searchable: false}
                ]
            }, datatables_config));
        }
        if ($('#admin_links_table').length) {
            $scope.datatables['admin_links_table'] = $('#admin_links_table').DataTable($.extend({
                "ajax": BASE_API_PATH + 'admin/get_admin_links',

                "columns": [
                    {className: 'wrap-text', data: 'short_url', name: 'short_url'},
                    {className: 'wrap-text', data: 'long_url', name: 'long_url'},
                    {data: 'clicks', name: 'clicks'},
                    {data: 'created_at', name: 'created_at'},
                    {data: 'creator', name: 'creator'},

                    {data: 'disable', name: 'disable', orderable: false, searchable: false},
                    {data: 'delete', name: 'delete', orderable: false, searchable: false}
                ]
            }, datatables_config));
        }

        $scope.datatables['user_links_table'] = $('#user_links_table').DataTable($.extend({
            "ajax": BASE_API_PATH + 'admin/get_user_links',

            "columns": [
                {className: 'wrap-text', data: 'short_url', name: 'short_url'},
                {className: 'wrap-text', data: 'long_url', name: 'long_url'},
                {data: 'clicks', name: 'clicks'},
                {data: 'created_at', name: 'created_at'}
            ]
        }, datatables_config));
    };

    $scope.reloadLinkTables = function () {
        if ('admin_links_table' in $scope.datatables) {
            $scope.datatables['admin_links_table'].ajax.reload(null, false);
        }

        $scope.datatables['user_links_table'].ajax.reload(null, false);
    };

    $scope.reloadUserTables = function () {
        $scope.datatables['admin_users_table'].ajax.reload(null, false);
    };

    $scope.toggleUserActiveStatus = function($event, user_id) {
        var el = $($event.target);

        apiCall('admin/toggle_user_active', {
            'user_id': user_id,
        }, function(new_status) {
            var text = (new_status == 1) ? (usersI18n.active || 'Active') : (usersI18n.inactive || 'Inactive');
            el.text(text);
            if (el.hasClass('btn-success')) {
                el.removeClass('btn-success').addClass('btn-danger');
            }
            else {
                el.removeClass('btn-danger').addClass('btn-success');
            }
        });
    };

    $scope.generateNewAPIKey = function($event, user_id, is_dev_tab) {
        var el = $($event.target);
        var status_display_elem = el.prevAll('.status-display');

        if (is_dev_tab) {
            status_display_elem = el.parent().prev().children();
        }

        apiCall('admin/generate_new_api_key', {
            'user_id': user_id,
        }, function(new_status) {
            if (status_display_elem.is('input')) {
                status_display_elem.val(new_status);
            } else {
                status_display_elem.text(new_status);
            }
        });
    };

    $scope.checkNewUserFields = function() {
        var response = true;

        $('.new-user-fields input').each(function () {
            if ($(this).val().trim() == '' || response == false) {
                response = false;
            }
        });

        return response;
    };

    $scope.addNewUser = function($event) {
        var username = ($scope.newUserParams.username || $('.new-user-username').val() || '').trim();
        var userPassword = $scope.newUserParams.userPassword || $('.new-user-password').val() || '';
        var userEmail = ($scope.newUserParams.userEmail || $('.new-user-email').val() || '').trim();
        var userRole = $scope.newUserParams.userRole || $('.new-user-role').val() || '';

        if (!username || !userPassword || !userEmail) {
            toastr.error(jsMsg.fields_cannot_be_empty || 'Fields cannot be empty.', toastTitles.error);
            return false;
        }

        apiCall('admin/add_new_user', {
            'username': username,
            'user_password': userPassword,
            'user_email': userEmail,
            'user_role': userRole,
        }, function(result) {
            toastr.success(jsMsg.user_created || 'User created.', toastTitles.success);
            $('#new-user-form').clearForm();
            $scope.newUserParams.username = '';
            $scope.newUserParams.userPassword = '';
            $scope.newUserParams.userEmail = '';
            $scope.newUserParams.userRole = '';
            $scope.datatables['admin_users_table'].ajax.reload();
        }, function (err) {
            var detail = '';
            if (err && err.responseJSON) {
                if (err.responseJSON.errors) {
                    var keys = Object.keys(err.responseJSON.errors);
                    if (keys.length > 0 && err.responseJSON.errors[keys[0]].length > 0) {
                        detail = ' (' + err.responseJSON.errors[keys[0]][0] + ')';
                    }
                } else if (err.responseJSON.message) {
                    detail = ' (' + err.responseJSON.message + ')';
                }
            }
            toastr.error((jsMsg.user_create_error || 'Error creating user.') + detail, toastTitles.error);
        });
    };

    $scope.deleteUser = function($event, user_id) {
        var delete_user_confirm = window.confirm(jsMsg.confirm_delete_user || 'Delete user?');

        if (delete_user_confirm) {
            apiCall('admin/delete_user', {
                'user_id': user_id,
            }, function(new_status) {
                toastr.success(jsMsg.user_deleted || 'User deleted.', toastTitles.success);
                $scope.reloadUserTables();
            });
        }
    };

    $scope.changeUserRole = function(role, user_id) {
        apiCall('admin/change_user_role', {
            'user_id': user_id,
            'role': role,
        }, function(result) {
            toastr.success(jsMsg.role_updated || 'Role updated.', toastTitles.success);
        });
    };

    $scope.openAPIModal = function($event, username, api_key, api_active, api_quota, user_id) {
        $scope.modals.editUserApiInfo.push({
            apiKey: api_key,
            apiQuota: parseInt(api_quota),
            userId: user_id,
            apiActive: api_active
        });

        $timeout(function () {
            $('#edit-user-api-info-' + user_id).modal('show');
        });
    };

    $scope.deleteLink = function($event, link_ending) {
        var delete_link_confirm = window.confirm(jsMsg.confirm_delete_link || 'Delete link?');

        if (delete_link_confirm) {
            apiCall('admin/delete_link', {
                'link_ending': link_ending,
            }, function(new_status) {
                toastr.success(jsMsg.link_deleted || 'Link deleted.', toastTitles.success);
                $scope.reloadLinkTables();
            });
        }
    };

    $scope.toggleLink = function($event, link_ending) {
        var el = $($event.target);

        apiCall('admin/toggle_link', {
            'link_ending': link_ending,
        }, function(next_action) {
            toastr.success(jsMsg.link_toggled || 'Updated.', toastTitles.success);
            if (next_action == 'Disable') {
                el.removeClass('btn-success');
                el.addClass('btn-danger');
                el.text(linksI18n.disable || 'Disable');
            } else {
                el.removeClass('btn-danger');
                el.addClass('btn-success');
                el.text(linksI18n.enable || 'Enable');
            }
        });
    };

    $scope.editLongLink = function(link_ending, old_long_link) {
        $scope.modals.editLongLink.push({
            linkEnding: link_ending,
            oldLongLink: old_long_link,
        });

        $timeout(function () {
            $('#edit-long-link-' + link_ending).modal('show');
        });
    };

    $scope.init = function() {
        $('.admin-nav a').click(function(e) {
            e.preventDefault();
            $(this).tab('show');
        });
        $scope.syncHash();

        $(window).on('hashchange', function() {
            $scope.syncHash();
        });

        $("a[href^=#]").on("click", function(e) {
            history.pushState({}, '', this.href);
        });

        $scope.initTables();
    };

    $scope.init();
});
