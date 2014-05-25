<?php
require_once 'jsonRPCClient.php';
$coin = new jsonRPCClient('http://user:Dk2NJxNKR4YFWrsvSmtrCjLWZBQhRzrEgsxQShmWMF3P@127.0.0.1:55355'); // connection string to local wallet


// Create Wallet Address - return via JSON to user
if(isset($_POST['createWallet'])) {
	
	$name = addslashes(strip_tags($_POST['name']));
	$email = addslashes(strip_tags($_POST['email']));
	$expectedBalance = addslashes(strip_tags($_POST['expectedBalance']));
	$paymentAddress = addslashes(strip_tags($_POST['paymentAddress']));
	
	try {
		$address = $coin->getnewaddress();
		//$address = "ASDcm4obzcxENCWXLMnJFcL3USMgSfc371"; for testing, creating tons of local wallets is not fun.
	} catch (Exception $e) {
		echo nl2br($e->getMessage()).'<br />'."\n";
	}
	
	// Create database logging the above fields somewhere?
	
	
	
$response = array('address' => $address);	
echo json_encode($response);

}


// Poll RPC for balance
if(isset($_POST['checkTransaction'])) {
	
// user provided variables
	$address = addslashes(strip_tags($_POST['address'])); // get provided address
	$expectedBalance = addslashes(strip_tags($_POST['expectedBalance'])); // get expected balance
	$paymentAddress = addslashes(strip_tags($_POST['paymentAddress'])); // get payment address


// system defined variables
	$confirmed = 0; // 0 indicates nothing received, 1 equals valid receipt, -1 indicates too much or too little sent


// check if confirmed balance equals expected balance
	$confirmedBalance = $coin->getreceivedbyaddress($address, 4);
	if ($confirmedBalance == $expectedBalance) $confirmed = 1;
	if ($confirmedBalance < $expectedBalance || $confirmedBalance > $expectedBalance) $confirmed = -1;
	if ($confirmedBalance == 0) $confirmed = 0;
	
	
// proceed with coin transfer if confirmedBalance == expectedBalance
	if ($confirmed == 1) {
		
		$txId = $coin->sendtoaddress($paymentAddress, $confirmedBalance);
		
		// move balance from system wallet to a different wallet to avoid abuse
		
	}

$response = array('address' => $address, 'expectedBalance' => $expectedBalance, 'confirmedBalance' => $confirmedBalance, 'confirmed' => $confirmed);
echo json_encode($response);

}

?>