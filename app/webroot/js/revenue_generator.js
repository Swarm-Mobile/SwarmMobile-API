$(function() {

  $('.interval_title').text($('#revenue_interval').val());
  num_days = $('#revenue_interval option:selected').attr('days');
  
  $('#revenue_interval').on('change', function(){
    $('.interval_title').text($('#revenue_interval').val());
     num_days = $('#revenue_interval option:selected').attr('days');
     $('.metric_input').trigger('keyup');
  });
  
  $('.metric_input').on('keypress', function(ev) {
      var keyCode = window.event ? ev.keyCode : ev.which;
      //codes for 0-9
      if (keyCode < 48 || keyCode > 57) {
          //codes for backspace, delete, enter
          if (keyCode != 0 && keyCode != 8 && keyCode != 13 && !ev.ctrlKey) {
              ev.preventDefault();
          }
      }
  });
  $('.metric_input').keyup(function(){    
    resetSlider();
    calculateMetrics();
    resetIncreaseMetric();
  });
  $('#interval_sales_input').on('keyup', function(ev){
    var theSalesVal = $(this).val();
    if(isNumber(theSalesVal)){
      theSalesVal = parseFloat(theSalesVal);
      $('.interval_sales').html('$'+numberWithCommas(theSalesVal));
    }
  });


  $('#metric_select').change(function(){
    $('.metric_append').html();
    $('.metric_prepend').html();
    valToLookAt = $(this).val();
    resetSlider();

      if(calculateMetrics()){
        if(valToLookAt=="0"){
          $('#metricWrapper').hide();
        } else {
          $('#metricWrapper').show();
        };
        if(!isAdmin){
            mixpanel.track("Revenue Generator Used");
        }
        resetIncreaseMetric();
        
      };

  });

  $( "#slider" ).slider({
    slide: function( event, ui ) {
      metricSliderActions(ui.value);
    }
  });

});
function calculateMetrics(){

  square_feet = parseInt($('input[name="square_feet"]').val());
  foot_traffic = parseInt($('input[name="foot_traffic"]').val());
  interval_sales = parseInt($('input[name="interval_sales"]').val());
  daily_transactions = parseInt($('input[name="daily_transactions"]').val());
  conversion_rate = parseFloat((daily_transactions/foot_traffic).toFixed(2));
  avg_daily_transaction_value = parseFloat(((interval_sales/num_days)/daily_transactions).toFixed(2));
  dollars_per_square_foot = parseFloat(interval_sales/square_feet.toFixed(2));

  if(isNumber(square_feet) && isNumber(foot_traffic) && isNumber(interval_sales) && isNumber(daily_transactions)){
      $('#moreMetricAlert').fadeOut();
    return true;
  } else if (valToLookAt!="0") {
      $('#moreMetricAlert').fadeIn();
      $('#metricWrapper').hide();
      $('#metric_select').val("0");
    return false;

    };

  $('.interval_sales').html('$'+interval_sales);
  
}
function metricSliderActions(value){
  if(!value){
    value=0;
  }
  if(valToLookAt=="Foot Traffic"){
    value_to_increase = value+metric_to_increase;

    if(value!=0){
      yearly_increase = parseInt(value_to_increase*num_days);
      transactions_increase = parseInt((yearly_increase*conversion_rate).toFixed(2));
      sales_increase = parseInt((transactions_increase*avg_daily_transaction_value).toFixed(2));
      new_sales_total = sales_increase;
      sales_percent_increase = (((sales_increase-interval_sales)/interval_sales)*100).toFixed();
      
    } else {
      new_sales_total = interval_sales;
      sales_percent_increase = 0;
    };

  }else if (valToLookAt=="Conversion Rate"){

    value_to_increase = (value/10)+parseInt((metric_to_increase*100).toFixed());
    if(value_to_increase>100){
      value_to_increase=100;
    }
    if(value!=0){
      transactions_increase = parseInt(((value_to_increase/100)*foot_traffic)*num_days);
      sales_increase = parseInt((transactions_increase*avg_daily_transaction_value).toFixed(2));
      new_sales_total = sales_increase;
      sales_percent_increase = (((sales_increase-interval_sales)/interval_sales)*100).toFixed();
    } else {
      new_sales_total = interval_sales;
      sales_percent_increase = 0;
    };

  } else if (valToLookAt=="Average Transaction Value"){

    value_to_increase = (value/10)+metric_to_increase;
    
    if(value!=0){

      sales_increase = parseInt((value_to_increase*daily_transactions)*num_days);
      new_sales_total = sales_increase;
      sales_percent_increase = (((sales_increase-interval_sales)/interval_sales)*100).toFixed();

    } else {
      new_sales_total = interval_sales;
      sales_percent_increase = 0;
    };

  } else if (valToLookAt=="Transactions Per Day"){

    value_to_increase = (value/10)+metric_to_increase;
    
    if(value!=0){

      sales_increase = parseInt((value_to_increase*avg_daily_transaction_value)*num_days);
      new_sales_total = sales_increase;
      sales_percent_increase = (((sales_increase-interval_sales)/interval_sales)*100).toFixed();

    } else {
      new_sales_total = interval_sales;
      sales_percent_increase = 0;
    };

  } else if (valToLookAt=="Dollars/Sq Ft/m Performance"){

    value_to_increase = value+metric_to_increase;
    
    if(value!=0){

      sales_increase = parseInt(value_to_increase*square_feet);
      new_sales_total = sales_increase;
      sales_percent_increase = (((sales_increase-interval_sales)/interval_sales)*100).toFixed();
    } else {
      new_sales_total = interval_sales;
      sales_percent_increase = 0;
    };

  }



  $('.increase_metric').html(value_to_increase.toFixed(2));
  $('.interval_sales').html('$'+numberWithCommas(new_sales_total));
  $('.sales_percent_increase').html(sales_percent_increase+'% increase');

}
function resetIncreaseMetric(){
  var measurement = $('#measurement').val(); 
  if(valToLookAt=="Foot Traffic"){
    $('.increase_metric').html(foot_traffic);
    metric_to_increase=foot_traffic;
    $('.metric_append').html('');
    $('.metric_prepend').html('');
  }else if (valToLookAt=="Conversion Rate"){
    $('.increase_metric').html((conversion_rate*100).toFixed());
    metric_to_increase=conversion_rate;
    $('.metric_append').html('%');
    $('.metric_prepend').html('');
  }else if (valToLookAt=="Average Transaction Value"){
    metric_to_increase=avg_daily_transaction_value;
    $('.increase_metric').html(metric_to_increase);
    $('.metric_append').html('');
    $('.metric_prepend').html('$');

  } else if (valToLookAt=="Transactions Per Day"){
    metric_to_increase=daily_transactions;
    $('.increase_metric').html(metric_to_increase);
    $('.metric_append').html('');
    $('.metric_prepend').html('');
  } else if (valToLookAt=="Dollars/Sq Ft/m Performance"){
    metric_to_increase=dollars_per_square_foot;
    $('.increase_metric').html(metric_to_increase);
    $('.metric_prepend').html('');
    $('.metric_append').html('$/'+measurement+'&sup2;');
  }


}
function resetSlider(){
  $( "#slider" ).slider( "value", 0 );
  $('#slider').trigger('slidechange');
  $('.sales_percent_increase').html('0% increase');
  metricSliderActions(0);
}
function isNumber(n) {
  return !isNaN(parseFloat(n)) && isFinite(n);
}
function numberWithCommas(x) {
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}