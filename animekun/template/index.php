<?php include('../template/header.php'); ?>

<div class="wrapper">
    <?php include('../template/menu_top.php'); ?>

    <div class="container">

    </div>
</div>

<?php
$json = array(
    'open_iv'  => bin2hex($open_iv),
    'open_key' => bin2hex($open_key),
    'open_time'=> $open_time,
);
$json = json_encode($json);
$encrypted = openssl_encrypt($json, 'aes-256-cbc', hex2bin($closed_login_key), $options=OPENSSL_RAW_DATA, $closed_login_iv);
$closed_hash = hash_hmac('sha3-512', $encrypted, hex2bin($closed_login_hash_key), true);
$encrypted_json = $closed_login_iv . $closed_hash . $encrypted;
?>

<form method="POST" id="user_login">
    <label for="user_name">Username:</label>
    <input type="text" name="user_name" value="toxic" />
    <label for="user_pass">Password:</label>
    <input type="password" name="user_pass" value="123" />

    <input type="hidden" name="form_url"  value="<?php echo url_api('users', array('microservice' => 'user', 'module' => 'login')); ?>" />
    <input type="hidden" name="open_iv"   value="<?php echo bin2hex($open_iv);   ?>" />
    <input type="hidden" name="open_key"  value="<?php echo bin2hex($open_key);  ?>" />
    <input type="hidden" name="open_time" value="<?php echo $open_time; ?>" />
    <input type="submit" value="Login" />
</form>

<script type="text/javascript">
const user_login_form = document.getElementById('user_login');

user_login_form.addEventListener('submit', (event) => {
    event.preventDefault();

    var form_url = user_login_form.elements['form_url'].value;

    var post_array = {
        'user_name': user_login_form.elements['user_name'].value,
        'user_pass': user_login_form.elements['user_pass'].value,
    };

    var post_data      = JSON.stringify(post_array);

    // Encrypt
    var open_iv  = CryptoJS.enc.Hex.parse(user_login_form.elements['open_iv'].value);
    var open_key = CryptoJS.enc.Hex.parse(user_login_form.elements['open_key'].value);
    var post_encrypted_hex = CryptoJS.AES.encrypt(post_data, open_key, {iv:open_iv}).ciphertext.toString();
    var hash = CryptoJS.SHA512(post_encrypted_hex);

    // Request
    var xhttp = new XMLHttpRequest();
    xhttp.open("POST", form_url, true); 
    xhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded; charset=UTF-8");
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            alert(this.responseText);
        }
    };
    xhttp.send('a=0&b=' + hash + post_encrypted_hex + '&c=' + "<?php echo bin2hex($encrypted_json); ?>");
});
</script>

<?php include('../template/footer.php'); ?>