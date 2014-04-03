var completeHtml = '<i class="glyphicon glyphicon-ok-circle"></i> <span class="hidden-xs">Complete</span>';
var incompleteHtml = '<i class="glyphicon glyphicon-ban-circle"></i> <span class="hidden-xs">Incomplete</span>';
var setupComplete = '<strong>Congratulations!</strong> Setup is complete. Your Swarm account manager has been notified. You will be emailed when your account is active.';
var setupIncomplete = 'Your account set up is not complete. Please complete each step to ensure we have as much data as possible...';


$(function() {
    $('#completionForm input.step_completion_status').each(function() {
        //get id of step
        var stepId = $(this).data('step');
        var stepWrapper = $('#' + stepId).parent('.accordion-group').find('.statusWrapper');
        var theVal = $(this).val();
        if (theVal == "yes") {
            stepWrapper.find('.step_status').html(completeHtml).removeClass('alert-danger').addClass('alert-success');
            stepWrapper.find('.markComplete').fadeOut();

        } else {
            stepWrapper.find('.step_status').html(incompleteHtml).removeClass('alert-success').addClass('alert-danger');
            stepWrapper.find('.markComplete').fadeIn();

        }
    });
    //set up hardware and POS change functions to show different options
    $('#hardwareSelect').on('change', function(e) {
        e.preventDefault();
        var provider = $(this).val();
        $('.hardwareOption').hide();
        if (provider != 0) {
            $('.hardwareOption.' + provider).show();
        }
        ;
    });
    $('#unit_of_measurement').on('change', function(e) {
        e.preventDefault();
        var theval = $(this).val();
        if (theval == "feet") {
            $('.measurement').html('ft');
        } else if (theval == "meters") {
            $('.measurement').html('m');
        }
    });
    //make sure options are shown by default
    $('#hardwareSelect, #posSelect, #unit_of_measurement').trigger('change');
    adjustPercentComplete(false);
    //sliders to change/show options
    $('.option_show button').on('click', function() {
        var btnValue = $(this).val();
        var toShow = $(this).parent('.option_show').data('show');
        var toHide = $(this).parent('.option_show').data('hide');
        if (btnValue == 'yes') {
            $('.' + toShow).show();
            $('.' + toHide).hide();
        } else {
            $('.' + toShow).hide();
            $('.' + toHide).show();
        }
    });
    //check the values on load to make sure they are showin'
    $('.option_show button.active').each(function() {
        var btnValue = $(this).val();
        var toShow = $(this).parent('.option_show').data('show');
        var toHide = $(this).parent('.option_show').data('hide');
        if (btnValue == 'yes') {
            $('.' + toShow).show();
            $('.' + toHide).hide();
        } else {
            $('.' + toShow).hide();
            $('.' + toHide).show();
        }
    });

    //form ajax submissions
    $(".form-actions input.btn, a.pos-oauth").click(function() {        
        var buttonPushed = $(this);
        var theForm = buttonPushed.parents('form');
        var currentStep = buttonPushed.parents('.accordion-group');

        //if hourly step, check for proper formatting
        if (buttonPushed.attr('id') == "hoursSubmit") {
            if (validateTimes(false)) {
                //continue on sir
            } else {
                //errors, so stop
                return false;
            }
        }
        ;

        showLoading();
        $.ajax({
            type: "POST",
            url: "/setup",
            data: theForm.serialize(),
            success: function(data)
            {
                stopLoading();
                var nextStep = currentStep.next('.accordion-group').find('.accordion-heading .accordion-toggle');
                nextStep.click();
                var nextStepToScroll = currentStep.find('.collapse').attr('id');
                if (nextStepToScroll) {
                    $(document).scrollTop($("#" + currentStep.find('.collapse').attr('id')).offset().top);
                }
                if(buttonPushed.hasClass('pos-oauth')){                    
                    window.location.href = buttonPushed.attr('action');
                }

            }
        });
        markComplete(currentStep);

        return false; // avoid actual submit of the form.
    });

    //step completion
    $('.markComplete').on('click', function(e) {
        e.preventDefault();
        var step = $(this).parents('.accordion-group');

        if (!markComplete(step)) {
            alert('This step can not be marked as complete until the required fields are filled in.');
        }
        ;
    });
    $("#support_form").validate({
        rules: {
            email: {required: true, email: true},
            name: {required: true, maxlength: 100},
            message: {required: true}
        }
    });

    $('.checkboxSelector input:checkbox').change(function() {
        var checkval = $(this).is(':checked');
        var hiddenInput = $(this).next('input[type="hidden"]');
        hiddenInput.val(checkval);
    });


});



function markComplete(step) {
    //globals

    var incompletions = 0;

    //step specific vars
    var stepId = step.find('.accordion-body').attr('id');

    var statusBtn = step.find('.step_status');
    var markComplete = step.find('.markComplete');
    var theForm = step.find('form');
    var requiredFields = theForm.find('input.needed, select.needed, textarea.needed');


    //check for NEEDED fields if needed fields are not blank, then mark as complete and update hidden step input values
    requiredFields.each(function() {
        var myval = $(this).val();
        if (!myval) {
            incompletions++;
        }
        ;
    });

    if (incompletions == 0) {
        statusBtn.html(completeHtml).removeClass('alert-danger').addClass('alert-success');
        step.addClass('completedStep');
        markComplete.fadeOut();
        $('#completionForm input.step_completion_status[data-step="' + stepId + '"]').val('yes');
        saveCompletionStatuses();
    } else {
        statusBtn.html(incompleteHtml).removeClass('alert-success').addClass('alert-danger');
        markComplete.fadeIn();
        $('#completionForm input.step_completion_status[data-step="' + stepId + '"]').val('no');
        saveCompletionStatuses();
        return false;
    };
    if(!isAdmin){
        mixpanel.track("Setup Step Completed");
    }

    return true;
}
function saveCompletionStatuses() {


    $.ajax({
        type: "POST",
        url: "/setup",
        data: $('#completionForm').serialize(),
        success: function(data)
        {
            adjustPercentComplete(true);
        }
    });
}
function adjustPercentComplete(sendEmail) {
    var progress_bar = $('#percent_complete');
    var total_complete = $('.glyphicon-ok-circle').length;
    var percent_complete = total_complete * 20;
    progress_bar.find('h3').html(percent_complete + "%");
    progress_bar.find('.progress-bar.progress-bar-success').width(percent_complete + '%');
    progress_bar.find('.progress-bar.progress-bar-danger').width((100 - percent_complete) + '%');

    if (total_complete == 5) {
        $('#setupComplete').modal('show');
        $('#setupStatus').removeClass('alert-danger').addClass('alert-success').html(setupComplete);
        if (sendEmail) {
            $.ajax({
                type: "GET",
                url: "/setup/_send_setup_email",
                success: function(data) {
                    // console.log(data);
                }
            });
        }
        ;
    } else {
        $('#setupStatus').addClass('alert-danger').removeClass('alert-success').html(setupIncomplete);
    }
    ;
}