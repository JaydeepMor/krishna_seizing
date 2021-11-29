$(document).ready(function() {
    $(".sidebar-themes").find("li").on("click", setTheme);

    $("#options-header-default").on("click", setHeaderTheme);
    $("#options-header-inverse").on("click", setHeaderTheme);

    $("#options-main-style").on("click", setPageStyle);
    $("#options-main-style-alt").on("click", setPageStyle);

    $('.remove-button').on('click', function() {
        let id = $(this).attr('data-id');

        if (confirm('Are you sure you want to delete this?')) {
            $('#remove-form-' + id).submit();
        }
    });

    $('.confirm-vehicle').on("click", function() {
        let id = $(this).attr('data-id');

        $('#confirm-vehicle-form-' + id).submit();
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
            $("#modal-select-seizer-" + id).modal("show");

            checkbox.prop('value', 'on');

            checkbox.prop('checked', true);

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
            checkbox.prop('value', 'on');

            checkbox.prop('checked', true);

            $('#cancel-vehicle-form-' + id).submit();

            return true;
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
            longitude = $(this).attr('data-longitude');

        $('#modal-activity-map').find('.modal-body').empty();

        new google.maps.Map(
            document.getElementById('activity-map'), {
                center: {
                    lat: parseFloat(latitude),
                    lng: parseFloat(longitude)
                },
                zoom: 8
            }
        );

        $('#modal-activity-map').modal("show");
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
