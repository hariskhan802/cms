    
    $(document).ready(function(){
        var app_url = localStorage.getItem('app_url');
        function __ajax($this, data = null, type = 'post', URL = null){
            var formData;
            if ($this)
                formData = new FormData($this);

            for(dd in data) {
                for(d in Object.keys(data[dd])) {
                    formData.append(Object.keys(data[dd])[d], Object.values(data[dd])[d]);
                }
            }
            var URL = URL ? URL : $($this).attr('action');

            return $.ajax({
                        url: URL,
                        type: type,
                        data: formData,
                        contentType: false,
                        processData: false,
                        cache: false,
                        error: function(data) {
                            if (!isJSON(data.responseText)) 
                                return false;

                            var data = JSON.parse(data.responseText);
                            if ('errors' in data) {
                                alert(data.errors);
                                window.location = localStorage.getItem('app_url')+'/admin';
                            }
                            else {
                                alert('Something went wrong!');
                            }
                            $('[name="submit"]').prop('disabled', false);
                        },
                        complete: function() {
                            $('.error-msg').hide();
                            setTimeout(function() {
                                $('.error-msg').each(function() {
                                    $(this).html($(this).html().split('.').join('<br />'));
                                });
                                $('.error-msg').show();
                            }, 10);
                            $('[name="submit"]').removeAttr('disabled');
                            
                            
                        }
                    });
        }
        function isHTML(str) {
            var a = document.createElement('div');
            a.innerHTML = str;

            for (var c = a.childNodes, i = c.length; i--; ) {
                if (c[i].nodeType == 1) return true; 
            }

            return false;
        }
        function convertToSlug(str) {
            str = str.replace(/^\s+|\s+$/g, '');
            str = str.toLowerCase();

            var from = "ãàáäâẽèéëêìíïîõòóöôùúüûñç·/_,:;";
            var to   = "aaaaaeeeeeiiiiooooouuuunc------";
            for (var i = 0, l = from.length; i < l; i++) {
                str = str.replace(new RegExp(from.charAt(i), 'g'), to.charAt(i));
            }

            str = str.replace(/[^a-z0-9 -]/g, '')
                    .replace(/\s+/g, '-')
                    .replace(/-+/g, '-');

            return str;
        }
        
        function __is_image($image = '') {
            $image_extensions = localStorage.getItem('image_extensions');
            $image = $image+'';
            $imageExtensions = $image_extensions.split(',');
            $explodeImage = $image.split('.');
            $extension = $explodeImage[($explodeImage.length-1)];
            return $imageExtensions.indexOf($extension) != -1;
        }
        function checkCheckboxes($this = '', isRoleSettingPage = true) {
            var isFormCheckboxChecked1 = true;
            var activeTab = isRoleSettingPage ? $('.tabs-wrap .tabs-menu ul li.active a').attr('href') : '';
            if (activeTab === undefined)
                activeTab = '';
            $(activeTab+' .form-c-wrap').each(function(i) {
                var isFormCheckboxChecked2 = true;
                if (!isRoleSettingPage) {
                    $(this).find('.checkbox-fg').each(function() {
                        if (!$(this).find('[type="checkbox"]').is(':checked')) {
                            isFormCheckboxChecked2 = false;
                        }
                    });
                    $(this).find('.checked-roles').prop('checked', isFormCheckboxChecked2);
                }
                else if (isRoleSettingPage) {
                    $(this).find('.checkbox-fg').each(function() {
                        if (!$(this).find('[type="checkbox"]').prop('checked')) {
                            isFormCheckboxChecked2 = false;
                        }
                    });
                    
                    $(this).find('.checked-roles').prop('checked', isFormCheckboxChecked2);
                    if (!$(this).find('.m-label + .checked-roles').prop('checked')) {
                        isFormCheckboxChecked1 = false;
                    }
                }
                
            });
            $(activeTab+' .form-head h6 + .checked-roles').prop('checked', isFormCheckboxChecked1);
        }
        function isJSON(str) {
            try {
                if (JSON.parse(str) && parseInt(str)) {
                    return false;
                }
            } catch (e) {
                return false;
            }
            return true;
        }
        function edit_page_req($mThis = null) {
            $('#add-edit-modal form .modal-title').text('Edit');
            var formAction = '';
            if ($mThis) {
                formAction = $($mThis).attr('href');
            }
            else {
                formAction = app_url+'/admin/'+$('.edit-page').attr('page-name')+'/edit/'+$('.edit-page').attr('edit-page-id');
            }
            
            $('.user-m-wrap .password-field').hide();
            $('[type="submit"][value="Save Draft"]').hide();
            $('#add-edit-modal form').attr('action', formAction)
            $('#add-edit-modal').modal('toggle');
            __ajax(null, null, 'get', formAction).then(function(data) {
                
                if (data.status == 'success') {
                    var post_status = '';
                    var item = data.item;
                    if (data.item.post_status == 'drafted') {
                        post_status = 'Publish';
                    }
                    else {
                        post_status = 'Update';
                    }
                    $('[type="submit"][value="Publish"]').val(post_status);
                    $('.edit-page form [type="submit"][value="Add"] ').val('Update');
                    $('[name="_status"]').val(post_status);
                    
                    $.each(item,function(k, v) {
                        if (__is_image(v)) {
                            
                            $('[name="'+k+'"]').removeAttr('required');
                            $('[name="_'+k+'"]').val(v);
                            $('[name="'+k+'"] + .error-msg').next('.edit-img').remove();
                            $('[name="'+k+'"] + .error-msg').after('<img src="'+app_url+'/public/assets/images/'+v+'" class="edit-img" width="50" />');
                        }

                        else if (isHTML(v)) {
                            tinymce.activeEditor.setContent(v);
                        }
                        else if (k == 'cats' && Array.isArray(v)) {
                            
                            $('[name="'+k+'[]"] option').each(function() {
                                
                                if (v.indexOf(parseInt($(this).attr('value'))) != -1) {
                                    $(this).attr('selected', 'selected');
                                }
                            });
                        }
                        else if(isJSON(v)) {
                            
                            $('#add-edit-modal .checkbox-fg').each(function(i) {
                                var permissions = JSON.parse(v)
                                var elemPerm = $(this).find('input[type="checkbox"]').attr('name').replace('permissions[', '').replace(']', '') ;
                                if (permissions.indexOf(elemPerm) != -1) {
                                    $(this).find('input[type="checkbox"]').prop('checked', true);
                                }

                                checkCheckboxes(this, false);
                            });
                        }
                        else {
                            
                            $('[name="'+k+'"]').val(v);
                        }
                    });
                    
                }
                
            });
        }
        function set_admin_panel_sub_menu_position() {
            setTimeout(function() {
                $extraSpace = 16;
                if ($('#accordionSidebar').hasClass('toggled') || window.screen.width <= 676) {
                    $extraSpace = 32;
                }
                $('.sidebar-dark .nav-item .cus-sub-menu.collapse:not(.show)').css({'left': $('.sidebar-dark .nav-item .nav-link').width()+$extraSpace})
            }, 100);
            // $('.sidebar-dark .nav-item .cus-sub-menu.collapse')
        }

        
        
        checkCheckboxes('', true);
        /* $('.c-datatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: $('.c-datatable').attr('data-url'),
            columns: [
            { data: 'id' },
            { data: 'title' },
            { data: 'template' },
            { data: 'featured_image' },
            { data: 'date' },
            ],
            columnDefs: [ {
                "targets": 3,
                "data": "download_link",
                "render": function ( data, type, row, meta ) {
                  return '<img src="'+data+'">';
                }
              } ]
        }); 
        $('.c-datatable').DataTable();*/

        

        if (typeof tinymce != "undefined") {
            tinymce.init({
                selector: '#html-editor',

                height: 500,
                toolbar1: "undo redo | insert | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image code | mybutton",
                toolbar2: "table | hr removeformat | subscript superscript | charmap emoticons | print fullscreen | ltr rtl | visualchars visualblocks nonbreaking pagebreak restoredraft",
                menubar: false,
                plugins: [
                    "advlist autolink link image lists charmap print preview hr anchor pagebreak",
                    "searchreplace wordcount visualblocks visualchars insertdatetime media nonbreaking",
                    "table contextmenu directionality emoticons paste textcolor code"
                ],
                content_css: [
                    '//fonts.googleapis.com/css?family=Lato:300,300i,400,400i',
                    // '//www.tinymce.com/css/codepen.min.css'
                    ],
            });
        }
        
        $('#add-edit-modal form, .profile-wrap form, .roles-settings-wrap form, .post-type .post-type-form form').submit(function(e) {
            e.preventDefault();
            $('[name="submit"]').prop('disabled', true);
            $('.error-msg').empty();
            var extraData = [];
            extraData.push({submit: $(this).find('[type="submit"]').attr('value')});
            __ajax(this, extraData).then(function(data) {
                if('errors' in data && data.status == 'fail') {
                    $.each(data.errors,function(k, v) {
                        if ($('[name="'+k+'"]').next('.c-label').length > 0) {
                            $('[name="'+k+'"]').next('.c-label').next('.error-msg').html(v);
                        }
                        else {
                            $('[name="'+k+'"]').next('.error-msg').html(v);
                        }
                        // scrollTo(0, jQuery('[name="'+k+'"]').offset().top-40);
                    });
                    $('[name="submit"]').prop('disabled', false);
                }
                else if (data.status == 'success') {
                    $('.card.mb-4.c-msg.border-left-success').html(data.message).show('slow').delay(3000).hide('slow');
                    setTimeout(function(){
                        $('#add-edit-modal').modal('toggle');
                        if ($('.edit-page').length > 0) {
                            window.location.href = app_url+'/admin/'+$('.edit-page').attr('parent-page-name')
                        }
                        else {
                            if (postType != '') {
                                window.location.href = app_url+'/admin/posts/?post_type='+postType;
                            }else{
                                location.reload();
                            }
                        }
                    }, 2000);
                }
                
            });
        });
        $('.add-new-record.f-action-switcher').click(function() {
            $('#add-edit-modal form').attr('action', $(this).attr('data-form'));
            $('.user-m-wrap .password-field').show();
            $('#add-edit-modal form')[0].reset();
            $('[type="submit"][value="Publish"]').val('Publish');
            $('.user-m-wrap [type="submit"], .template-m-wrap [type="submit"]').val('Add');
            $('.edit-img').remove();
            $('#add-edit-modal form .modal-title').text('Add New');
        });
        $('[name="submit"][value="Publish"]').click(function() {
            if ($('#add-edit-modal form').length > 0) {
                if ($('#add-edit-modal form')[0].checkValidity()) {
                    $('[name="_status"]').val($(this).val())
                }
            }
            
        });
        $('.danger-delete').click(function(e) {
            e.preventDefault();
            $('#delete-modal').modal('toggle');
            $('.confirm-btn').attr('href', $(this).attr('href'));
        });

        $('.nofound-td').attr('colspan', $('.c-table thead th').length);
        
        $('body:not(.post-type) .edit-record').click(function(e) {
            e.preventDefault();
            history.pushState(null, 'CMS', $(this).attr('href'));
            $('body').addClass('edit-page');
            edit_page_req(this);

        });
        
        $('.all-checked').click(function() {
            if ($(this).is(':checked')) {
                $('[name="action_ids[]"]').prop('checked', true);
            }
            else {
                $('[name="action_ids[]"]').prop('checked', false);
            }
        });

        $('[name="rec_action"]').change(function(e) {
            var ids = [];
            $('[name="action_ids[]"]:checked').each(function() {
                ids.push(this.value)
            });
            if (ids.length == 0) {
                alert('You must select any record before doing this action!');
                $(this).val('');
                return false;
            }
            if ($(this).val() == 'delete') {
                if (confirm('Are you sure? \nSelect "OK" below if you want to delete.')) {
                    $(this).closest('form').attr('action', $(this).find('option:selected').attr('data-form'));
                    $(this).closest('form').trigger('submit');
                }
                else {
                    $(this).val('')
                }
            }
            else {
                $(this).closest('form').attr('action', $(this).find('option:selected').attr('data-form'));
                $(this).closest('form').trigger('submit');
            }
        });
        $('[name="title"]').bind('blur', function(e) {
            console.log($('[name="slug"]').val() == '');
            if ($('[name="slug"]').val() == '') {
                $('[name="slug"]').val(convertToSlug($(this).val()))
            }
        });
        
        $('[name="slug"]').bind('blur', function(e) {
            $(this).val(convertToSlug($(this).val()))
        });
        $('a.p-visibility').click(function(e) {
            if ($(this).prev('.error-msg').prev('input[type="password"]').length > 0) {
                $(this).prev('.error-msg').prev('input[type="password"]').attr('type', 'text');
                $(this).find('i').removeClass('fa-eye').addClass('fa-eye-slash');
            }
            else {
                $(this).prev('.error-msg').prev('input[type="text"]').attr('type', 'password');
                $(this).find('i').removeClass('fa-eye-slash').addClass('fa-eye')
            }
        });
        $('.tabs-wrap .tabs-menu ul li').click(function(e) {
            e.preventDefault();
            $('.tabs-wrap .tabs-div .tab-div').hide();
            $('.tabs-wrap .tabs-menu ul li').removeClass('active');
            $(this).addClass('active');
            $($(this).find('a').attr('href')).show();

            checkCheckboxes();
        });
        if (window.location.hash != '') {
            $('.tabs-wrap .tabs-menu ul li').removeClass('active');
            $('[href="'+window.location.hash+'"]').parent().addClass('active');
            $(window.location.hash).show();
        }
        else{
            $($('.tabs-wrap .tabs-menu ul li.active a').attr('href')).show();
        }

        $('.form-group-wrap input.checked-roles').click(function(e) {
            $(this).parent('.form-c-wrap').find('.checkbox-fg [type="checkbox"]').prop('checked', $(this).is(':checked'));
            checkCheckboxes();
        });

        $('.form-head h6 + .checked-roles').click(function(e) {
            $(this).closest('.tab-div').find('[type="checkbox"]').prop('checked', $(this).is(':checked'));
        });
       
        $('.checkbox-fg input[type="checkbox"]').click(function(e) {
            checkCheckboxes(this);
        });

        $('.s-m-perm').click(function(e) {
            $(this).prev('ul.perm-list').toggleClass('p-l-hide'); 
            $(this).text($(this).text() == 'Show More' ? 'Hide More' : 'Show More');
        });
        if ($('.edit-page').length > 0) {
            $('.edit-page').attr('page-name');
            $('.edit-page').attr('edit-page-id');
            $('#add-edit-modal').modal('toggle');
            edit_page_req();

        }
        $('#add-edit-modal').bind('hidden.bs.modal', function () {
           if ($('body.edit-page').length > 0) {
                $('body').removeClass('edit-page');
                history.pushState(null, 'CMS', app_url+'/admin/'+$('body').attr('parent-page-name'));
           }
        });

        $(document).on('focusin', function(e) {
            if ($(e.target).closest(".mce-window, .moxman-window").length) {
                e.stopImmediatePropagation();
            }
        });
        
        
        $(window).resize(function(e) {
            set_admin_panel_sub_menu_position()
        });
        $('#sidebarToggle').click(function(e) {
            set_admin_panel_sub_menu_position()
        });
        
        set_admin_panel_sub_menu_position();
    });
    function custom_date_format_field($this, str) {
        $($this).closest('.radio-btn-wrap.form-check').find('[type="radio"]').val(str);

    }
    $('.cus-date-format').bind('blur keyup', function() {
        custom_date_format_field(this, this.value);
    });

    $('.radio-btn-wrap.form-check').on('click', function(){
        setTimeout(() => {
            $(this).closest('.radio-m-wrap').find('.cus-d-t-hidden').val(($(this).find('.cus-date-radio-btn').is(':checked')+""))
        }, 70);
        
    });