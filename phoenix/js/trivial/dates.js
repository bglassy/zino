var Dates = {
	LeapYear : function( year ) {
		if ( year % 100 ) {
			if ( year % 400 ) {
				return true;
			}
		}
		else if ( year % 4 ) {
			return true;
		}
		return false;
	},
	DaysInMonth : function( month , year ) {
		switch ( month ) {
			case 1:
			case 3:
			case 5:
			case 7:
			case 8:
			case 10:
			case 12:
				return 31;
			case 2:
				if ( Dates.LeapYear( year ) ) {
					return 29;
				}
				return 28;
		}
		return 30;
	},
	ValidDate : function( day , month , year ) {
		alert( day + ' ' + month + ' ' + year );
		var daysinmonth = Dates.DaysInMonth( month , year );
		alert( 'days in month: ' + daysinmonth );
		if ( day < 0 || day > daysinmonth ) {
			return false;
		}
		if ( month < 0 || month >12 ) {
			return false;
		}
		return true;
	}
};