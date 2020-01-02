'use strict';
var stripe = Stripe(scriptParams.stripePk);
var elements = stripe.elements();

jQuery(document).ready(function(){
    // Custom styling can be passed to options when creating an Element.
    var style = {
        base: {
            // Add your base input styles here. For example:
            fontSize: '16px',
            color: "#32325d",
        }
    };

// Create an instance of the card Element.
    var card = elements.create('card', {style: style});

// Add an instance of the card Element into the `card-element` <div>.
    card.mount('#card-element');

    card.addEventListener('change', function(event) {
        var displayError = document.getElementById('card-errors');
        if (event.error) {
            displayError.textContent = event.error.message;
        } else {
            displayError.textContent = '';
        }
    }); 


    // Create a token or display an error when the form is submitted.
    var form = document.getElementById('registration-form');
    form.addEventListener('submit', function(event) {
        console.log("getting token...");
        event.preventDefault();

        stripe.createToken(card).then(function(result) {
            if (result.error) {
                // Inform the customer that there was an error.
                var errorElement = document.getElementById('card-errors');
                errorElement.textContent = result.error.message;
            } else {
                // Send the token to your server.
                console.log("str token:");
                console.log(result.token);
                console.log(result.token.id);
                registerUser(result.token);
            }
        });
    });

    function registerUser(token) {
        jQuery("#token").val(token);
        var form = document.getElementById('registration-form');
        jQuery("#pmtToken").val(token.id);
        form.submit();
        return;
	};
});