<?php
/**
 * Defines countdown timer
 * Scripts included in PHP because need to access PHP data into JS code and wordpress localize (and other) hooks cannot used
 */

// Exit if file accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

$html = new OCUC_Render_Views();
$uc_option = self::get_uc_option();
$uc_time = isset($uc_option['uc_timer']) ? $uc_option['uc_timer'] : '';

ob_start();
?>

<script>
    // Set the date we're counting down to
    var endDate = "<?php echo date('F j, Y H:i:s', strtotime($uc_time)); ?>";
    var countDownDate = new Date(endDate).getTime();

    // Update the countdown every 1 second
    var x = setInterval(function() {

        // Get today's date and time
        var now = new Date().getTime();

        // Find the distance between now and the countdown date
        var distance = countDownDate - now;

        // Time calculations for days, hours, minutes and seconds
        var days = Math.floor(distance / (1000 * 60 * 60 * 24));
        var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        var seconds = Math.floor((distance % (1000 * 60)) / 1000);

        // Show timer is countdown is running
        if (distance > 0) {
            document.getElementById("counter-day").innerHTML = days;
            document.getElementById("counter-hour").innerHTML = hours;
            document.getElementById("counter-minute").innerHTML = minutes;
            document.getElementById("counter-second").innerHTML = seconds;
        }
        // If the countdown is finished, set it 00
        else {
            clearInterval(x);
            document.getElementById("counter-day").innerHTML = "00";
            document.getElementById("counter-hour").innerHTML = "00";
            document.getElementById("counter-minute").innerHTML = "00";
            document.getElementById("counter-second").innerHTML = "00";
        }
    }, 1000);
</script>
<?php
$html = ob_get_clean();
