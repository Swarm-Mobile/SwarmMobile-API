/**
 * Created by yaronguez on 3/14/14.
 * Submits content area of website via AJAX to pdf export script
 */
$(function() {


    // Get parameters for pdf export
    function get_pdf_params(){
        // Clone the HTML of the page
        var html = $('html').clone();

        // hide mouse tracking paths
        html.find('path.highcharts-tracker').hide();

        var host = window.location.protocol + '//' + window.location.host;

        // Change all relative urls to absolute
        html.find('a, link').not('[href^="http"],[href^="https"],[href^="mailto:"],[href^="#"]').each(function(){
            $(this).attr('href', function(index, value) {
                if (value.substr(0,1) !== "/") {
                    value = window.location.pathname + value;
                }

                return host + value;
            })
        })

        // Remove scripts
        html.find('script').remove();

        var result = {
            html: encodeURIComponent(html.prop('outerHTML')),
            date_start: $('input[name="daterangepicker_start"]').val(),
            date_end: $('input[name="daterangepicker_end"]').val(),
            page_name: $('#page-name').text(),
            member_id: member_id,
            obfuscate: 'dfkajhfj3435kJ'
        }

        return result;
    }

    // when user presses export to pdf button, issue a download JavaScript call with the pdf parameters
    $('#pdfButton').on('click',function(e){
        var pdf_params = get_pdf_params();
        $.download(
            '/pdf_export/download',
            'html=' + pdf_params.html
                + '&member_id=' + pdf_params.member_id
                + '&obfuscate=' + pdf_params.obfuscate
                + '&date_start=' + pdf_params.date_start
                + '&date_end=' + pdf_params.date_end
                + '&page_name=' + pdf_params.page_name,
            'post'
        );

        e.preventDefault();
    });

    $('.emailPdfButton').on('click', function(e) {
        $('#emailModal').modal('show');
        e.preventDefault();
    });

    $('#exportEmailForm').on('submit', function(e){
        $('#emailModal .alert-danger').remove();
        $('#exportEmailForm #toEmail').attr('disabled','disabled');
        $('#exportEmailForm #emailMessage').attr('disabled','disabled');
        $('#emailModal button').attr('disabled','disabled');
        $('#exportEmailForm #sendEmailPdf').html('<img src="/b2b/images/ajax-loader2.gif"/>');
        $('#emailModal').data('bs.modal').options.keyboard = false;
        $('#emailModal').data('bs.modal').options.backdrop = 'static';

        var pdf_params = get_pdf_params();
        pdf_params.email_to = $('#exportEmailForm #toEmail').val();
        pdf_params.email_message = $('#exportEmailForm #emailMessage').val();
        e.preventDefault();

        $.post(
            '/pdf_export/email',
            'html=' + pdf_params.html
                + '&member_id=' + pdf_params.member_id
                + '&obfuscate=' + pdf_params.obfuscate
                + '&date_start=' + pdf_params.date_start
                + '&date_end=' + pdf_params.date_end
                + '&page_name=' + pdf_params.page_name
                + '&email_to=' + pdf_params.email_to
                + '&email_message=' + pdf_params.email_message
            ,
            'json'
        ).always(function(){
                $('#exportEmailForm #toEmail').removeAttr('disabled');
                $('#exportEmailForm #emailMessage').removeAttr('disabled');
                $('#emailModal button').removeAttr('disabled');
                $('#exportEmailForm #sendEmailPdf').html('Send');
                $('#emailModal').data('bs.modal').options.keyboard = true;
                $('#emailModal').data('bs.modal').options.backdrop = true;
            })
        .fail(function(xhr, textStatus, errorThrown){
                $('#exportEmailForm').prepend('<div class="alert alert-danger">Error: '+ xhr.statusText + '</div>');
            })
        .done(function(data){
            if(typeof data !== 'object'){
                data = $.parseJSON(data);
            }
                
            if(!data.success){
                $('#exportEmailForm').prepend('<div class="alert alert-danger">'+ data.message + '</div>');
            } else {
                $('#emailModal').modal('hide');
                $('#exportEmailForm #toEmail').val('');
                $('#content').prepend('<div class="container-fluid hidden-print"><div class="alert alert-success alert-dismissable alert-top"> <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>' + data.message + '</div></div>')

            }
        });

    });


})