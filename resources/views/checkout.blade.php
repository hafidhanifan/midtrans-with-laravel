<!doctype html>
<html>

<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body>


    <h1>Checkout</h1>
    <input id="amount" type="number" value="100000">
    <button id="pay-button">Bayar Sekarang</button>


    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}">
    </script>


    <script>
        document.getElementById('pay-button').addEventListener('click', function(){
const amount = document.getElementById('amount').value;


fetch('/payment/create', {
method: 'POST',
headers: {
'Content-Type': 'application/json',
'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
},
body: JSON.stringify({ amount })
})
.then(r => r.json())
.then(data => {
window.snap.pay(data.token, {
onSuccess: function(result){
alert('Payment success — server masih menunggu notification untuk final status.');
},
onPending: function(result){
alert('Payment pending — silakan selesaikan pembayaran.');
},
onError: function(result){
alert('Payment error');
},
onClose: function(){
alert('Popup ditutup tanpa menyelesaikan pembayaran');
}
});
});
});
    </script>
</body>

</html>