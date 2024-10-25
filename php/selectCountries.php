<?php
$selectCountries = mysqli_query($conn, "SELECT * FROM countries WHERE CountryStatus=1 order by CountryName DESC");
if ($selectCountries->num_rows > 0) {
    while ($getCountries = mysqli_fetch_assoc($selectCountries)) {
        ?>
        <option value="<?php echo $getCountries['countryId'] ?>"><?php echo $getCountries['CountryName'] ?></option>
        <?php
    }
}
?>