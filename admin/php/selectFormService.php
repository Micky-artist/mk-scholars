<?php
$selectServices = mysqli_query($conn, "SELECT * FROM services WHERE servicestatus=1 order by servicename DESC");
if ($selectServices->num_rows > 0) {
    while ($getService = mysqli_fetch_assoc($selectServices)) {
        ?>
        <option value="<?php echo $getService['serviceId'] ?>"><?php echo $getService['servicename'] ?></option>
        <?php
    }
}
?>