(function($){
	var initLayout = function() {
	    $('#txt_StartDate').DatePicker({
		    format:'Y-m-d',
		    calendars: 2,
		    date: $('#txt_StartDate').val(),
		    current: $('#txt_StartDate').val(),
		    starts: 1,
		    //position: 'top',
		    onChange: function(formated, dates){
			    $('#txt_StartDate').val(formated);
			    $('#txt_StartDate').DatePickerHide();
		    }
		    ,onRender: function(date) {
                return {
                    disabled: (date.valueOf()<new Date().valueOf())
                }
            }
	    });
	};
	//EYE.register(initLayout, 'init');
})(jQuery);