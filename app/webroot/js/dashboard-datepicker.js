//global config for date range picker
//set global date variables so we can use them anywhere
var global_date_start;
var global_date_end;
var global_date_start2;
var global_date_end2;

//restrict demo dates
if(demo=="yes"){
  var date_ranges = {
    // 'Today': [Date.parse('June 27th, 2013').toString('yyyy-MM-dd'), Date.parse('June 27th, 2013').toString('yyyy-MM-dd')],
    'Yesterday': [Date.parse('June 26th, 2013').toString('yyyy-MM-dd'), Date.parse('June 26th, 2013').toString('yyyy-MM-dd')],
    'This Week': [Date.parse('June 21st, 2013').toString('yyyy-MM-dd'), Date.parse('June 27th, 2013').toString('yyyy-MM-dd')],
    'This Month': [Date.parse('June 1st, 2013').toString('yyyy-MM-dd'), Date.parse('June 27th, 2013').toString('yyyy-MM-dd')]
  }
} else {
  var date_ranges = {
    'Today': [moment(), moment()],
    'Yesterday': [moment().subtract('days', 1), moment().subtract('days', 1)],
    'Last 7 Days': [moment().subtract('days', 6), moment()],
    'Last 30 Days': [moment().subtract('days', 29), moment()],
    'This Month': [moment().startOf('month'), moment().endOf('month')],
    'Last Month': [moment().subtract('month', 1).startOf('month'), moment().subtract('month', 1).endOf('month')]
  }
};



//set initial date vars, global namespace, range 2 is empty by default
start1CookieVal = $.cookie('start_range_1');
end1CookieVal = $.cookie('end_range_1');
  
if(typeof(start1CookieVal) != 'undefined' && typeof(end1CookieVal) != 'undefined' && start1CookieVal != null && end1CookieVal != null && demo=="no"){


  start_range_1 = Date.parse(start1CookieVal).toString('yyyy-MM-dd');
  end_range_1 = Date.parse(end1CookieVal).toString('yyyy-MM-dd');

  var range_of_days = days_between(Date.parse(start1CookieVal),Date.parse(end1CookieVal))+1;
  if(range_of_days==1){
    start_range_2 = Date.parse(start1CookieVal).add({ days: -7 }).toString('yyyy-MM-dd');
    end_range_2 = Date.parse(start1CookieVal).add({ days: -7 }).toString('yyyy-MM-dd');
  } else {
    start_range_2 = Date.parse(start1CookieVal).add({ days: -range_of_days }).toString('yyyy-MM-dd');
    end_range_2 = Date.parse(start1CookieVal).add({ days: -1 }).toString('yyyy-MM-dd');
  }



} else {


  start_range_1 = Date.today().add({ days: -7 }).toString('yyyy-MM-dd');
  end_range_1 = Date.today().add({ days: -1 }).toString('yyyy-MM-dd');
  var range_of_days = days_between(start_range_1, end_range_1)+1;
  start_range_2 = Date.parse(start_range_1).add({ days: -range_of_days }).toString('yyyy-MM-dd');
  end_range_2 = Date.parse(start_range_1).add({ days: -1 }).toString('yyyy-MM-dd');



  if(demo=="yes"){
    start_range_1 = Date.parse('June 26th, 2013').toString('yyyy-MM-dd');
    end_range_1 = Date.parse('June 26th, 2013').toString('yyyy-MM-dd');
    start_range_2 = Date.parse('June 19th, 2013').toString('yyyy-MM-dd');
    end_range_2 = Date.parse('June 19th, 2013').toString('yyyy-MM-dd');
  }
  $.cookie('start_range_1', start_range_1, {path: '/'});
  $.cookie('end_range_1', end_range_1, {path: '/'});
  
}

s1 = Date.parse(start_range_1);
e1 = Date.parse(end_range_1);
s2 = Date.parse(start_range_2);
e2 = Date.parse(end_range_2);
setReportTitle(s1, e1, s2, e2);

//rangepicker defaults
// console.log(start1CookieVal);
// console.log(end1CookieVal);
var rangepicker_options = {
  startDate: Date.parse(start_range_1).toString('MM/dd/yyyy'),
  endDate: Date.parse(end_range_1).toString('MM/dd/yyyy'),
  minDate: '01/01/2013',
  maxDate: moment(),
  dateLimit: { days: 60 },
  showDropdowns: true,
  showWeekNumbers: true,
  timePicker: false,
  timePickerIncrement: 1,
  timePicker12Hour: true,
  ranges: date_ranges,
  opens: 'left',
  buttonClasses: ['btn btn-default'],
  applyClass: 'btn-small btn-primary',
  cancelClass: 'btn-small',
  format: 'MM/DD/YYYY',
  separator: ' to ',
  locale: {
    applyLabel: 'Submit',
    cancelLabel: 'Clear',
    fromLabel: 'From',
    toLabel: 'To',
    customRangeLabel: 'Custom',
    daysOfWeek: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr','Sa'],
    monthNames: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
    firstDay: 1
  }
}

function setReportTitle(start, end, start_2, end_2){

  if(start.getTime()==end.getTime()){
    $('.varATitle').html(start.toString('dddd MMMM dS, yyyy'));
    $('#range_a .hidden-xs, #range_a .visible-xs').html(start.toString('MMMM dS, yyyy'));
  } else {
    $('#range_a .hidden-xs, .varATitle').html(start.toString('MMMM dS, yyyy') + ' - ' + end.toString('MMMM dS, yyyy'));
    $('#range_a .visible-xs').html(start.toString('MMM d, yyyy') + ' - ' + end.toString('MMM d, yyyy'));

  }
  if(start_2&&end_2){
    if(start_2.getTime()==end_2.getTime()){
      $('.varBTitle .hidden-xs, .varBTitle .visible-xs').html(start_2.toString('MMMM dS, yyyy'));
    } else {
      $('.varBTitle .hidden-xs').html(start_2.toString('MMMM dS, yyyy')+' - '+end_2.toString('MMMM dS, yyyy'));
      $('.varBTitle .visible-xs').html(start_2.toString('MMM d, yyyy')+' - '+end_2.toString('MMM d, yyyy'));
    }
  }
  global_date_start = start.toString('MMM d, yyyy');
  global_date_end = end.toString('MMM d, yyyy');
  if(start_2&&end_2){
    global_date_start2 = start_2.toString('MMM d, yyyy');
    global_date_end2 = end_2.toString('MMM d, yyyy');

  }
}

function weeks_between(date1, date2) {
  // The number of milliseconds in one week
  var ONE_WEEK = 1000 * 60 * 60 * 24 * 7;
  // Convert both dates to milliseconds
  var date1_ms = date1.getTime();
  var date2_ms = date2.getTime();
  // Calculate the difference in milliseconds
  var difference_ms = Math.abs(date1_ms - date2_ms);
  // Convert back to weeks and return hole weeks
  return Math.floor(difference_ms / ONE_WEEK);
}