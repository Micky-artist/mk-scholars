
<form class="FinalForm" method="POST" action="https://checkout.flutterwave.com/v3/hosted/pay">
    <input type="hidden" name="public_key" value="FLWPUBK-fd9a72fe52fbf0bd373323b44d7e2097-X" />
    <input type="hidden" name="customizations[title]" value="Rwanda Driver Code" />
    <input type="hidden" name="customizations[description]" value="Pass Easily" />
    <input type="hidden" name="customizations[logo]" value="https://rwandadrivercode.com/media/logo1.png" />
    <input type="hidden" name="customer[email]" value="<?php echo $customer_email; ?>" />
    <input type="hidden" name="customer[name]" value="<?php echo $customer_name; ?>" />
    <input type="hidden" name="tx_ref" value="<?php echo $transaction_id; ?>" />
    <input type="hidden" name="amount" value="<?php echo $finalAmount; ?>" />
    <input type="hidden" name="subType" value="<?php echo $type; ?>"/>
    <input type="hidden" name="currency" value="RWF" />
    <input type="hidden" name="redirect_url" value="https://rwandadrivercode.com/pages/php/TransactionCompleted.php?type=<?php echo $type; ?>" />
    <div class="PayButton">
        <button type="submit" class="buyBtn" id="start-payment-button">Pay Now</button>
        <!-- <a><button name="payNow" >Buy</button></a> -->
    </div>
</form>
<script src="../js/preventFormResubmition.js"></script>
