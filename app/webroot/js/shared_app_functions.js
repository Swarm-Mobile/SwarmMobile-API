
//Adds a visit to the db in user visits NOT sessions, only if more than 24 hours have passed OR it is the user's first time
function AddVisit(){
  if(!deviceEmail){
    deviceEmail = 'Guest';
  }
  //get current date as yyy-mm-dd string and last visit as yyyy-mm-dd string, if it's a different day, then add a visit
  if(last_visit){
    now = new Date(); 
    t = last_visit.split(/[- :]/);
    d = new Date(t[0], t[1]-1, t[2]);
    d_b_s = parseInt(d.getFullYear().toString()+d.getMonth().toString()+d.getDate().toString());
    now_s = parseInt(now.getFullYear().toString()+now.getMonth().toString()+now.getDate().toString());
    // alert(d_b_s+' and '+now_s);
    var IsNewVisit = now_s>d_b_s;
    // console.log(IsNewVisit + ' '+now_s+' '+d_b_s);
  } else {
    var IsNewVisit = true;
  };
  // alert('trying to add visit for you: '+IsNewVisit+' - '+deviceVisits);
  if(IsNewVisit || deviceVisits==0){
    // alert('Adding new visit');
  deviceVisits++;
   $.ajax({
        type: "GET",
        dataType: "json",
        cache: false,
        url: "/analytic/connect.php",
        data: { 
            'token':token,
            'email': deviceEmail, 
            'member_id': memberId,
            'mac_address': globalMac
            // 'ip_address': ip_add
        },
        success: function (data) {
          // alert('Added visit');
        },
        error: function(xhr,response,error){
            // alert('There was an error adding a visit '+xhr.responseText+' '+error);
            // console.log(error);
            // console.log(xhr.responseText);
        }
      });
  } else {
    // alert(now_s+' is not bigger than '+d_b_s+' so not adding a visit');
  };
}

function isValidEmail(emailAddress) {
    var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[(2([0-4]\d|5[0-5])|1?\d{1,2})(\.(2([0-4]\d|5[0-5])|1?\d{1,2})){3} \])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
    return re.test(emailAddress);
}