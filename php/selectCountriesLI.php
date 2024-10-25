<?php
$selectCountries = mysqli_query($conn, "SELECT DISTINCT(s.country), c.CountryName, c.countryId FROM countries c INNER JOIN scholarships s ON s.country = c.countryId WHERE s.scholarshipStatus !=0 order by c.CountryName DESC");
if ($selectCountries->num_rows > 0) {
    while ($getCountries = mysqli_fetch_assoc($selectCountries)) {
        ?>
        <li><a href="?i=<?php echo $getCountries['countryId'] ?>&Country_name=<?php echo $getCountries['CountryName'] ?>" class="tran3s"><?php echo $getCountries['CountryName'] ?></a></li>
        <?php
    }
}
?>