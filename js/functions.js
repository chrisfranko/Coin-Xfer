$(document).ready(function() {

	$("#formSubmit").click(function() {
		
		var emailAddress = $("input[name='email']").val();
		var firstName = $("input[name='firstName']").val();	
		var expectedBalance_var = $("input[name='expectedBalance']").val();
		var paymentAddress_var = $("input[name='paymentAddress']").val();
			
		$.post("client.php",
	  {
	    createWallet:'true', // this is the only required field...
	    email:emailAddress, // the following are here if we want to log this into a database somewhere
	    name:firstName,
	    expectedBalance:expectedBalance_var,
			paymentAddress:paymentAddress_var
	  },
	  function(data,status){ // Create Payment Address
	  	
	    var obj = $.parseJSON(data);
	    console.log(data);
	    
	    $('.payment_addr').text(obj['address']);
	    $('.payment_container').removeClass('hidden');
							
			var address_var = obj['address']; // payment address from the first POST to client.php
			var expectedBalance_var = $("input[name='expectedBalance']").val();
			var paymentAddress_var = $("input[name='paymentAddress']").val();
			
			var timer = setInterval(function() { // Begin Payment Loop
								
				$('.payment_status').text('Refreshing...');
				box1 = new ajaxLoader('.payment', {classOveride: 'blue-loader'});
			
				$.post("client.php",
				  {
				    checkTransaction:'true',
				    address:address_var,
				    expectedBalance:expectedBalance_var,
				    paymentAddress:paymentAddress_var
				  },
				  function(data,status){ // Check Payment Status
				    var obj = $.parseJSON(data);
				    console.log(data);    
				    
				    if (obj['confirmed'] == 1) { // Payment Amounts Equal - Transfer Completed
				    	clearInterval(timer); 
				    	box1.remove(); 
				    	$('.payment_status').text('Current Status: Paid!'); 				    	
				    } else {
				    	setTimeout(function(){
							box1.remove();
							jQuery('.payment_status').text('Current Status: Pending');    
							}, 4000);	
						}
				});							
			
			}, 10000); // Repeat loop every 10 seconds
			        
		});
		
	});
	
});