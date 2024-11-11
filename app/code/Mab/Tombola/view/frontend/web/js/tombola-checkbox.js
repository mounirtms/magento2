define([
    'jquery',
    'ko',
    'uiComponent',
    'Magento_Checkout/js/model/quote',
    'Magento_Customer/js/model/customer' // To get the customer data
], function ($, ko, Component, quote, customer) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Mab_Tombola/tombola-checkbox' // Corrected template path
        },

        initialize: function () {
            this._super();
            this.showCheckbox = ko.observable(false);
            this.isReadonly = ko.observable(true);
            this.joinTombola = ko.observable(false);

            quote.totals.subscribe(function () {
                var totals = quote.getTotals()();
                if (totals && totals.grand_total > 3000) {
                    this.showCheckbox(true);  // Show checkbox if grand total > 3000
                    this.isReadonly(false);   // Enable checkbox if grand total > 3000
                } else {
                    this.showCheckbox(true);  // Always show the checkbox
                    this.isReadonly(true);    // Make checkbox read-only if grand total <= 3000
                }
            }, this);
        },

        toggleTombola: function () {
            this.joinTombola(!this.joinTombola()); // Toggle the value

            // Retrieve customer first and last name from the quote
            var firstName = quote.billingAddress() ? quote.billingAddress().firstname : '';
            var lastName = quote.billingAddress() ? quote.billingAddress().lastname : '';
            var clientName = firstName + ' ' + lastName;

            // Get quote ID and customer ID if logged in
            var quoteId = quote.quoteId();
            var customerId = customer.isLoggedIn() ? customer.getCustomerId() : null;

            // Send the tombola data along with the client name and IDs to the backend
            this.sendTombolaData(clientName, this.joinTombola(), quoteId, customerId);
        },

        // Function to send the Tombola data (checkbox status, client name, quote_id, customer_id) to the backend
        sendTombolaData: function (clientName, joinTombola, quoteId, customerId) {
            // Example of AJAX request to send data to your custom controller/action
            $.ajax({
                url: '/tombola/ajax/tombolapost', // Replace with your actual URL
                method: 'POST',
                data: {
                    client_name: clientName,
                    join_tombola: joinTombola,
                    quote_id: quoteId,
                    customer_id: customerId
                },
                success: function(response) {
                    console.log(response);  // Handle the response from the server
                },
                error: function(xhr, status, error) {
                    console.log(status, error); // Handle any errors
                }
            });
        }
    });
});
