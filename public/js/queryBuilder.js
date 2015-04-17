var undoHistory = new Array();

var optionsSet1 = [
{label: 'inverse', title: 'inverse', value: 'inverse'},
{label: 'not', title: 'not', value: 'not'},
{label: '(', title: '(', value: '('},
	{label: '{', title: '{', value: '{'},
	{label: ')', title: ')', value: ')'},
{label: '}', title: '}', value: '}'}
];

var optionsSet2 = [
{label: 'or', title: 'or', value: 'or'},
{label: 'and', title: 'and', value: 'and'},
{label: '(', title: '(', value: '('},
	{label: '{', title: '{', value: '{'},
	{label: ')', title: ')', value: ')'},
{label: '}', title: '}', value: '}'}
];

var optionsSet3 = [
{label: 'some', title: 'some', value: 'some'},
{label: 'min', title: 'min', value: 'min'},
{label: 'max', title: 'max', value: 'max'},
{label: 'only', title: 'only', value: 'only'},
{label: 'Self', title: 'Self', value: 'Self'},
{label: 'exactly', title: 'exactly', value: 'exactly'},
{label: 'value', title: 'value', value: 'value'}
];

$(document).ready(function() {

	/*$('#classes').multiselect({
		checkboxName: 'multiselect[]',
		disableIfEmpty: true,
		maxHeight: 250,
		enableFiltering: true,
		enableCaseInsensitiveFiltering: true,
		buttonWidth: '200px',
		nonSelectedText: '(none selected)',
		allSelectedText: '(all selected)'
	});*/

	var classesOptions = {
		onChange: function(option) {
			updateQuery($('#classes option:selected').val());
			$('#operators').multiselect('dataprovider', optionsSet2);
			enableDisable(false, false, false, true);
		}
	};

	var individualsOptions = {
		onChange: function(option) {
			updateQuery($('#individuals option:selected').val());
			$('#operators').multiselect('dataprovider', optionsSet2);
			enableDisable(false, false, false, true);
		}
	};

	var relationshipsOptions = {
		onChange: function(option) {
			updateQuery($('#relationships option:selected').val());
			$('#operators').multiselect('dataprovider', optionsSet3)
			enableDisable(false, false, false, true);
		}
	};

	var operatorsOptions = {
		onChange: function(option) {

			var optionSelected = $('#operators option:selected').val();
			updateQuery(optionSelected);

			if(optionSelected == "value") {
				$('#operators').multiselect('dataprovider', optionsSet2);
				enableDisable(false, true, false, false);
			} else {
				enableDisable(true, false, true, true);
			}

			if(optionSelected == "and" || optionSelected == "or") {
				$('#operators').multiselect('dataprovider', optionsSet1);
			}

			if(optionSelected == "min" || optionSelected == "max" || optionSelected == "exactly") {
				var number = prompt("Please enter an integer", "");
            		// Verify if is integer
            		updateQuery(number);
            		enableDisable(true, false, false, true);
            		$('#operators').multiselect('dataprovider', optionsSet2);
            	}

            	if(optionSelected == "some" || optionSelected == "only") {
            		enableDisable(true, false, false, true);
            		$('#operators').multiselect('dataprovider', optionsSet2);
            	}

            	if(optionSelected == "Self") {
            		enableDisable(false, false, false, true);
            		$('#operators').multiselect('dataprovider', optionsSet2);
            	}
            }
        };

        $('#classes').multiselect('setOptions', classesOptions);
        $('#individuals').multiselect('setOptions', individualsOptions);
        $('#relationships').multiselect('setOptions', relationshipsOptions);
        $('#operators').multiselect('setOptions', operatorsOptions);
        $('#operators').multiselect('dataprovider', optionsSet1);

        enableDisable(true, false, true, true);

    });

function updateQuery(optionToAdd) {
	undoHistory.push(optionToAdd);
	document.getElementById("query-main").innerHTML 
	= document.getElementById("query-main").innerHTML.concat(undoHistory[undoHistory.length-1]).concat(" ");
}

function rebuildQuery() {
	undoHistory.pop();
	document.getElementById("query-main").innerHTML = "";

	var updatedQuery = "";

	for (var i = 0; i < undoHistory.length; i++) {
		updatedQuery = updatedQuery.concat(undoHistory[i]).concat(" ");
	}
	document.getElementById("query-main").innerHTML = updatedQuery;
	enableDisable(true, true, true, true);
}

function enableDisable(enableClasses, enableIndividuals, enableRelationships, enableOperators) {

	if (enableClasses) {
		$('#classes').multiselect('enable');
	}
	else {
		$('#classes').multiselect('disable');
	}
	if (enableIndividuals) {
		$('#individuals').multiselect('enable');
	}
	else {
		$('#individuals').multiselect('disable');
	}
	if (enableRelationships) {
		$('#relationships').multiselect('enable');
	}
	else {
		$('#relationships').multiselect('disable');
	}
	if (enableOperators) {
		$('#operators').multiselect('enable');
	}
	else {
		$('#operators').multiselect('disable');
	}

	$('#classes').multiselect('deselectAll', false);
	$('#individuals').multiselect('deselectAll', false);
	$('#relationships').multiselect('deselectAll', false);
	// NOT WORKING, WHY? :(
	$('#operators').multiselect('deselectAll', false);
}