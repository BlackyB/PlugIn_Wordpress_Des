(function() {
	var rollButton = document.getElementById('roll-button');
	if (rollButton !== null)
		rollButton.addEventListener('click', rollDice);

	function rollDice() {
		clearRolls('dice-roll', 'all-rolls');
		var totalDice = document.getElementsByClassName('number-of-dice');
		var rolls = {
			"result" : [],
			"throwDice" : []
		};
		for (var i = 0; i < totalDice.length; i++) {
			if (document.getElementById(totalDice[i].id).value > 0){
				var numberOfDice = (totalDice[i].value);
				var dieType = parseInt((totalDice[i].id).replace('dice-',""));
				rolls["throwDice"].push(numberOfDice + "D" + dieType);
				rolls["result"].push(getRolls(numberOfDice, dieType));
			}
		}
		modifier = parseInt(document.getElementById('modifier').value);

		sum = 0
		for (var i = 0; i < rolls['result'].length; i++) {
			sum += rolls["result"][i].reduce(addValues);
		}
		
		if (document.getElementById('display-all-cb').checked){
			var displayString = '';

			for (var i = 0; i < rolls['throwDice'].length; i++) {
				displayString += (rolls['throwDice'][i] + "( " + rolls['result'][i].join(", ") + " ) <br/>");
			}
			if(modifier > 0) {
				var show = (displayString + " + " + modifier + " = ");
			} else if( modifier < 0){
				var show = (displayString + modifier + " = ");
			} else {
				var show = (displayString + " = ");
			}
			displayTotal('all-rolls', show);
			document.getElementById('rollFullResult').value = show.toString();

		}
		document.getElementById('rollResult').value = sum;
		document.getElementById('formDB').submit();
		displayTotal('dice-roll', sum + modifier);
	}

	function getRolls(numberOfDice, dieType) {
		var rolls = [];
		for (var i = 0; i < numberOfDice; i++) {
			rolls.push(randomNumber(1, dieType));
		}
		return rolls;
	}

	function randomNumber(min, max) {
		return Math.floor(Math.random() * (max - min + 1)) + min;
	}

	function addValues(a, b) {
		return a + b;
	}

	function displayTotal(containerId, value) {
		document.getElementById(containerId).innerHTML = value;
	}

	function clearRolls(rollContainerId, allRollsContainerId) {
		document.getElementById(rollContainerId).innerHTML = '';
		document.getElementById(allRollsContainerId).innerHTML = '';
	}
})();


function openTab(evt, tab) {
  // Declare all variables
  var i, tabcontent, tablinks;

  // Get all elements with class="tabcontent" and hide them
  tabcontent = document.getElementsByClassName("tabcontent");
  for (i = 0; i < tabcontent.length; i++) {
    tabcontent[i].style.display = "none";
  }

  // Get all elements with class="tablinks" and remove the class "active"
  tablinks = document.getElementsByClassName("tablinks");
  for (i = 0; i < tablinks.length; i++) {
    tablinks[i].className = tablinks[i].className.replace(" active", "");
  }

  // Show the current tab, and add an "active" class to the button that opened the tab
  document.getElementById(tab).style.display = "flex";
  evt.currentTarget.className += " active";
}
