$(document).ready(function() {
    $('select').material_select();

    var picker2 = $('.datepicker'),
    	$input = $('.datepicker').pickadate(),
    	picker = $input.pickadate('picker'); 

   	picker2.pickadate({
	    selectMonths: true,
	    selectYears: 2,
	    min: true,
	    monthsFull: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
		monthsShort: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
		weekdaysFull: ['D', 'L', 'M', 'X', 'J', 'V', 'S'],
		showWeekdaysFull: true,
		firstDay: 1,
		labelMonthNext: 'Mes siguiente',
		labelMonthPrev: 'Mes anterior',
		labelMonthSelect: 'Selecciona un mes',
		labelYearSelect: 'Selecciona un año',
		format: 'dd/mm/yyyy',
	  });

    picker.set('disable', [
    	//6, 7,
	  // Using a collection of arrays formatted as [YEAR,MONTH,DATE]
	  //[2017,4,9], [2017,4,13], [2017,4,20],

	  // Using JavaScript Date objects
	  //new Date(2017,9,13), new Date(2017,9,24)
	  { from: [2017,4,1], to: [2017,4,20] }
	]);

	picker.set('enable', [
	  [2017,4,9],
	  new Date(2017,4,20)
	]);
});