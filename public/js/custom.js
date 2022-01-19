$(document).ready(function() {
    $(".sidebar-themes").find("li").on("click", setTheme);

    $("#options-header-default").on("click", setHeaderTheme);
    $("#options-header-inverse").on("click", setHeaderTheme);

    $("#options-main-style").on("click", setPageStyle);
    $("#options-main-style-alt").on("click", setPageStyle);

    $('.remove-button').on('click', function() {
        let id = $(this).attr('data-id');

        if (confirm('Are you sure you want to delete this? \nWe won\'t be recover this.')) {
            $('#remove-form-' + id).submit();
        }
    });

    $('.remove-finance-vehicles').on("click", function() {
        let self  = $(this),
            title = self.data("original-title");

        if (confirm(title + " ?")) {
            $('#remove-finance-vehicles-form').submit();
        }
    });

    $('.confirm-vehicle').on("click", function() {
        let id   = $(this).attr('data-id'),
            form = $('#confirm-vehicle-form-' + id);

        form.submit();

        sendWhatsAppMessage(form.serializeArray(), '1');
    });

    $('.cancel-vehicle').on("click", function() {
        let id   = $(this).attr('data-id'),
            form = $('#cancel-vehicle-form-' + id);

        form.submit();

        sendWhatsAppMessage(form.serializeArray(), '2');
    });

    $('.confirm-vehicle-button').on('click', function() {
        let id       = $(this).attr('data-id'),
            checkbox = $(this).parents('.switch').find('input[type="radio"]'),
            cancel   = $(this).attr('data-cancel');

        if (cancel == "1") {
            alert("This vehicle already cancelled! Please uncancelled this and try again.");

            return false;
        }

        if (!checkbox.is(':checked') && confirm('Are you sure you want to confirm this vehicle?')) {
            $("#modal-confirm-select-seizer-" + id).modal("show");

            // Enable / Disable confirm buttons for submit.
            $("#modal-confirm-select-seizer-" + id).find('select#confirm-users').unbind().on("change", function() {
                let self = $(this);

                if (self.val() != "") {
                    $("#modal-confirm-select-seizer-" + id).find('.confirm-vehicle').fadeIn(300);
                } else {
                    $("#modal-confirm-select-seizer-" + id).find('.confirm-vehicle').fadeOut(100);
                }
            });

            // Turn off toggle button.
            $("#modal-confirm-select-seizer-" + id).on("hide.bs.modal", function () {
                checkbox.prop('value', 'off');

                checkbox.prop('checked', false);
            });

            // checkbox.prop('value', 'on');

            // checkbox.prop('checked', true);

            // $('#confirm-vehicle-form-' + id).submit();

            // return true;
        } else if (checkbox.is(':checked') && confirm('Are you sure you want to unconfirmed this vehicle?')) {
            checkbox.prop('value', 'off');

            checkbox.prop('checked', false);

            $('#confirm-vehicle-form-' + id).submit();

            return true;
        } else {
            // checkbox.prop('value', '');

            // checkbox.click();

            return false;
        }
    });

    $('.cancel-vehicle-button').on('click', function() {
        let id         = $(this).attr('data-id'),
            checkbox   = $(this).parents('.switch').find('input[type="radio"]'),
            confirmed  = $(this).attr('data-confirm');

        if (confirmed == "1") {
            alert("This vehicle already confirmed! Please unconfirmed this and try again.");

            return false;
        }

        if (!checkbox.is(':checked') && confirm('Are you sure you want to cancel this vehicle?')) {
            /* checkbox.prop('value', 'on');

            checkbox.prop('checked', true);

            $('#cancel-vehicle-form-' + id).submit();

            return true; */

            $("#modal-cancel-select-seizer-" + id).modal("show");

            // Enable / Disable confirm buttons for submit.
            $("#modal-cancel-select-seizer-" + id).find('select#cancel-users').unbind().on("change", function() {
                let self = $(this);

                if (self.val() != "") {
                    $("#modal-cancel-select-seizer-" + id).find('.cancel-vehicle').fadeIn(300);
                } else {
                    $("#modal-cancel-select-seizer-" + id).find('.cancel-vehicle').fadeOut(100);
                }
            });

            // Turn off toggle button.
            $("#modal-cancel-select-seizer-" + id).on("hide.bs.modal", function () {
                checkbox.prop('value', 'off');

                checkbox.prop('checked', false);
            });
        } else if (checkbox.is(':checked') && confirm('Are you sure you want to uncancelled this vehicle?')) {
            checkbox.prop('value', 'off');

            checkbox.prop('checked', false);

            $('#cancel-vehicle-form-' + id).submit();

            return true;
        } else {
            // checkbox.prop('value', '');

            // checkbox.click();

            return false;
        }
    });

    $('.subscribe-user-button').on('click', function() {
        let id         = $(this).attr('data-id'),
            checkbox   = $(this).parents('.switch').find('input[type="radio"]'),
            subscribed = $(this).attr('data-subscribed');

        if (!checkbox.is(':checked') && confirm('Are you sure you want to subscribe this user?')) {
            checkbox.prop('value', 'on');

            checkbox.prop('checked', true);

            $('#subscribe-user-form-' + id).submit();

            return true;
        } else if (checkbox.is(':checked') && confirm('Are you sure you want to unsubscribe this user?')) {
            checkbox.prop('value', 'off');

            // checkbox.prop('checked', false);

            $('#subscribe-user-form-' + id).submit();

            return true;
        } else {
            // checkbox.prop('value', '');

            // checkbox.click();

            return false;
        }
    });

    $(document).find('.multiselect').multiselect({
        templates: {
            // Use the Awesome Bootstrap Checkbox structure
            li: '<li class="checkList"><a tabindex="0"><div class="aweCheckbox aweCheckbox-danger"><label for=""></label></div></a></li>'
        }
    });

    $(document).find('.multiselect-container div.aweCheckbox').each(function(index) {
        let id      = 'multiselect-' + index,
            $input  = $(this).find('input');

        // Associate the label and the input
        $(this).find('label').attr('for', id);
        $input.attr('id', id);

        // Remove the input from the label wrapper
        $input.detach();

        // Place the input back in before the label
        $input.prependTo($(this));

        $(this).click(function(e) {
            // Prevents the click from bubbling up and hiding the dropdown
            e.stopPropagation();
        });
    });

    $(document).find('.multiselect-inline').multiselect({
        templates: {
            // Use the Awesome Bootstrap Checkbox structure
            li: '<li class="checkList"><a tabindex="0"><div class="aweCheckbox aweCheckbox-danger multiselect-inline"><label for=""></label></div></a></li>'
        }
    });

    $(document).find('.multiselect-container div.multiselect-inline').each(function(index) {
        let id      = 'multiselect-' + index,
            $input  = $(this).find('input');

        // Associate the label and the input
        $(this).find('label').attr('for', id);
        $input.attr('id', id);

        // Remove the input from the label wrapper
        $input.detach();

        // Place the input back in before the label
        $input.prependTo($(this));

        $(this).click(function(e) {
            // Prevents the click from bubbling up and hiding the dropdown
            e.stopPropagation();
        });

        $(this).parents('ul').attr('style','display: block; position: unset; float: unset;');
        $(this).parents('ul').parent('.btn-group').attr('style','width: 100%;');
        $(this).parents('ul').parent('.btn-group').find('button.dropdown-toggle').attr('style','width: 100%;');
    });

    // Update password ajax.
    $(document).find("#admin-password-update").on('click', adminPasswordUpdate);

    // Sub Seizer activity map.
    $('.show-map').on("click", function() {
        let latitude  = $(this).attr('data-latitude'),
            longitude = $(this).attr('data-longitude'),
            myCenter  = new google.maps.LatLng(parseFloat(latitude), parseFloat(longitude));

        $('#modal-activity-map').find('.modal-body').empty();

        let map = new google.maps.Map(
            document.getElementById('activity-map'), {
                center: myCenter,
                zoom: 12
            }
        );

        new google.maps.Marker({
            position: myCenter,
            map: map
        });

        $('#modal-activity-map').modal("show");
    });

    // Report page radio buttons.
    $(document).find('#is_confirm').on("click", function() {
        $(document).find('#is_cancel').prop("checked", false);
    });
    $(document).find('#is_cancel').on("click", function() {
        $(document).find('#is_confirm').prop("checked", false);
    });
});

function setTheme() {
    let self = $(this).find('a');

    $.ajax({
        url: setThemeCookieRoute + "?theme=" + self.data("theme")
    });
}

function setHeaderTheme() {
    let self = $(this);

    $.ajax({
        url: setThemeCookieRoute + "?header=" + self.data("type")
    });
}

function setPageStyle() {
    let self = $(this);

    $.ajax({
        url: setThemeCookieRoute + "?page=" + self.data("type")
    });
}

function adminPasswordUpdate(event) {
    event.preventDefault();

    let self     = $(this),
        form     = self.parents("form"),
        formData = form.serializeArray(),
        postData = {},
        _token   = null;

    form.find("strong[id^='error_']").html("");
    form.find("strong[id^='success_']").html("");

    $.each(formData, function(index, data) {
        if (data.name == "_token") {
            _token = data.value;
        } else {
            postData[data.name] = data.value;
        }
    });

    $.ajax ({
        data: postData,
        type: "POST",
        headers: {'X-CSRF-TOKEN': _token },
        url: form.prop("action"),
        cache: false,
        success: function(data) {
            if (data.success) {
                form.find("#success_general").html(data.success).fadeIn(200);
            } else {
                form.find("#error_" + data.element).html(data.error).fadeIn(200);
            }
        },
        error: function(data) {
            console.log(data);

            form.find("#error_general").html("Something went wrong. Please contact superadmin or reload the page.").fadeIn(200);
        }
    });
}

// 1: Confirm, 2: Cancel
function sendWhatsAppMessage(formArray, isConfirmedCancelled) {
    let postData = {},
        _token   = null,
        ajaxUrl  = whatsAppMessageRoute;

    $.each(formArray, function(index, data) {
        if (data.name == "_token") {
            _token = data.value;
        } else {
            postData[data.name] = data.value;
        }
    });

    postData['is_confirm_cancelled'] = isConfirmedCancelled;

    $.ajax ({
        data: postData,
        type: "POST",
        headers: {'X-CSRF-TOKEN': _token },
        url: ajaxUrl,
        cache: false,
        success: function(data) {
            if (data.is_success) {
                window.open(data.whats_app_web_url, '_blank').focus();
            } else {
                alert(data.msg);
            }
        },
        error: function(data) {
            console.log(data);
        }
    });
}
