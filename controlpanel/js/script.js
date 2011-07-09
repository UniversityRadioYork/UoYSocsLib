$(function(){
  $( "#tabs" ).tabs();
	$( "#termdates tbody a"	).button();
  $( "tbody tr:even" ).css("background-color","#efefef");
  $( "tbody tr:odd" ).css("background-color","#e5e5e5");

	function conflictionFree(jqobj) {
		//check year doesn't conflict
		var row = $(jqobj).parents('tr').first();
		var year = $(row).find('td').first();
		if (year.has('input').length != 0) {
			year = year.find('index').val();
		} else {
			year = year.html();
		}
		$('tr td:first-child:not(:last)').not($(row).find('td:first')).each(function (index) {
			if ($(this).html() == year){
				return false;
			}
		});
		//check dates are in order and in bounds
		var toCmp = new Date(year+"-08-31");
		$(jqobj).parents('tr').find('input').each(function (index) {
			var tmp = new Date($(this).val()); 
			if (toCmp < tmp) {
				toCmp = tmp;
			} else {
				return false;
			}
		});
	  var tmp = new Date((year+1)+"-09-01"); 
		if (!(toCmp < tmp)) {
      return false;
    }
		return true;
  };

	$( "#termdates .update").click(function () {
		if (!conflictionFree(this)) {
			alert("Invalid: Data conflicting with itself.");
			return false;
		} 
		//TODO add submit code to UoY_Cache somehow
		return false;
	});

	$( "#termdates .add").click(function () {
		if (!conflictionFree(this)) {
			alert("Invalid: Data conflicting with itself.");
			return false;
		} 
		//TODO add submit code to UoY_Cache somehow
		return false;
	});

	$( "#termdates .del").click(function () {
		//TODO add submit code to UoY_Cache somehow
		return false;
	});
});
