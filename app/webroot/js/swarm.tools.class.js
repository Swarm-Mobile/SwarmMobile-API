if(typeof(currency) === 'undefined'){
    var currency = '$';    
}

var tools = {
    defaults: function(type) {
        if (type === 'start_date' || type === 'end_date') {
            d = new Date();
            d = d.toISOString();
            i = d.indexOf('T');
            return d.substring(0, i);
        }
        if (type === 'previous_start_date' || type === 'previous_end_date') {
            d = new Date();
            d.setDate(d.getDate() - 7);
            d = d.toISOString();
            i = d.indexOf('T');
            return d.substring(0, i);
        }
    },
    coalesce: function(nvalue, cvalue) {
        if (typeof (nvalue) !== 'undefined' && nvalue !== '') {
            return nvalue;
        }
        return cvalue;
    },
    hex: function(value) {
        var colors = [
            {name: 'blue', hex: '#3498db'},
            {name: 'green', hex: '#27ae60'},
            {name: 'orange', hex: '#fd7037'},
            {name: 'yellow', hex: '#f39c12'},
            {name: 'violet', hex: '#8e44ad'},
            {name: 'green2', hex: '#16a085'},
            {name: 'green3', hex: '#1abc9c'},
            {name: 'grey', hex: '#4e5c60'}
        ];
        var result = '#3498db';
        colors.forEach(function(v) {
            if (value === v.name) {
                result = v.hex;
                return;
            }
            if (value === v.hex) {
                result = v.name;
                return;
            }
        });
        return result;
    },
    color: function(value) {
        var colors = [
            {id: 1, name: 'blue'},
            {id: 2, name: 'green'},
            {id: 3, name: 'orange'},
            {id: 4, name: 'yellow'},
            {id: 5, name: 'violet'},
            {id: 6, name: 'green2'},
            {id: 7, name: 'green3'},
            {id: 8, name: 'grey'}
        ];
        var result = 1;
        colors.forEach(function(v) {
            if (value === '' + v.id || value === v.id || value === v.name) {
                result = v.id;
                return;
            }
        });
        return result;
    },
    endpoint: function(endpoint) {
        var info = [
            {name: 'walkbys', color: '#3498db', type: 'num', line_type: 'areaspline', icon: 'footstepsIcon'},
            {name: 'footTraffic', color: '#27ae60', type: 'num', line_type: 'areaspline', icon: 'guestsIcon'},
            {name: 'returning', color: '#27ae60', type: 'num', line_type: 'areaspline', icon: 'guestsIcon'},
            {name: 'dwell', color: '#27ae60', type: 'time', line_type: 'areaspline', icon: 'guestsIcon'},
            {name: 'transactions', color: '#fd7037', type: 'num', line_type: 'areaspline', icon: 'tagIcon'},
            {name: 'revenue', color: '#f39c12', type: 'currency', line_type: 'spline', icon: 'revenueIcon'},
            {name: 'avgTicket', color: '#f39c12', type: 'currency', line_type: 'areaspline', icon: 'revenueIcon'},
            {name: 'itemsPerTransaction', color: '#f39c12', type: 'num', line_type: 'areaspline', icon: 'revenueIcon'},
            {name: 'windowConversion', color: '#3498db', type: 'rate', line_type: 'areaspline', icon: 'footstepsIcon'},
            {name: 'conversionRate', color: '#fd7037', type: 'rate', line_type: 'areaspline', icon: 'tagIcon'}
        ];
        result = {'name': 'default', color: '#3498db', type: 'areaspline', icon: ''};
        info.forEach(function(v) {
            if (endpoint === v.name) {
                result = v;
                return;
            }
        });
        return result;
    },
    endpointColor: function(endpoint) {
        return tools.endpoint(endpoint).color;
    },
    endpointType: function(endpoint) {
        return tools.endpoint(endpoint).type;
    },
    endpointLineType: function(endpoint) {
        return tools.endpoint(endpoint).line_type;
    },
    endpointIcon: function(endpoint) {
        return tools.endpoint(endpoint).icon;
    },
    parseDate: function(date) {
        var tmp = date.split('-');
        var months = ['', 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        return months[parseInt(tmp[1])] + ' ' + tmp[2] + ',' + tmp[0];
    },
    ucwords: function(str) {
        return (str + '')
                .replace(/^([a-z\u00E0-\u00FC])|\s+([a-z\u00E0-\u00FC])/g, function($1) {
                    return $1.toUpperCase();
                });
    },
    makeHMS: function(seconds_string) {
        if (seconds_string) {
            d = Number(seconds_string);
            var h = Math.floor(d / 3600);
            var m = Math.floor(d % 3600 / 60);
            var s = Math.floor(d % 3600 % 60);
            m = (m > 0) ? (((m < 10) ? ('0' + m) : m) + ":") : ((h > 0) ? '00:' : "");
            h = (h > 0) ? (((h < 10) ? ('0' + h) : h) + ":") : "";
            s = (s < 10) ? ('0' + s) : s;
            return h + m + s;
        } 
        return 0;
    },
    addCommas: function(nStr) {
        if(nStr >= 1000){
            nStr = nStr.toFixed(0);
        }
        nStr += '';
        x = nStr.split('.');
        x1 = x[0];
        x2 = x.length > 1 ? '.' + x[1] : '';
        var rgx = /(\d+)(\d{3})/;
        while (rgx.test(x1)) {
            x1 = x1.replace(rgx, '$1' + ',' + '$2');
        }
        return x1 + x2;
    }
}